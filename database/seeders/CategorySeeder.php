<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Events',
            'Conferences',
            'Restaurants',
            'Experiences',
            'Venues',
            'Catering',
            'Photography',
            'Entertainment',
            'Planning',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'slug' => Str::slug($category)],
                ['name' => $category]
            );
        }
    }
}