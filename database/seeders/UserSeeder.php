<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('passwordAdmin@123'),
            'role' => 'admin'
        ]);

        User::insert([
            [
                'name' => 'John Doe',
                'email' => 'john@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'user'
            ],
            [
                'name' => 'Alice Smith',
                'email' => 'alice@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'user'
            ],
            [
                'name' => 'David Lee',
                'email' => 'david@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'user'
            ]
        ]);
    }
} 
