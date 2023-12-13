<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        return [
            'user_id' => $this->faker->randomElement(\App\Models\User::pluck('id')),
            'total_price' => $this->faker->randomFloat(2, 10, 500),
            'status' => $this->faker->randomElement(['Delivered', 'Pending', 'Processing']),
            'payment_status' => $this->faker->randomElement([0, 1]),
            'delivery_address' => $this->faker->address,
            'delivery_method' => $this->faker->word,
        ];
    }
}
