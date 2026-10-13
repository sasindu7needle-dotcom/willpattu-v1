<?php

namespace App\Http\Controllers;

use App\BookingAvailability;
use App\Http\Requests\SearchAvailabilityRequest;
use App\Http\Requests\StoreBookingRequest;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function create(): View
    {
        return view('booking.search', ['rooms' => null, 'search' => [], 'nights' => 0]);
    }

    public function search(SearchAvailabilityRequest $request, BookingAvailability $availability): View
    {
        $search = $request->validated();
        $nights = (int) CarbonImmutable::parse($search['arrival'])->diffInDays(CarbonImmutable::parse($search['departure']));

        return view('booking.search', [
            'search' => $search,
            'nights' => $nights,
            'rooms' => $availability->rooms($search['arrival'], $search['departure'], $search['nationality']),
        ]);
    }

    public function checkout(SearchAvailabilityRequest $request, BookingAvailability $availability): RedirectResponse
    {
        $request->validate(['rooms' => ['required', 'array', 'max:30'], 'rooms.*' => ['required', 'integer', 'min:0', 'max:50']]);
        $search = $request->validated();
        $selection = $this->selection($search, $request->input('rooms'), $availability);
        $token = (string) Str::uuid();
        $request->session()->put('booking_quotes.'.$token, [
            'search' => $search,
            'quantities' => $selection['selectedRooms']->mapWithKeys(fn (object $room): array => [$room->id => $room->quantity])->all(),
            'total' => $selection['total'],
        ]);

        return to_route('booking.review', ['token' => $token]);
    }

    public function review(Request $request, BookingAvailability $availability, string $token): View|RedirectResponse
    {
        $quote = $request->session()->get('booking_quotes.'.$token);
        abort_unless($quote, 404);
        $search = $quote['search'];
        try {
            $selection = $this->selection($search, $quote['quantities'], $availability);
        } catch (ValidationException $exception) {
            return to_route('booking.search', $search)->withErrors($exception->errors());
        }
        if ($selection['total'] !== $quote['total']) {
            return to_route('booking.search', $search)->withErrors(['rooms' => 'The rate has changed. Please review the updated price and choose your rooms again.']);
        }

        return view('booking.checkout', array_merge($selection, compact('search', 'token')));
    }

    public function store(StoreBookingRequest $request, BookingAvailability $availability): RedirectResponse
    {
        $data = $request->validated();
        $quote = $request->session()->get('booking_quotes.'.$data['booking_token']);
        if (! $quote || $quote['quantities'] != $data['rooms'] || collect($quote['search'])->except('promo_code')->all() != collect($data)->only(array_keys($quote['search']))->except('promo_code')->all()) {
            throw ValidationException::withMessages(['rooms' => 'Please check availability and review your room selection again.']);
        }

        $booking = DB::transaction(function () use ($data, $availability, $quote): object {
            $roomIds = array_keys(array_filter($data['rooms'], fn (mixed $quantity): bool => (int) $quantity > 0));
            $availability->lockRooms($roomIds);
            $existing = DB::table('bookings')->where('booking_token', $data['booking_token'])->first();
            if ($existing) {
                return $existing;
            }
            $selection = $this->selection($data, $data['rooms'], $availability);
            if ($selection['total'] !== $quote['total']) {
                throw ValidationException::withMessages(['rooms' => 'The rate has changed. Please check availability again to review the updated price.']);
            }
            $bookingId = DB::table('bookings')->insertGetId(array_merge(
                collect($data)->only(['property', 'arrival', 'departure', 'nationality', 'adults', 'children', 'name', 'email', 'phone', 'requests', 'booking_token'])->all(),
                ['reference' => 'WP-'.Str::upper(Str::random(12)), 'total' => $selection['total'] / 100, 'status' => 'confirmed', 'consented_at' => now(), 'created_at' => now(), 'updated_at' => now()],
            ));
            foreach ($selection['selectedRooms'] as $room) {
                DB::table('booking_items')->insert([
                    'booking_id' => $bookingId, 'room_type_id' => $room->id, 'room_name' => $room->name,
                    'quantity' => $room->quantity, 'nightly_rate' => $room->nightly_rate,
                    'subtotal' => $room->subtotal / 100,
                ]);
            }

            return DB::table('bookings')->find($bookingId);
        }, 3);

        return redirect()->to(route('booking.confirmation', ['reference' => $booking->reference]));
    }

    public function confirmation(Request $request, string $reference): View
    {
        $booking = DB::table('bookings')->where('reference', $reference)->first();
        abort_unless($booking && $request->session()->has('booking_quotes.'.$booking->booking_token), 404);

        return view('booking.confirmation', [
            'booking' => $booking,
            'items' => DB::table('booking_items')->where('booking_id', $booking->id)->get(),
        ]);
    }

    /** @return array{selectedRooms: Collection, nights: int, total: int, roomCount: int} */
    private function selection(array $search, array $quantities, BookingAvailability $availability): array
    {
        $availableRooms = $availability->rooms($search['arrival'], $search['departure'], $search['nationality'])->keyBy('id');
        $selectedRooms = collect();
        $nights = (int) CarbonImmutable::parse($search['arrival'])->diffInDays(CarbonImmutable::parse($search['departure']));
        foreach ($quantities as $roomId => $quantity) {
            if ((int) $quantity === 0) {
                continue;
            }
            $room = $availableRooms->get($roomId);
            if (! $room || (int) $quantity > $room->available) {
                throw ValidationException::withMessages(['rooms' => 'Your selected rooms are no longer available. Please check availability again.']);
            }
            $room->quantity = (int) $quantity;
            $room->subtotal = (int) round((float) $room->nightly_rate * 100) * $nights * $room->quantity;
            $selectedRooms->push($room);
        }
        if ($selectedRooms->isEmpty()) {
            throw ValidationException::withMessages(['rooms' => 'Select at least one room to continue.']);
        }
        $capacity = $selectedRooms->sum(fn (object $room): int => $room->capacity * $room->quantity);
        if ($capacity < (int) $search['adults'] + (int) $search['children']) {
            throw ValidationException::withMessages(['rooms' => 'Select enough rooms for every guest, including children.']);
        }

        return ['selectedRooms' => $selectedRooms, 'nights' => $nights, 'total' => $selectedRooms->sum('subtotal'), 'roomCount' => $selectedRooms->sum('quantity')];
    }
}
