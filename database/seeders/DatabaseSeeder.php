<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            CartSeeder::class,
            WishlistSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
