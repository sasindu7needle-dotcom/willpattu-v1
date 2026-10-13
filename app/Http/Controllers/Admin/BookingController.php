<?php

namespace App\Http\Controllers\Admin;

use App\BookingAvailability;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveRoomTypeRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate(['status' => ['nullable', Rule::in(['confirmed', 'cancelled'])], 'search' => ['nullable', 'string', 'max:120']]);
        $query = DB::table('bookings');
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['search'])) {
            $query->where(function ($query) use ($filters): void {
                $query->where('reference', 'like', '%'.$filters['search'].'%')->orWhere('name', 'like', '%'.$filters['search'].'%')->orWhere('email', 'like', '%'.$filters['search'].'%');
            });
        }

        return view('admin.bookings.index', [
            'bookings' => $query->latest('id')->paginate(20)->withQueryString(),
            'confirmedCount' => DB::table('bookings')->where('status', 'confirmed')->count(),
            'upcomingCount' => DB::table('bookings')->where('status', 'confirmed')->where('arrival', '>=', now('Asia/Colombo')->toDateString())->count(),
        ]);
    }

    public function show(int $booking): View
    {
        $reservation = DB::table('bookings')->find($booking);
        abort_unless($reservation, 404);

        return view('admin.bookings.show', ['booking' => $reservation, 'items' => DB::table('booking_items')->where('booking_id', $booking)->get()]);
    }

    public function cancel(int $booking): RedirectResponse
    {
        DB::transaction(function () use ($booking): void {
            $ids = DB::table('booking_items')->where('booking_id', $booking)->pluck('room_type_id');
            app(BookingAvailability::class)->lockRooms($ids->all());
            abort_unless(DB::table('bookings')->where('id', $booking)->exists(), 404);
            DB::table('bookings')->where('id', $booking)->where('status', 'confirmed')->update(['status' => 'cancelled', 'updated_at' => now()]);
        });

        return back()->with('status', 'Booking cancelled. Rooms are available for these dates again.');
    }

    public function inventory(): View
    {
        return view('admin.bookings.inventory', ['rooms' => DB::table('room_types')->orderBy('id')->get()]);
    }

    public function saveRoom(SaveRoomTypeRequest $request, ?int $room = null): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($room, $data): void {
            if ($room) {
                app(BookingAvailability::class)->lockRooms([$room]);
                abort_unless(DB::table('room_types')->where('id', $room)->lockForUpdate()->first(), 404);
                $reservations = DB::table('booking_items')->join('bookings', 'bookings.id', '=', 'booking_items.booking_id')
                    ->where('room_type_id', $room)->where('status', 'confirmed')->where('departure', '>', now('Asia/Colombo')->toDateString())
                    ->get(['arrival', 'departure', 'quantity']);
                $events = [];
                foreach ($reservations as $reservation) {
                    $events[$reservation->arrival] = ($events[$reservation->arrival] ?? 0) + $reservation->quantity;
                    $events[$reservation->departure] = ($events[$reservation->departure] ?? 0) - $reservation->quantity;
                }
                ksort($events);
                $occupied = 0;
                $maximum = 0;
                foreach ($events as $change) {
                    $occupied += $change;
                    $maximum = max($maximum, $occupied);
                }
                if ($data['inventory'] < $maximum) {
                    throw ValidationException::withMessages(['inventory' => 'Inventory cannot be lower than '.$maximum.' rooms already reserved on upcoming dates.']);
                }
                DB::table('room_types')->where('id', $room)->update(array_merge($data, ['updated_at' => now()]));
            } else {
                DB::table('room_types')->insert(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));
            }
        });

        return to_route('admin.bookings.inventory')->with('status', 'Room inventory saved.');
    }
}
