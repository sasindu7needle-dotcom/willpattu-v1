<?php

namespace Tests\Feature;

use App\Models\PageContentItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SafariPageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_safari_editor_requires_an_authenticated_administrator(): void
    {
        $this->get('/admin/pages/safaris')->assertRedirect('/admin/login');

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get('/admin/pages/safaris')->assertForbidden();
    }

    public function test_administrator_can_update_safaris_page_details(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->get('/admin/pages/safaris')
            ->assertOk()
            ->assertSee('Hero content')
            ->assertSee('Safari experiences')
            ->assertSee('data-confirm-dialog', false)
            ->assertSee('Delete this section?');

        $this->put('/admin/pages/safaris', [
            'title' => 'Journey deeper into the wild.',
            'kicker' => 'PRIVATE WILPATTU SAFARIS',
            'intro' => 'Follow forest trails with an experienced guide and an unhurried sense of discovery.',
            'hero_image' => 3,
        ])->assertRedirect('/admin/pages/safaris')->assertSessionHas('status');

        $this->assertDatabaseHas('page_contents', [
            'slug' => 'safaris',
            'title' => 'Journey deeper into the wild.',
            'hero_image' => 3,
        ]);

        $this->get('/safaris')
            ->assertOk()
            ->assertSee('Journey deeper into the wild.')
            ->assertSee('Follow forest trails with an experienced guide');
    }

    public function test_administrator_can_add_edit_and_delete_safari_experiences(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/safaris/items', [
            'title' => 'Sunrise photography safari',
            'meta' => 'Private jeep · First light',
            'text' => 'A patient early drive designed for wildlife photographers.',
            'image' => 6,
        ])->assertRedirect('/admin/pages/safaris')->assertSessionHas('status');

        $item = PageContentItem::query()->where('title', 'Sunrise photography safari')->firstOrFail();
        $this->get('/safaris')->assertSee('Sunrise photography safari');

        $this->put('/admin/pages/safaris/items/'.$item->id, [
            'title' => 'Golden-hour photography safari',
            'meta' => 'Private jeep · Photographer-led',
            'text' => 'An updated private drive timed for soft forest light.',
            'image' => 9,
        ])->assertRedirect('/admin/pages/safaris')->assertSessionHas('status');

        $this->assertDatabaseHas('page_content_items', [
            'id' => $item->id,
            'title' => 'Golden-hour photography safari',
            'image' => 9,
        ]);
        $this->get('/safaris')->assertSee('Golden-hour photography safari')->assertDontSee('Sunrise photography safari');

        $this->delete('/admin/pages/safaris/items/'.$item->id)
            ->assertRedirect('/admin/pages/safaris')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('page_content_items', ['id' => $item->id]);
        $this->get('/safaris')->assertDontSee('Golden-hour photography safari');
    }

    public function test_safari_content_rejects_unknown_image_numbers(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/safaris/items', [
            'title' => 'Invalid safari image',
            'meta' => 'Test content',
            'text' => 'This safari experience should not be saved.',
            'image' => 4,
        ])->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('page_content_items', ['title' => 'Invalid safari image']);
    }

    private function administrator(): User
    {
        return User::query()->where('username', 'admin')->firstOrFail();
    }
}
