<?php

namespace Database\Seeders;

use App\Models\OrderItem;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        OrderItem::insert([
            [
                'order_id' => 1,
                'product_id' => 1,
                'quantity' => 1,
                'price' => 999
            ],
            [
                'order_id' => 1,
                'product_id' => 3,
                'quantity' => 1,
                'price' => 120
            ],
            [
                'order_id' => 2,
                'product_id' => 2,
                'quantity' => 1,
                'price' => 850
            ]
        ]);
    }
}
