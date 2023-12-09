<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Adjust the number based on your requirement (e.g., 100)
        $numberOfProducts = 100;

        // Use the factory to create fake products
        Product::factory($numberOfProducts)->create();
    }
}
