<?php

namespace Database\Factories;

use App\Models\Page;
use App\Models\PageImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PageImage>
 */
class PageImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_id' => Page::factory(),
            'path' => 'pages/galleries/'.fake()->uuid().'.webp',
            'thumbnail_path' => 'pages/galleries/thumbnails/'.fake()->uuid().'.webp',
            'alt_text' => fake()->sentence(3),
            'sort_order' => 0,
        ];
    }
}
