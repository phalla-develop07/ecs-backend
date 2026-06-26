<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Order::insert([
            [
                'user_id' => 2,
                'total_amount' => 1119,
                'status' => 'delivered',
                'address' => '123 Main Street, Phnom Penh, Cambodia'
            ],
            [
                'user_id' => 3,
                'total_amount' => 850,
                'status' => 'pending',
                'address' => '456 Riverside Road, Siem Reap, Cambodia'
            ]
        ]);
    }
}
