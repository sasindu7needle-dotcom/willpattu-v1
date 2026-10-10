<?php

namespace Tests\Feature;

use App\Models\PageContentItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StayPageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_stay_editor_requires_an_authenticated_administrator(): void
    {
        $this->get('/admin/pages/stay')->assertRedirect('/admin/login');

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get('/admin/pages/stay')->assertForbidden();
    }

    public function test_administrator_can_update_stay_page_details(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->get('/admin/pages/stay')
            ->assertOk()
            ->assertSee('Hero content')
            ->assertSee('Stay sections');

        $this->put('/admin/pages/stay', [
            'title' => 'Sleep beside the wilderness.',
            'kicker' => 'STAY IN WILPATTU',
            'intro' => 'A quiet retreat shaped by the forest and its rhythms.',
            'hero_image' => 6,
        ])->assertRedirect('/admin/pages/stay')->assertSessionHas('status');

        $this->assertDatabaseHas('page_contents', [
            'slug' => 'stay',
            'title' => 'Sleep beside the wilderness.',
            'hero_image' => 6,
        ]);

        $this->get('/stay')
            ->assertOk()
            ->assertSee('Sleep beside the wilderness.')
            ->assertSee('A quiet retreat shaped by the forest and its rhythms.');
    }

    public function test_administrator_can_add_edit_and_delete_stay_sections(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/stay/items', [
            'title' => 'Forest family villa',
            'meta' => 'Families · Four guests',
            'text' => 'A generous retreat for families travelling together.',
            'image' => 11,
        ])->assertRedirect('/admin/pages/stay')->assertSessionHas('status');

        $item = PageContentItem::query()->where('title', 'Forest family villa')->firstOrFail();
        $this->get('/stay')->assertSee('Forest family villa');

        $this->put('/admin/pages/stay/items/'.$item->id, [
            'title' => 'Private forest villa',
            'meta' => 'Families · Six guests',
            'text' => 'An updated private retreat for families travelling together.',
            'image' => 14,
        ])->assertRedirect('/admin/pages/stay')->assertSessionHas('status');

        $this->assertDatabaseHas('page_content_items', [
            'id' => $item->id,
            'title' => 'Private forest villa',
            'image' => 14,
        ]);
        $this->get('/stay')->assertSee('Private forest villa')->assertDontSee('Forest family villa');

        $this->delete('/admin/pages/stay/items/'.$item->id)
            ->assertRedirect('/admin/pages/stay')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('page_content_items', ['id' => $item->id]);
        $this->get('/stay')->assertDontSee('Private forest villa');
    }

    public function test_stay_content_rejects_unknown_image_numbers(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/stay/items', [
            'title' => 'Invalid image section',
            'meta' => 'Test content',
            'text' => 'This section should not be saved.',
            'image' => 4,
        ])->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('page_content_items', ['title' => 'Invalid image section']);
    }

    private function administrator(): User
    {
        return User::query()->where('username', 'admin')->firstOrFail();
    }
}
