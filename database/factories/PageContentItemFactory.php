<?php

namespace Database\Factories;

use App\Models\PageContent;
use App\Models\PageContentItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageContentItem>
 */
class PageContentItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_content_id' => PageContent::factory(),
            'title' => fake()->sentence(4),
            'meta' => fake()->words(4, true),
            'text' => fake()->paragraph(),
            'image' => fake()->randomElement(config('park.content_images')),
            'position' => 0,
        ];
    }
}
