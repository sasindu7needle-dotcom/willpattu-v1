<?php

namespace Tests\Feature;

use App\Models\PageContentItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WildlifePageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_wildlife_editor_requires_an_authenticated_administrator(): void
    {
        $this->get('/admin/pages/wildlife')->assertRedirect('/admin/login');

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get('/admin/pages/wildlife')->assertForbidden();
    }

    public function test_administrator_can_update_wildlife_page_details(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->get('/admin/pages/wildlife')
            ->assertOk()
            ->assertSee('Hero content')
            ->assertSee('Wildlife sections');

        $this->put('/admin/pages/wildlife', [
            'title' => 'Meet the wild residents of Wilpattu.',
            'kicker' => 'WILDLIFE & HABITATS',
            'intro' => 'Discover the remarkable species and natural habitats that make Wilpattu unique.',
            'hero_image' => 8,
        ])->assertRedirect('/admin/pages/wildlife')->assertSessionHas('status');

        $this->assertDatabaseHas('page_contents', [
            'slug' => 'wildlife',
            'title' => 'Meet the wild residents of Wilpattu.',
            'hero_image' => 8,
        ]);

        $this->get('/wildlife')
            ->assertOk()
            ->assertSee('Meet the wild residents of Wilpattu.')
            ->assertSee('Discover the remarkable species and natural habitats');
    }

    public function test_administrator_can_add_edit_and_delete_wildlife_sections(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/wildlife/items', [
            'title' => 'Painted stork',
            'meta' => 'Birdlife · Wetlands',
            'text' => 'Watch for painted storks feeding around the shallow edges of a villu.',
            'image' => 15,
        ])->assertRedirect('/admin/pages/wildlife')->assertSessionHas('status');

        $item = PageContentItem::query()->where('title', 'Painted stork')->firstOrFail();
        $this->get('/wildlife')->assertSee('Painted stork');

        $this->put('/admin/pages/wildlife/items/'.$item->id, [
            'title' => 'Wetland birdlife',
            'meta' => 'Birdwatching · Villus',
            'text' => 'Look across the shallow water for storks, herons and seasonal visitors.',
            'image' => 7,
        ])->assertRedirect('/admin/pages/wildlife')->assertSessionHas('status');

        $this->assertDatabaseHas('page_content_items', [
            'id' => $item->id,
            'title' => 'Wetland birdlife',
            'image' => 7,
        ]);
        $this->get('/wildlife')->assertSee('Wetland birdlife')->assertDontSee('Painted stork');

        $this->delete('/admin/pages/wildlife/items/'.$item->id)
            ->assertRedirect('/admin/pages/wildlife')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('page_content_items', ['id' => $item->id]);
        $this->get('/wildlife')->assertDontSee('Wetland birdlife');
    }

    public function test_wildlife_content_rejects_unknown_image_numbers(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/wildlife/items', [
            'title' => 'Invalid wildlife image',
            'meta' => 'Test content',
            'text' => 'This wildlife section should not be saved.',
            'image' => 4,
        ])->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('page_content_items', ['title' => 'Invalid wildlife image']);
    }

    private function administrator(): User
    {
        return User::query()->where('username', 'admin')->firstOrFail();
    }
}
