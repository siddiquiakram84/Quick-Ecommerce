<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the seeder.
     */
    public function run(): void
    {
        // Seed an admin user
        User::create([
            'name' => 'john doe',
            'email' => 'johndoe@example.com',
            'password' => 'admin_password',
            'phone' => '1234567890',
            'role' => 'admin',
            'status' => 1, // Assuming 1 represents an active status
            // ... other attributes
        ]);

        // Seed a non-admin user
        User::create([
            'name' => 'jane doe',
            'email' => 'janedoe@example.com',
            'password' => 'user_password',
            'phone' => '9876543210',
            'role' => 'user',
            'status' => 1, // Assuming 1 represents an active status
            // ... other attributes
        ]);

        // Seed additional users if needed
    }
}