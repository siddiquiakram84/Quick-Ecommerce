<?php

namespace Database\Factories;

use App\Models\OrderProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderProductFactory extends Factory
{
    protected $model = OrderProduct::class;

    public function definition()
    {
        return [
            'order_id' => function () {
                return \App\Models\Order::factory()->create()->id;
            },
            'product_id' => function () {
                return \App\Models\Product::factory()->create()->id;
            },
            'quantity' => $this->faker->numberBetween(1, 10),
            // Add other attributes here
        ];
    }
}