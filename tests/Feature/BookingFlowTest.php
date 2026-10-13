<?php

namespace Tests\Feature;

use App\BookingAvailability;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_shows_available_counts_and_nationality_rates(): void
    {
        $room = $this->room();
        $this->get(route('booking.search', $this->search()))->assertOk()->assertSee('3 available for your dates')->assertSee('10,000.00');
        $this->get(route('booking.search', array_replace($this->search(), ['nationality' => 'International'])))->assertOk()->assertSee('15,000.00');
        $this->assertSame(3, app(BookingAvailability::class)->remaining(DB::table('room_types')->find($room), '2026-11-01', '2026-11-03'));
    }

    public function test_guest_can_select_multiple_rooms_and_confirm_without_duplicate_bookings(): void
    {
        $room = $this->room();
        $payload = $this->checkout([$room => 2]);
        $confirmation = $this->post(route('booking.store'), $payload)->assertRedirect()->headers->get('Location');
        $this->get($confirmation)->assertOk()->assertSee('The wild is waiting.')->assertSee('40,000.00');
        $this->post(route('booking.store'), $payload)->assertRedirect($confirmation);
        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseHas('bookings', ['name' => 'Test Guest', 'total' => 40000, 'status' => 'confirmed']);
        $this->assertDatabaseHas('booking_items', ['room_type_id' => $room, 'quantity' => 2, 'subtotal' => 40000]);
        $this->get(route('booking.search', $this->search()))->assertSee('1 available for your dates');
    }

    public function test_zero_quantities_do_not_break_checkout(): void
    {
        $room = $this->room();
        $other = $this->room(['name' => 'Family room']);
        $this->post(route('booking.store'), $this->checkout([$room => 1, $other => 0]))->assertRedirect();
        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseCount('booking_items', 1);
    }

    public function test_availability_uses_peak_nightly_occupancy_and_allows_same_day_turnover(): void
    {
        $room = $this->room(['inventory' => 2]);
        $this->post(route('booking.store'), $this->checkout([$room => 1], ['departure' => '2026-11-02']))->assertRedirect();
        $this->post(route('booking.store'), $this->checkout([$room => 1], ['arrival' => '2026-11-02']))->assertRedirect();
        $inventory = DB::table('room_types')->find($room);
        $availability = app(BookingAvailability::class);
        $this->assertSame(1, $availability->remaining($inventory, '2026-11-01', '2026-11-03'));
        $this->assertSame(2, $availability->remaining($inventory, '2026-11-03', '2026-11-05'));
    }

    public function test_availability_is_rechecked_when_another_guest_takes_the_last_room(): void
    {
        $room = $this->room(['inventory' => 1]);
        $first = $this->checkout([$room => 1]);
        $second = $this->checkout([$room => 1]);
        $this->post(route('booking.store'), $first)->assertRedirect();
        $this->post(route('booking.store'), $second)->assertSessionHasErrors('rooms');
        $this->assertDatabaseCount('bookings', 1);
    }

    public function test_rate_changes_require_a_new_quote(): void
    {
        $room = $this->room();
        $payload = $this->checkout([$room => 1]);
        DB::table('room_types')->where('id', $room)->update(['local_rate' => 12000]);
        $this->post(route('booking.store'), $payload)->assertSessionHasErrors('rooms');
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_rejects_empty_selection_insufficient_capacity_and_unknown_rooms(): void
    {
        $room = $this->room(['capacity' => 2]);
        $this->post(route('booking.checkout'), $this->search() + ['rooms' => [$room => 0]])->assertSessionHasErrors('rooms');
        $this->post(route('booking.checkout'), array_replace($this->search(), ['adults' => 3, 'rooms' => [$room => 1]]))->assertSessionHasErrors('rooms');
        $this->post(route('booking.checkout'), $this->search() + ['rooms' => [999 => 1]])->assertSessionHasErrors('rooms');
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_rejects_invalid_dates_guest_counts_property_and_unsupported_promos(): void
    {
        $this->get(route('booking.search', array_replace($this->search(), ['arrival' => '2026-01-01', 'departure' => '2026-01-01', 'adults' => 0, 'property' => 'unknown', 'promo_code' => 'FAKE'])))
            ->assertSessionHasErrors(['arrival', 'departure', 'adults', 'property', 'promo_code']);
        $this->get(route('booking.search', array_replace($this->search(), ['departure' => '2026-12-20'])))->assertSessionHasErrors('departure');
        $this->get(route('booking.search', array_replace($this->search(), ['arrival' => 'invalid'])))->assertSessionHasErrors('arrival');
    }

    public function test_guest_details_validation_returns_to_a_reloadable_review(): void
    {
        $room = $this->room();
        $payload = $this->checkout([$room => 1]);
        $review = route('booking.review', $payload['booking_token']);
        $this->from($review)->post(route('booking.store'), array_replace($payload, ['email' => 'invalid', 'consent' => 0]))->assertRedirect($review)->assertSessionHasErrors(['email', 'consent']);
        $this->get($review)->assertOk()->assertSee('value="invalid"', false);
    }

    public function test_confirmation_and_review_are_private_to_the_booking_session(): void
    {
        $room = $this->room();
        $payload = $this->checkout([$room => 1]);
        $url = $this->post(route('booking.store'), $payload)->headers->get('Location');
        $this->flushSession();
        $this->get($url)->assertNotFound();
        $this->get(route('booking.review', $payload['booking_token']))->assertNotFound();
    }

    public function test_guest_cannot_skip_quote_or_tamper_with_room_quantities(): void
    {
        $room = $this->room();
        $payload = $this->checkout([$room => 1]);
        $this->post(route('booking.store'), array_replace($payload, ['rooms' => [$room => 2]]))->assertSessionHasErrors('rooms');
        $this->flushSession();
        $this->post(route('booking.store'), $payload)->assertSessionHasErrors('rooms');
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_administrator_can_view_and_cancel_booking_releasing_inventory(): void
    {
        $room = $this->room();
        $this->post(route('booking.store'), $this->checkout([$room => 2]))->assertRedirect();
        $booking = DB::table('bookings')->first();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $this->get(route('admin.bookings.index'))->assertOk()->assertSee($booking->reference);
        $this->get(route('admin.bookings.show', $booking->id))->assertOk()->assertSee('Test Guest')->assertSee('2 ×');
        $this->put(route('admin.bookings.cancel', $booking->id))->assertSessionHas('status');
        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'cancelled']);
        $this->assertSame(3, app(BookingAvailability::class)->remaining(DB::table('room_types')->find($room), '2026-11-01', '2026-11-03'));
    }

    public function test_admin_booking_routes_are_protected(): void
    {
        $this->get(route('admin.bookings.index'))->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create(['is_admin' => false]));
        $this->get(route('admin.bookings.index'))->assertForbidden();
        $this->get(route('admin.bookings.inventory'))->assertForbidden();
        $this->put(route('admin.bookings.cancel', 1))->assertForbidden();
        $this->post(route('admin.bookings.rooms.store'), [])->assertForbidden();
    }

    public function test_admin_can_create_update_and_hide_inventory(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $data = ['name' => 'Forest Villa', 'description' => 'Private forest retreat', 'inventory' => 4, 'capacity' => 2, 'local_rate' => 9000, 'international_rate' => 14000, 'is_active' => 1];
        $this->post(route('admin.bookings.rooms.store'), $data)->assertRedirect(route('admin.bookings.inventory'));
        $room = DB::table('room_types')->first();
        $this->get(route('admin.bookings.inventory'))->assertOk()->assertSee('Forest Villa');
        $this->put(route('admin.bookings.rooms.update', $room->id), array_replace($data, ['is_active' => 0]))->assertRedirect();
        $this->get(route('booking.search', $this->search()))->assertDontSee('Forest Villa');
    }

    public function test_inventory_cannot_drop_below_existing_reservations(): void
    {
        $room = $this->room();
        $this->post(route('booking.store'), $this->checkout([$room => 2]))->assertRedirect();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $data = (array) DB::table('room_types')->find($room);
        $this->put(route('admin.bookings.rooms.update', $room), array_replace($data, ['inventory' => 1]))->assertSessionHasErrors('inventory');
        $this->assertDatabaseHas('room_types', ['id' => $room, 'inventory' => 3]);
    }

    public function test_empty_inventory_has_a_helpful_empty_state(): void
    {
        $this->get(route('booking.create'))->assertOk()->assertSee('Your next adventure starts here.');
        $this->get(route('booking.search', $this->search()))->assertOk()->assertSee('Stays are being prepared.');
    }

    private function room(array $overrides = []): int
    {
        return DB::table('room_types')->insertGetId(array_replace([
            'name' => 'Forest Villa', 'description' => 'A private room close to the wild.',
            'inventory' => 3, 'capacity' => 2, 'local_rate' => 10000,
            'international_rate' => 15000, 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
        ], $overrides));
    }

    private function search(): array
    {
        $this->travelTo(Carbon::parse('2026-10-13 12:00:00', 'Asia/Colombo'));

        return ['property' => 'makara', 'arrival' => '2026-11-01', 'departure' => '2026-11-03', 'nationality' => 'Sri Lankan', 'adults' => 2, 'children' => 0];
    }

    private function checkout(array $rooms, array $overrides = []): array
    {
        $search = array_replace($this->search(), $overrides);
        $review = $this->post(route('booking.checkout'), $search + ['rooms' => $rooms])->assertRedirect()->headers->get('Location');
        $token = basename($review);
        $this->get($review)->assertOk();
        $selected = array_filter($rooms, fn (int $quantity): bool => $quantity > 0);

        return $search + ['rooms' => $selected, 'booking_token' => $token, 'name' => 'Test Guest', 'email' => 'guest@example.com', 'phone' => '+94771234567', 'consent' => 1];
    }
}
