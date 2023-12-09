<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Faker;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Get all user IDs
        $userIds = User::pluck('id')->toArray();

        // Create fake orders
        Order::factory(50)->create([
            'user_id' => function () use ($userIds) {
                return $this->faker->randomElement($userIds);
            },
        ]);
    }
}
