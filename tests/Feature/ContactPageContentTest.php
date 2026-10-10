<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactPageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_editor_requires_an_authenticated_administrator(): void
    {
        $this->get('/admin/pages/contact')->assertRedirect('/admin/login');

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get('/admin/pages/contact')->assertForbidden();
    }

    public function test_administrator_can_update_contact_page_copy(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->get('/admin/pages/contact')
            ->assertOk()
            ->assertSee('Hero content')
            ->assertSee('Supporting text')
            ->assertSee('Enquiry fields stay unchanged.');

        $this->put('/admin/pages/contact', [
            'title' => 'Let us plan your Wilpattu journey.',
            'kicker' => 'CONTACT OUR TEAM',
            'intro' => 'Share your travel plans and our reservations team will help shape the details.',
            'hero_image' => 11,
            'settings' => [
                'eyebrow' => 'START YOUR JOURNEY',
                'heading' => "Tell us what\nyou imagine.",
                'supporting_text' => 'Share your dates, interests and special requests with our team.',
                'reservations_label' => 'WILPATTU RESERVATIONS',
                'availability_note' => 'All stays and experiences remain subject to availability.',
                'submission_note' => 'Your enquiry is the first step; confirmation follows separately.',
            ],
        ])->assertRedirect('/admin/pages/contact')->assertSessionHas('status');

        $this->assertDatabaseHas('page_contents', [
            'slug' => 'contact',
            'title' => 'Let us plan your Wilpattu journey.',
            'hero_image' => 11,
        ]);

        $this->get('/contact')
            ->assertOk()
            ->assertSee('Let us plan your Wilpattu journey.')
            ->assertSee('START YOUR JOURNEY')
            ->assertSee('Tell us what')
            ->assertSee('you imagine.')
            ->assertSee('WILPATTU RESERVATIONS')
            ->assertSee('All stays and experiences remain subject to availability.')
            ->assertSee('Your enquiry is the first step; confirmation follows separately.');
    }

    public function test_contact_form_fields_and_submission_behavior_remain_unchanged(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/contact/items', [])->assertNotFound();
        $this->delete('/admin/pages/contact/items/1')->assertNotFound();

        $this->get('/contact')
            ->assertSee('name="name"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="category"', false)
            ->assertSee('name="message"', false)
            ->assertSee('name="consent"', false)
            ->assertSee('action="'.route('enquire').'"', false);
    }

    public function test_contact_copy_requires_all_supported_text_fields(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->put('/admin/pages/contact', [
            'title' => 'Contact',
            'kicker' => 'CONTACT',
            'intro' => 'Contact introduction.',
            'hero_image' => 6,
            'settings' => [],
        ])->assertSessionHasErrors([
            'settings.eyebrow',
            'settings.heading',
            'settings.supporting_text',
            'settings.reservations_label',
            'settings.availability_note',
            'settings.submission_note',
        ]);
    }

    private function administrator(): User
    {
        return User::query()->where('username', 'admin')->firstOrFail();
    }
}
