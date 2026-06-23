<?php
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
 
class CartController extends Controller
{
    // GET /api/cart
    public function index(Request $request)
    {
        $items = Cart::with('product')
            ->where('user_id', $request->user()->id)->get();
 
        $total = $items->sum(fn($i) => $i->product->price * $i->quantity);
 
        return response()->json(['success' => true, 'data' => $items, 'total' => $total]);
    }
 
    // POST /api/cart
    public function store(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id', 'quantity' => 'integer|min:1']);
 
        $item = Cart::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)->first();
 
        if ($item) {
            $item->increment('quantity', $request->quantity ?? 1);
        } else {
            $item = Cart::create([
                'user_id'    => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity ?? 1,
            ]);
        }
 
        return response()->json(['success' => true, 'data' => $item->load('product')], 201);
    }
 
    // PUT /api/cart/{id}
    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
 
        $item = Cart::where('id', $id)->where('user_id', $request->user()->id)->first();
 
        if (!$item) return response()->json(['success' => false, 'message' => 'Not found'], 404);
 
        $item->update(['quantity' => $request->quantity]);
        return response()->json(['success' => true, 'data' => $item->load('product')]);
    }
 
    // DELETE /api/cart/{id}
    public function destroy(Request $request, $id)
    {
        $item = Cart::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$item) return response()->json(['success' => false, 'message' => 'Not found'], 404);
 
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Item removed']);
    }
}

