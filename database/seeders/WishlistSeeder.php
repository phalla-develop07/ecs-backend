<?php

namespace Database\Seeders;

use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        Wishlist::insert([
            [
                'user_id' => 2,
                'product_id' => 1
            ],
            [
                'user_id' => 2,
                'product_id' => 5
            ],
            [
                'user_id' => 3,
                'product_id' => 3
            ]
        ]);
    }
}
