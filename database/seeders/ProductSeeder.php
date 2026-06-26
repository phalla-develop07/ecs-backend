<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::insert([
            [
                'category_id' => 1,
                'name' => 'iPhone 15',
                'slug' => 'iphone-15',
                'description' => 'Apple smartphone',
                'price' => 999,
                'stock' => 20,
                'image' => null,
                'is_active' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Samsung Galaxy S24',
                'slug' => 'samsung-galaxy-s24',
                'description' => 'Samsung smartphone',
                'price' => 850,
                'stock' => 15,
                'image' => null,
                'is_active' => true,
            ],
            [
                'category_id' => 2,
                'name' => 'Nike Air Max',
                'slug' => 'nike-air-max',
                'description' => 'Running shoes',
                'price' => 120,
                'stock' => 30,
                'image' => null,
                'is_active' => true,
            ],
            [
                'category_id' => 3,
                'name' => 'Laravel Book',
                'slug' => 'laravel-book',
                'description' => 'Laravel 12 Guide',
                'price' => 35,
                'stock' => 50,
                'image' => null,
                'is_active' => true,
            ],
            [
                'category_id' => 4,
                'name' => 'Blender',
                'slug' => 'blender',
                'description' => 'Kitchen blender',
                'price' => 60,
                'stock' => 10,
                'image' => null,
                'is_active' => true,
            ]
        ]);
    }
}
