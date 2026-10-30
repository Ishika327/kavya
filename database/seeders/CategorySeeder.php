<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Romantic',
            'Emotional',
            'Comedy',
            'Motivational',
            'Philosophical',
            'Nature',
            'Friendship',
            'Sadness',
            'Life',
            'Family',
            'Social Issues',
            'Spiritual',
            'Short Quotes',
            'Free Verse',
        ];

        foreach ($categories as $name) {
            Category::query()->updateOrCreate(
                ['slug' => str($name)->slug()],
                ['name' => $name],
            );
        }
    }
}
