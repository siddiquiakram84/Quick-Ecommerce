<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Creating 5 admin users
        User::factory(5)->create([
            'phone' => '1234567890',
            'role' => 'admin',
            'status' => 1,
            // Add other fillable attributes here
        ]);

        // Creating 5 regular user accounts
        User::factory(5)->create([
            'phone' => '1234567890',
            'role' => 'user',
            'status' => 1,
            // Add other fillable attributes here
        ]);
    }
}
