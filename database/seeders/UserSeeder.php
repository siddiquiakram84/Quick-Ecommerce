<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed 50 regular users
        User::factory(50)->create();

        // Seed 50 admin users
        User::factory(50)->admin()->create();
    }
}
