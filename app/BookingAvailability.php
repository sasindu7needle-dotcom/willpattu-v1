<?php

namespace App;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookingAvailability
{
    /** @param array<int, int|string> $roomIds */
    public function lockRooms(array $roomIds): void
    {
        $query = DB::table('room_types')->whereIn('id', $roomIds)->orderBy('id');
        if (DB::connection()->getDriverName() === 'sqlite') {
            $query->update(['inventory' => DB::raw('inventory')]);
        }
        $query->lockForUpdate()->get();
    }

    public function rooms(string $arrival, string $departure, string $nationality): Collection
    {
        return DB::table('room_types')->where('is_active', true)->orderBy('id')->get()->map(function (object $room) use ($arrival, $departure, $nationality): object {
            $room->available = $this->remaining($room, $arrival, $departure);
            $room->nightly_rate = $nationality === 'Sri Lankan' ? $room->local_rate : $room->international_rate;

            return $room;
        });
    }

    public function remaining(object $room, string $arrival, string $departure): int
    {
        $reservations = DB::table('booking_items')->join('bookings', 'bookings.id', '=', 'booking_items.booking_id')
            ->where('booking_items.room_type_id', $room->id)->where('bookings.status', 'confirmed')
            ->where('bookings.arrival', '<', $departure)->where('bookings.departure', '>', $arrival)
            ->get(['bookings.arrival', 'bookings.departure', 'booking_items.quantity']);
        $maximumOccupied = 0;

        for ($day = CarbonImmutable::parse($arrival); $day->lt(CarbonImmutable::parse($departure)); $day = $day->addDay()) {
            $date = $day->toDateString();
            $occupied = $reservations->filter(fn (object $booking): bool => $booking->arrival <= $date && $booking->departure > $date)->sum('quantity');
            $maximumOccupied = max($maximumOccupied, $occupied);
        }

        return max(0, $room->inventory - $maximumOccupied);
    }
}
