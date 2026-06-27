<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\Review;
use App\Models\Wishlist;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_categories' => Category::count(),
            'total_products'   => Product::count(),
            'total_users'      => User::where('role', 'user')->count(),
            'total_orders'     => Order::count(),
            'total_reviews'    => Review::count(),
            'total_wishlists'  => Wishlist::count(),
            'low_stock'        => Product::where('stock', '<', 10)->count(),
            'pending_orders'   => Order::where('status', 'pending')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}