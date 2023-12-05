<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        Category::factory(100)->create([
            'name' => $this->getRandomCategoryName(),
            'description' => $this->getRandomCategoryDescription(),
            // Add other fillable attributes here
        ]);
    }

    private function getRandomCategoryName()
    {
        // Replace with logic to generate or retrieve random category names
        $names = ['Electronics', 'Clothing', 'Books', 'Home & Garden', 'Sports'];
        return $names[array_rand($names)];
    }

    private function getRandomCategoryDescription()
    {
        // Replace with logic to generate or retrieve random category descriptions
        $descriptions = [
            'Explore the latest electronic gadgets.',
            'Stay in style with our trendy clothing collection.',
            'Immerse yourself in a world of literature.',
            'Upgrade your living space with our home essentials.',
            'Fuel your passion for sports and fitness.'
        ];
        return $descriptions[array_rand($descriptions)];
    }
}