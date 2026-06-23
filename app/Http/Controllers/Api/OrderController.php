<?php
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\{Cart, Order, OrderItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
 
class OrderController extends Controller
{
    // POST /api/checkout
    public function checkout(Request $request)
    {
        $cartItems = Cart::with('product')->where('user_id', $request->user()->id)->get();
 
        if ($cartItems->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Cart is empty'], 422);
        }
 
        $total = $cartItems->sum(fn($i) => $i->product->price * $i->quantity);
 
        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id'      => $request->user()->id,
                'total_amount' => $total,
                'status'       => 'pending',
                'address'      => $request->input('address', ''),
            ]);
 
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->product->price,
                ]);
            }
 
            // Clear cart after checkout
            Cart::where('user_id', $request->user()->id)->delete();
 
            DB::commit();
 
            return response()->json(['success' => true, 'order' => $order->load('items.product')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Checkout failed: ' . $e->getMessage()], 500);
        }
    }
 
    // GET /api/orders
    public function index(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->latest()->paginate(10);
        return response()->json(['success' => true, 'data' => $orders]);
    }
 
    // GET /api/orders/{id}
    public function show(Request $request, $id)
    {
        $order = Order::with('items.product')
            ->where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$order) return response()->json(['success' => false, 'message' => 'Not found'], 404);
        return response()->json(['success' => true, 'data' => $order]);
    }
}

