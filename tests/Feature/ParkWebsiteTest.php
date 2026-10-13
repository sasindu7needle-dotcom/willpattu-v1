<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ParkWebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_requested_pages_and_articles_render_without_ai_planner(): void
    {
        $this->get('/')->assertOk()->assertSee('Discover')->assertDontSee('AI Safari Planner');
        foreach (config('content.pages') as $slug => $page) {
            $this->get('/'.$slug)->assertOk()->assertSee($page['title']);
        }
        foreach (config('content.articles') as $slug => $page) {
            $this->get('/journal/'.$slug)->assertOk()->assertSee($page['title']);
        }
        $this->get('/ai-safari-planner')->assertNotFound();
        $this->get('/unknown-page')->assertNotFound();
    }

    public function test_newsletter_signup_is_saved_once(): void
    {
        $this->from('/')->post(route('newsletter.subscribe'), [
            'newsletter_email' => 'Guest@example.com',
        ])->assertRedirect(url('/').'#newsletter')->assertSessionHas('newsletter_success');

        $this->from('/')->post(route('newsletter.subscribe'), [
            'newsletter_email' => 'guest@example.com',
        ])->assertSessionHas('newsletter_success');

        $this->assertDatabaseHas('newsletter_subscriptions', ['email' => 'guest@example.com']);
        $this->assertDatabaseCount('newsletter_subscriptions', 1);
    }

    public function test_invalid_newsletter_email_is_rejected_separately_from_contact_form(): void
    {
        $this->from('/')->post(route('newsletter.subscribe'), [
            'newsletter_email' => 'invalid-email',
        ])->assertSessionHasErrors(['newsletter_email'], errorBag: 'newsletter');

        $this->assertDatabaseCount('newsletter_subscriptions', 0);
    }

    private function enquiry(): array
    {
        return ['name' => 'Test Traveller', 'email' => 'traveller@example.com', 'category' => 'Reservations', 'arrival' => now()->addDays(10)->toDateString(), 'departure' => now()->addDays(12)->toDateString(), 'travellers' => 2, 'message' => 'Please send a quotation for a private safari.', 'consent' => '1'];
    }

    public function test_valid_enquiry_is_saved_with_a_reference(): void
    {
        $response = $this->post('/contact', $this->enquiry());
        $response->assertRedirect('/contact')->assertSessionHas('success');
        $this->assertDatabaseHas('enquiries', ['email' => 'traveller@example.com', 'status' => 'new', 'category' => 'Reservations']);
        $this->assertMatchesRegularExpression('/^WP-[A-Z0-9]{10}$/', DB::table('enquiries')->first()->reference);
    }

    public function test_invalid_dates_and_missing_consent_are_rejected(): void
    {
        $data = $this->enquiry();
        $data['departure'] = $data['arrival'];
        unset($data['consent']);
        $this->post('/contact', $data)->assertSessionHasErrors(['departure', 'consent']);
        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_contact_form_accepts_an_enquiry_without_booking_details(): void
    {
        $this->get('/contact')->assertOk()
            ->assertDontSee('name="category"', false)
            ->assertDontSee('name="arrival"', false)
            ->assertDontSee('name="departure"', false)
            ->assertDontSee('name="travellers"', false)
            ->assertDontSee('name="interest"', false);

        $this->post('/contact', [
            'name' => 'Test Guest',
            'email' => 'guest@example.com',
            'message' => 'Please tell me more about your safari experiences.',
            'consent' => '1',
        ])->assertRedirect('/contact')->assertSessionHasNoErrors()->assertSessionHas('success');

        $this->assertDatabaseHas('enquiries', [
            'email' => 'guest@example.com',
            'category' => 'General Enquiries',
        ]);
    }

    public function test_honeypot_and_unknown_category_are_rejected(): void
    {
        $data = $this->enquiry();
        $data['website'] = 'spam.example';
        $data['category'] = 'invalid';
        $this->post('/contact', $data)->assertSessionHasErrors(['website', 'category']);
        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_sixth_enquiry_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/contact', [])->assertRedirect();
        }
        $this->post('/contact', [])->assertStatus(429);
    }
}
