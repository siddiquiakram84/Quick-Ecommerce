<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        Product::create([
            'category_id' => 1, // Replace with an existing category ID
            'name' => 'Sample Product 1',
            'description' => 'This is a sample product description.',
            'price' => 19.99,
            'quantity_in_stock' => 50,
            'image' => 'sample_image_1.jpg',
        ]);

        // Add more products as needed
        Product::create([
            'category_id' => 2, // Replace with another existing category ID
            'name' => 'Sample Product 2',
            'description' => 'Another sample product description.',
            'price' => 29.99,
            'quantity_in_stock' => 30,
            'image' => 'sample_image_2.jpg',
        ]);

        // Add more products with specific data as needed
    }
}
