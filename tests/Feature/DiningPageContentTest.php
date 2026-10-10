<?php

namespace Tests\Feature;

use App\Models\PageContentItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiningPageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_dining_editor_requires_an_authenticated_administrator(): void
    {
        $this->get('/admin/pages/dining')->assertRedirect('/admin/login');

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get('/admin/pages/dining')->assertForbidden();
    }

    public function test_administrator_can_update_dining_page_details(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->get('/admin/pages/dining')
            ->assertOk()
            ->assertSee('Hero content')
            ->assertSee('Dining sections')
            ->assertSee('data-confirm-dialog', false);

        $this->put('/admin/pages/dining', [
            'title' => 'Flavours inspired by the island.',
            'kicker' => 'WILPATTU DINING',
            'intro' => 'Gather around a table shaped by Sri Lankan ingredients and warm hospitality.',
            'hero_image' => 10,
        ])->assertRedirect('/admin/pages/dining')->assertSessionHas('status');

        $this->assertDatabaseHas('page_contents', [
            'slug' => 'dining',
            'title' => 'Flavours inspired by the island.',
            'hero_image' => 10,
        ]);

        $this->get('/dining')
            ->assertOk()
            ->assertSee('Flavours inspired by the island.')
            ->assertSee('Gather around a table shaped by Sri Lankan ingredients');
    }

    public function test_administrator_can_add_edit_and_delete_dining_sections(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/dining/items', [
            'title' => 'Lake-side breakfast',
            'meta' => 'Breakfast · By arrangement',
            'text' => 'Begin the day with a relaxed breakfast inspired by fresh local ingredients.',
            'image' => 12,
        ])->assertRedirect('/admin/pages/dining')->assertSessionHas('status');

        $item = PageContentItem::query()->where('title', 'Lake-side breakfast')->firstOrFail();
        $this->get('/dining')->assertSee('Lake-side breakfast');

        $this->put('/admin/pages/dining/items/'.$item->id, [
            'title' => 'Breakfast by the villu',
            'meta' => 'Private breakfast · Morning',
            'text' => 'An updated private breakfast experience overlooking the natural landscape.',
            'image' => 14,
        ])->assertRedirect('/admin/pages/dining')->assertSessionHas('status');

        $this->assertDatabaseHas('page_content_items', [
            'id' => $item->id,
            'title' => 'Breakfast by the villu',
            'image' => 14,
        ]);
        $this->get('/dining')->assertSee('Breakfast by the villu')->assertDontSee('Lake-side breakfast');

        $this->delete('/admin/pages/dining/items/'.$item->id)
            ->assertRedirect('/admin/pages/dining')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('page_content_items', ['id' => $item->id]);
        $this->get('/dining')->assertDontSee('Breakfast by the villu');
    }

    public function test_dining_content_rejects_unknown_image_numbers(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/dining/items', [
            'title' => 'Invalid dining image',
            'meta' => 'Test content',
            'text' => 'This dining section should not be saved.',
            'image' => 4,
        ])->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('page_content_items', ['title' => 'Invalid dining image']);
    }

    private function administrator(): User
    {
        return User::query()->where('username', 'admin')->firstOrFail();
    }
}
