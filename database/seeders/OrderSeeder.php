<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User; 
use App\Models\Product;
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run()
    {
        Order::create([
            'user_id' => 1, // Replace with an existing user ID
            'total_price' => 50.00,
            'status' => 'Processing',
            'payment_status' => 0,
            'delivery_address' => '123 Main St, Cityville',
            'delivery_method' => 'Standard',
        ]);

        // Add more orders as needed
        Order::create([
            'user_id' => 2, // Replace with another user ID
            'total_price' => 75.00,
            'status' => 'Pending',
            'payment_status' => 0,
            'delivery_address' => '456 Oak St, Townsville',
            'delivery_method' => 'Express',
        ]);

    }
}