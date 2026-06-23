<?php
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
 
class ReviewController extends Controller
{
    // GET /api/products/{productId}/reviews  (public)
    public function index($productId)
    {
        $reviews = Review::with('user:id,name')
            ->where('product_id', $productId)->latest()->get();
        return response()->json(['success' => true, 'data' => $reviews]);
    }
 
    // POST /api/products/{productId}/reviews  (auth required)
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);
 
        // One review per user per product
        $exists = Review::where('user_id', $request->user()->id)
            ->where('product_id', $productId)->exists();
 
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'You already reviewed this product'], 409);
        }
 
        $review = Review::create([
            'user_id'    => $request->user()->id,
            'product_id' => $productId,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);
 
        return response()->json(['success' => true, 'data' => $review->load('user:id,name')], 201);
    }
}

