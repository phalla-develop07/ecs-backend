<?php
// app/Http/Controllers/Admin/WishlistController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $query = Wishlist::with(['user', 'product'])->latest();

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            );
        }

        $wishlists = $query->paginate(10)->withQueryString();

        return view('admin.wishlists.index', compact('wishlists'));
    }

    public function destroy(Wishlist $wishlist)
    {
        $wishlist->delete();
        return redirect()->route('admin.wishlists.index')
            ->with('success', 'Wishlist item removed successfully.');
    }
}