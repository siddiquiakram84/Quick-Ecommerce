<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'category_id' => function () {
                return \App\Models\Category::factory()->create()->id;
            },
            'name' => $this->faker->word,
            'description' => $this->faker->paragraph,
            'price' => $this->faker->randomFloat(2, 10, 100),
            'quantity_in_stock' => $this->faker->numberBetween(1, 100),
            'image' => $this->faker->imageUrl(),
            // Add other attributes here
        ];
    }
}