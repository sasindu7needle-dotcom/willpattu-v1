<?php

namespace Database\Seeders;

use App\Models\PageContent;
use Illuminate\Database\Seeder;

class PageContentItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (config('park.customizable_pages') as $slug) {
            $pageContent = PageContent::query()->where('slug', $slug)->firstOrFail();
            $items = $slug === 'home'
                ? config('content.home.items')
                : config('content.pages.'.$slug.'.items');

            if ($pageContent->items()->exists()) {
                continue;
            }

            foreach ($items as $position => $item) {
                $pageContent->items()->create([...$item, 'position' => $position]);
            }
        }
    }
}
