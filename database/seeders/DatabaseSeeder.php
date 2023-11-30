<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(OrderSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(OrderProductSeeder::class);
        $this->call(CartSeeder::class);
    }
}