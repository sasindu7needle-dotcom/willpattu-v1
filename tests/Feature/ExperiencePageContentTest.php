<?php

namespace Tests\Feature;

use App\Models\PageContentItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExperiencePageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_experiences_editor_requires_an_authenticated_administrator(): void
    {
        $this->get('/admin/pages/experiences')->assertRedirect('/admin/login');

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get('/admin/pages/experiences')->assertForbidden();
    }

    public function test_administrator_can_update_experiences_page_details(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->get('/admin/pages/experiences')
            ->assertOk()
            ->assertSee('Hero content')
            ->assertSee('Experiences')
            ->assertSee('data-confirm-dialog', false);

        $this->put('/admin/pages/experiences', [
            'title' => 'Discover more beyond the safari.',
            'kicker' => 'CURATED EXPERIENCES',
            'intro' => 'Explore nature, culture and quiet moments beyond the national park.',
            'hero_image' => 7,
        ])->assertRedirect('/admin/pages/experiences')->assertSessionHas('status');

        $this->assertDatabaseHas('page_contents', [
            'slug' => 'experiences',
            'title' => 'Discover more beyond the safari.',
            'hero_image' => 7,
        ]);

        $this->get('/experiences')
            ->assertOk()
            ->assertSee('Discover more beyond the safari.')
            ->assertSee('Explore nature, culture and quiet moments');
    }

    public function test_administrator_can_add_edit_and_delete_experiences(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/experiences/items', [
            'title' => 'Sunset nature walk',
            'meta' => 'Guided · By arrangement',
            'text' => 'Take a gentle guided walk as the landscape settles into evening.',
            'image' => 12,
        ])->assertRedirect('/admin/pages/experiences')->assertSessionHas('status');

        $item = PageContentItem::query()->where('title', 'Sunset nature walk')->firstOrFail();
        $this->get('/experiences')->assertSee('Sunset nature walk');

        $this->put('/admin/pages/experiences/items/'.$item->id, [
            'title' => 'Twilight nature walk',
            'meta' => 'Private guide · Evening',
            'text' => 'An updated guided walk through the landscape in the soft evening light.',
            'image' => 14,
        ])->assertRedirect('/admin/pages/experiences')->assertSessionHas('status');

        $this->assertDatabaseHas('page_content_items', [
            'id' => $item->id,
            'title' => 'Twilight nature walk',
            'image' => 14,
        ]);
        $this->get('/experiences')->assertSee('Twilight nature walk')->assertDontSee('Sunset nature walk');

        $this->delete('/admin/pages/experiences/items/'.$item->id)
            ->assertRedirect('/admin/pages/experiences')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('page_content_items', ['id' => $item->id]);
        $this->get('/experiences')->assertDontSee('Twilight nature walk');
    }

    public function test_experience_content_rejects_unknown_image_numbers(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/experiences/items', [
            'title' => 'Invalid experience image',
            'meta' => 'Test content',
            'text' => 'This experience should not be saved.',
            'image' => 4,
        ])->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('page_content_items', ['title' => 'Invalid experience image']);
    }

    private function administrator(): User
    {
        return User::query()->where('username', 'admin')->firstOrFail();
    }
}
