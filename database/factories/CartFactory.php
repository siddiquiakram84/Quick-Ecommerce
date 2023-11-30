<?php

namespace Database\Factories;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = Cart::class;

    public function definition()
    {
        return [
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'status' => $this->faker->randomElement([1, 2]), // Adjust as needed
            'total_price' => $this->faker->randomFloat(2, 10, 50),
            // Add other attributes here
        ];
    }
}