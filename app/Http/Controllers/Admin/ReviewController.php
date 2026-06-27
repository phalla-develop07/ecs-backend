<?php
// app/Http/Controllers/Admin/ReviewController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product'])->latest();

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('search')) {
            $query->where('comment', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($q) =>
                      $q->where('name', 'like', '%' . $request->search . '%')
                  );
        }

        $reviews = $query->paginate(10)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function show(Review $review)
    {
        $review->load(['user', 'product']);
        return view('admin.reviews.show', compact('review'));
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}