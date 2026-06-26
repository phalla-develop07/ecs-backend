<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        Review::insert([
            [
                'user_id' => 2,
                'product_id' => 1,
                'rating' => 5,
                'comment' => 'Excellent phone.'
            ],
            [
                'user_id' => 3,
                'product_id' => 2,
                'rating' => 4,
                'comment' => 'Very good product.'
            ],
            [
                'user_id' => 4,
                'product_id' => 3,
                'rating' => 5,
                'comment' => 'Helpful book.'
            ]
        ]);
    }
}