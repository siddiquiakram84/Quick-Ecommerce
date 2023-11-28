<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            OrderSeeder::class,
            ProductSeeder::class,
            OrderProductSeeder::class,
            CartSeeder::class,
        ]);
    }
}
