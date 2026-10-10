<?php

namespace Tests\Feature;

use App\Models\PageContentItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfferPageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_offers_editor_requires_an_authenticated_administrator(): void
    {
        $this->get('/admin/pages/offers')->assertRedirect('/admin/login');

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get('/admin/pages/offers')->assertForbidden();
    }

    public function test_administrator_can_update_offers_page_details(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->get('/admin/pages/offers')
            ->assertOk()
            ->assertSee('Hero content')
            ->assertSee('Offers')
            ->assertSee('data-confirm-dialog', false);

        $this->put('/admin/pages/offers', [
            'title' => 'An unforgettable escape, thoughtfully arranged.',
            'kicker' => 'WILPATTU OFFERS',
            'intro' => 'Explore curated stays and safari combinations designed around your journey.',
            'hero_image' => 15,
        ])->assertRedirect('/admin/pages/offers')->assertSessionHas('status');

        $this->assertDatabaseHas('page_contents', [
            'slug' => 'offers',
            'title' => 'An unforgettable escape, thoughtfully arranged.',
            'hero_image' => 15,
        ]);

        $this->get('/offers')
            ->assertOk()
            ->assertSee('An unforgettable escape, thoughtfully arranged.')
            ->assertSee('Explore curated stays and safari combinations');
    }

    public function test_administrator_can_add_edit_and_delete_offers(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/offers/items', [
            'title' => 'Three-night wilderness escape',
            'meta' => 'Three nights · Quotation on request',
            'text' => 'Combine a relaxed stay with guided morning and evening safari drives.',
            'image' => 6,
        ])->assertRedirect('/admin/pages/offers')->assertSessionHas('status');

        $item = PageContentItem::query()->where('title', 'Three-night wilderness escape')->firstOrFail();
        $this->get('/offers')->assertSee('Three-night wilderness escape');

        $this->put('/admin/pages/offers/items/'.$item->id, [
            'title' => 'Four-night wilderness escape',
            'meta' => 'Four nights · Tailored quotation',
            'text' => 'An updated longer escape with private safari and dining arrangements.',
            'image' => 13,
        ])->assertRedirect('/admin/pages/offers')->assertSessionHas('status');

        $this->assertDatabaseHas('page_content_items', [
            'id' => $item->id,
            'title' => 'Four-night wilderness escape',
            'image' => 13,
        ]);
        $this->get('/offers')->assertSee('Four-night wilderness escape')->assertDontSee('Three-night wilderness escape');

        $this->delete('/admin/pages/offers/items/'.$item->id)
            ->assertRedirect('/admin/pages/offers')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('page_content_items', ['id' => $item->id]);
        $this->get('/offers')->assertDontSee('Four-night wilderness escape');
    }

    public function test_offer_content_rejects_unknown_image_numbers(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/offers/items', [
            'title' => 'Invalid offer image',
            'meta' => 'Test content',
            'text' => 'This offer should not be saved.',
            'image' => 4,
        ])->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('page_content_items', ['title' => 'Invalid offer image']);
    }

    private function administrator(): User
    {
        return User::query()->where('username', 'admin')->firstOrFail();
    }
}
