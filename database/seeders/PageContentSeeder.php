<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class PageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (config('park.customizable_pages') as $slug) {
            $page = $slug === 'home'
                ? config('content.home')
                : config('content.pages.'.$slug);

            PageContent::query()->firstOrCreate([
                'slug' => $slug,
            ], [
                'title' => $page['title'],
                'kicker' => $page['kicker'],
                'intro' => $page['intro'],
                'hero_image' => $page['image'],
                'settings' => $page['settings'] ?? null,
            ]);
        }
    }
}
