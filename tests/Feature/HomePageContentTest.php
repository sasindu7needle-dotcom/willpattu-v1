<?php

namespace Tests\Feature;

use App\Models\PageContent;
use App\Models\PageContentItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomePageContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_editor_requires_an_authenticated_administrator(): void
    {
        $this->get('/admin/pages/home')->assertRedirect('/admin/login');

        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get('/admin/pages/home')->assertForbidden();
    }

    public function test_administrator_can_update_home_hero_content_without_replacing_animation_artwork(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->get('/admin/pages/home')
            ->assertOk()
            ->assertSee('Hero content')
            ->assertSee('Animal animation')
            ->assertSee('Discovery cards')
            ->assertSee('Animation background');

        $this->put('/admin/pages/home', [
            'title' => 'RETURN TO THE WILD',
            'kicker' => 'BEGIN YOUR WILPATTU STORY',
            'intro' => 'An unforgettable journey into the forest.',
        ])->assertRedirect('/admin/pages/home')->assertSessionHas('status');

        $this->assertDatabaseHas('page_contents', [
            'slug' => 'home',
            'title' => 'RETURN TO THE WILD',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('RETURN TO THE')
            ->assertSee('WILD')
            ->assertSee('BEGIN YOUR WILPATTU STORY')
            ->assertSee('wilpattu-forest-plate.png')
            ->assertSee('data-wildlife-journey', false);
    }

    public function test_administrator_can_upload_disable_and_reset_the_homepage_animation(): void
    {
        Storage::fake('public');
        $this->seed();
        $this->actingAs($this->administrator());

        $this->put('/admin/pages/home/animation', [
            'animation_enabled' => '1',
            'animation_alt' => 'A tiger walking through the forest',
            'animation_file' => UploadedFile::fake()->image('tiger-atlas.png', 400, 300),
        ])->assertRedirect('/admin/pages/home')->assertSessionHas('status');

        $pageContent = PageContent::query()->where('slug', 'home')->firstOrFail();
        $animationPath = $pageContent->settings['animation_path'];

        Storage::disk('public')->assertExists($animationPath);
        $this->get('/')
            ->assertOk()
            ->assertSee('A tiger walking through the forest')
            ->assertSee('data-animation-src=', false)
            ->assertSee(Storage::disk('public')->url($animationPath), false);

        $this->put('/admin/pages/home/animation', [
            'animation_enabled' => '0',
            'animation_alt' => 'A tiger walking through the forest',
        ])->assertRedirect('/admin/pages/home')->assertSessionHas('status');

        $this->get('/')->assertOk()->assertDontSee('class="leopard-actor"', false);

        $this->delete('/admin/pages/home/animation')
            ->assertRedirect('/admin/pages/home')
            ->assertSessionHas('status');

        Storage::disk('public')->assertMissing($animationPath);
        $settings = $pageContent->fresh()->settings;
        $this->assertTrue($settings['animation_enabled']);
        $this->assertNull($settings['animation_path']);
        $this->get('/')->assertSee('images/leopard-front-walk-atlas.png', false);
    }

    public function test_homepage_animation_rejects_an_incompatible_sprite_grid(): void
    {
        Storage::fake('public');
        $this->seed();
        $this->actingAs($this->administrator());

        $this->put('/admin/pages/home/animation', [
            'animation_enabled' => '1',
            'animation_alt' => 'An invalid animal animation',
            'animation_file' => UploadedFile::fake()->image('invalid-atlas.png', 401, 300),
        ])->assertSessionHasErrors('animation_file');

        Storage::disk('public')->assertDirectoryEmpty('home-animations');
    }

    public function test_administrator_can_add_edit_and_delete_home_discovery_cards(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/home/items', [
            'title' => 'Plan your escape',
            'meta' => 'Everything you need to know',
            'text' => 'plan-your-stay',
            'image' => 11,
        ])->assertRedirect('/admin/pages/home')->assertSessionHas('status');

        $item = PageContentItem::query()->where('title', 'Plan your escape')->firstOrFail();
        $this->get('/')->assertSee('Plan your escape')->assertSee('Everything you need to know');

        $this->put('/admin/pages/home/items/'.$item->id, [
            'title' => 'Prepare for Wilpattu',
            'meta' => 'Plan a thoughtful journey',
            'text' => 'getting-here',
            'image' => 14,
        ])->assertRedirect('/admin/pages/home')->assertSessionHas('status');

        $this->assertDatabaseHas('page_content_items', [
            'id' => $item->id,
            'title' => 'Prepare for Wilpattu',
            'text' => 'getting-here',
            'image' => 14,
        ]);
        $this->get('/')
            ->assertSee('Prepare for Wilpattu')
            ->assertDontSee('Everything you need to know');

        $this->delete('/admin/pages/home/items/'.$item->id)
            ->assertRedirect('/admin/pages/home')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('page_content_items', ['id' => $item->id]);
        $this->get('/')->assertDontSee('Prepare for Wilpattu');
    }

    public function test_home_discovery_cards_reject_unknown_destinations_and_images(): void
    {
        $this->seed();
        $this->actingAs($this->administrator());

        $this->post('/admin/pages/home/items', [
            'title' => 'Invalid card',
            'meta' => 'This should not be saved',
            'text' => 'unknown-page',
            'image' => 4,
        ])->assertSessionHasErrors(['text', 'image']);

        $this->assertDatabaseMissing('page_content_items', ['title' => 'Invalid card']);
    }

    private function administrator(): User
    {
        return User::query()->where('username', 'admin')->firstOrFail();
    }
}
