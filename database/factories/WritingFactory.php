<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use App\Models\Writing;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Writing>
 */
class WritingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'author_id' => User::factory(),
            'title' => fake()->sentence(4),
            'slug' => fake()->unique()->slug(4),
            'content' => fake()->paragraphs(3, true),
            'language' => 'en',
            'status' => 'draft',
            'is_featured' => false,
            'is_poem_of_day' => false,
            'is_poem_of_week' => false,
            'is_anonymous' => false,
            'view_count' => 0,
            'like_count' => 0,
            'published_at' => null,
        ];
    }
}
