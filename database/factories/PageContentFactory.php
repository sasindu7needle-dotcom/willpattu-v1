<?php

namespace Database\Factories;

use App\Models\PageContent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageContent>
 */
class PageContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'title' => fake()->sentence(4),
            'kicker' => fake()->words(3, true),
            'intro' => fake()->paragraph(),
            'hero_image' => fake()->randomElement(config('park.content_images')),
            'settings' => null,
        ];
    }
}
