<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    // GET /api/wishlist
    public function index(Request $request)
    {
        $items = Wishlist::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    // POST /api/wishlist
    public function store(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $exists = Wishlist::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Already in wishlist'], 409);
        }

        $item = Wishlist::create([
            'user_id'    => $request->user()->id,
            'product_id' => $request->product_id,
        ]);

        return response()->json(['success' => true, 'data' => $item], 201);
    }

    // DELETE /api/wishlist/{id}
    public function destroy(Request $request, $id)
    {
        $item = Wishlist::where('id', $id)
            ->where('user_id', $request->user()->id)->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $item->delete();
        return response()->json(['success' => true, 'message' => 'Removed from wishlist']);
    }
}
