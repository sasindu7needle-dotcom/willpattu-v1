<?php

namespace Tests\Feature;

use App\Models\PageContentItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryPageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_editor_requires_an_authenticated_administrator(): void
    {
        $this->get('/admin/pages/gallery')->assertRedirect('/admin/login');

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get('/admin/pages/gallery')->assertForbidden();
    }

    public function test_administrator_can_update_gallery_page_details(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->get('/admin/pages/gallery')
            ->assertOk()
            ->assertSee('Hero content')
            ->assertSee('Gallery photographs')
            ->assertSee('Filter category')
            ->assertSee('data-confirm-dialog', false);

        $this->put('/admin/pages/gallery', [
            'title' => 'Wild moments, quietly observed.',
            'kicker' => 'WILPATTU GALLERY',
            'intro' => 'Explore photographs of wildlife, forest trails and the park landscape.',
            'hero_image' => 3,
        ])->assertRedirect('/admin/pages/gallery')->assertSessionHas('status');

        $this->assertDatabaseHas('page_contents', [
            'slug' => 'gallery',
            'title' => 'Wild moments, quietly observed.',
            'hero_image' => 3,
        ]);

        $this->get('/gallery')
            ->assertOk()
            ->assertSee('Wild moments, quietly observed.')
            ->assertSee('Explore photographs of wildlife, forest trails');
    }

    public function test_administrator_can_add_edit_and_delete_gallery_photographs(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/gallery/items', [
            'title' => 'Morning light over the villu',
            'meta' => 'Landscapes',
            'text' => 'Soft morning light falling across a natural lake in Wilpattu.',
            'image' => 10,
        ])->assertRedirect('/admin/pages/gallery')->assertSessionHas('status');

        $item = PageContentItem::query()->where('title', 'Morning light over the villu')->firstOrFail();
        $this->get('/gallery')
            ->assertSee('Morning light over the villu')
            ->assertSee('data-filter="Landscapes"', false);

        $this->put('/admin/pages/gallery/items/'.$item->id, [
            'title' => 'Evening light over the villu',
            'meta' => 'Nature',
            'text' => 'Warm evening light settling across a natural lake in Wilpattu.',
            'image' => 14,
        ])->assertRedirect('/admin/pages/gallery')->assertSessionHas('status');

        $this->assertDatabaseHas('page_content_items', [
            'id' => $item->id,
            'title' => 'Evening light over the villu',
            'meta' => 'Nature',
            'image' => 14,
        ]);
        $this->get('/gallery')->assertSee('Evening light over the villu')->assertDontSee('Morning light over the villu');

        $this->delete('/admin/pages/gallery/items/'.$item->id)
            ->assertRedirect('/admin/pages/gallery')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('page_content_items', ['id' => $item->id]);
        $this->get('/gallery')->assertDontSee('Evening light over the villu');
    }

    public function test_gallery_content_rejects_unknown_image_numbers(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/gallery/items', [
            'title' => 'Invalid gallery image',
            'meta' => 'Nature',
            'text' => 'This gallery photograph should not be saved.',
            'image' => 4,
        ])->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('page_content_items', ['title' => 'Invalid gallery image']);
    }

    private function administrator(): User
    {
        return User::query()->where('username', 'admin')->firstOrFail();
    }
}
