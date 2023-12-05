<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User; // Make sure to import the User model if not already imported
use App\Models\Product; // Make sure to import the Product model if not already imported

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Create 10 orders with factory data
        \App\Models\Order::factory(10)->create()->each(function ($order) {
            // You can customize each order here
            $user = User::inRandomOrder()->first(); // Get a random user
            $order->user()->associate($user); // Associate the order with a random user

            // Attach random products to the order
            $products = Product::inRandomOrder()->limit(3)->get(); // Get 3 random products
            foreach ($products as $product) {
                $quantity = rand(1, 5); // You can adjust the quantity range as needed
                $order->products()->attach($product, ['quantity' => $quantity]);
            }

            // You can add more customizations as needed

            $order->save(); // Save the changes to the order
        });
    }
}
