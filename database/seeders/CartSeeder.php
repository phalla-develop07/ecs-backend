<?php

namespace Database\Seeders;

use App\Models\Cart;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        Cart::insert([
            [
                'user_id' => 2,
                'product_id' => 1,
                'quantity' => 1
            ],
            [
                'user_id' => 2,
                'product_id' => 3,
                'quantity' => 2
            ],
            [
                'user_id' => 3,
                'product_id' => 2,
                'quantity' => 1
            ]
        ]);
    }
}
