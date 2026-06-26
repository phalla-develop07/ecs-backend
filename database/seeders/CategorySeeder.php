<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
            [
                'name'        => 'Electronics',
                'slug'        => 'electronics',
                'description' => 'Electronic devices and gadgets',
                'image'       => null, // ✅ nullable — upload via admin panel later
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Fashion',
                'slug'        => 'fashion',
                'description' => 'Clothing and accessories',
                'image'       => null,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Books',
                'slug'        => 'books',
                'description' => 'Books and magazines',
                'image'       => null,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Home & Kitchen',
                'slug'        => 'home-kitchen',
                'description' => 'Kitchen appliances and home essentials',
                'image'       => null,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Sports',
                'slug'        => 'sports',
                'description' => 'Sports equipment and outdoor gear',
                'image'       => null,
                'is_active'   => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}