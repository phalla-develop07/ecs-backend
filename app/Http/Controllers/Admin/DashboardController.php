<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_categories' => Category::count(),
            'total_products'   => Product::count(),
            'total_users'      => User::where('role', 'user')->count(),
            'low_stock'        => Product::where('stock', '<', 10)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
