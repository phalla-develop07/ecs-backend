<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\{Cart, Order, OrderItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Post(
        path: '/api/checkout',
        operationId: 'checkout',
        summary: 'Checkout — converts all cart items into an order then clears the cart',
        tags: ['Orders'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'address',
                        type: 'string',
                        example: '123 Main St, Phnom Penh',
                        nullable: true,
                        description: 'Optional — shipping address, defaults to empty string if omitted'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Order placed successfully — cart is cleared after this call',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'order',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'user_id', type: 'integer', example: 1),
                                new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 89.97),
                                new OA\Property(
                                    property: 'status',
                                    type: 'string',
                                    example: 'pending',
                                    description: 'Always "pending" on creation'
                                ),
                                new OA\Property(property: 'address', type: 'string', example: '123 Main St, Phnom Penh'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                new OA\Property(
                                    property: 'items',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'order_id', type: 'integer', example: 1),
                                            new OA\Property(property: 'product_id', type: 'integer', example: 3),
                                            new OA\Property(property: 'quantity', type: 'integer', example: 2),
                                            new OA\Property(
                                                property: 'price',
                                                type: 'number',
                                                format: 'float',
                                                example: 29.99,
                                                description: 'Price snapshot at the time of checkout'
                                            ),
                                            new OA\Property(
                                                property: 'product',
                                                type: 'object',
                                                properties: [
                                                    new OA\Property(property: 'id', type: 'integer', example: 3),
                                                    new OA\Property(property: 'name', type: 'string', example: 'Rose Perfume'),
                                                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
                                                    new OA\Property(property: 'image', type: 'string', nullable: true, example: 'products/abc.jpg'),
                                                ]
                                            ),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated — Bearer token missing or invalid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Cart is empty — nothing to checkout',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Cart is empty'),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Checkout failed — transaction rolled back, cart is preserved',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Checkout failed: SQLSTATE[...] ...'),
                    ]
                )
            ),
        ]
    )]
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

            Cart::where('user_id', $request->user()->id)->delete();

            DB::commit();

            return response()->json(['success' => true, 'order' => $order->load('items.product')], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Checkout failed: ' . $e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/orders',
        operationId: 'getOrders',
        summary: 'Get all orders for the authenticated user (paginated, 10 per page)',
        tags: ['Orders'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Orders retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Products are gotten successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                new OA\Property(property: 'per_page', type: 'integer', example: 10),
                                new OA\Property(property: 'total', type: 'integer', example: 25),
                                new OA\Property(
                                    property: 'data',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'user_id', type: 'integer', example: 1),
                                            new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 89.97),
                                            new OA\Property(property: 'status', type: 'string', example: 'pending'),
                                            new OA\Property(property: 'address', type: 'string', example: '123 Main St, Phnom Penh'),
                                            new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                            new OA\Property(
                                                property: 'items',
                                                type: 'array',
                                                items: new OA\Items(
                                                    properties: [
                                                        new OA\Property(property: 'id', type: 'integer', example: 1),
                                                        new OA\Property(property: 'product_id', type: 'integer', example: 3),
                                                        new OA\Property(property: 'quantity', type: 'integer', example: 2),
                                                        new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
                                                        new OA\Property(
                                                            property: 'product',
                                                            type: 'object',
                                                            properties: [
                                                                new OA\Property(property: 'id', type: 'integer', example: 3),
                                                                new OA\Property(property: 'name', type: 'string', example: 'Rose Perfume'),
                                                                new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
                                                                new OA\Property(property: 'image', type: 'string', nullable: true, example: 'products/abc.jpg'),
                                                            ]
                                                        ),
                                                    ]
                                                )
                                            ),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated — Bearer token missing or invalid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->latest()->paginate(10);

        return response()->json(['success' => true, 'message' => 'Products are gotten successfully', 'data' => $orders]);
    }

    #[OA\Get(
        path: '/api/orders/{id}',
        operationId: 'getOrder',
        summary: 'Get a single order with all its items and products',
        tags: ['Orders'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Order ID — must belong to the authenticated user',
                schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Order retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Order is gotten successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'user_id', type: 'integer', example: 1),
                                new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 89.97),
                                new OA\Property(property: 'status', type: 'string', example: 'pending'),
                                new OA\Property(property: 'address', type: 'string', example: '123 Main St, Phnom Penh'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                new OA\Property(
                                    property: 'items',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'order_id', type: 'integer', example: 1),
                                            new OA\Property(property: 'product_id', type: 'integer', example: 3),
                                            new OA\Property(property: 'quantity', type: 'integer', example: 2),
                                            new OA\Property(
                                                property: 'price',
                                                type: 'number',
                                                format: 'float',
                                                example: 29.99,
                                                description: 'Price snapshot at the time of checkout — may differ from current product price'
                                            ),
                                            new OA\Property(
                                                property: 'product',
                                                type: 'object',
                                                properties: [
                                                    new OA\Property(property: 'id', type: 'integer', example: 3),
                                                    new OA\Property(property: 'name', type: 'string', example: 'Rose Perfume'),
                                                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
                                                    new OA\Property(property: 'image', type: 'string', nullable: true, example: 'products/abc.jpg'),
                                                ]
                                            ),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated — Bearer token missing or invalid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Order not found or does not belong to the authenticated user',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Not found'),
                    ]
                )
            ),
        ]
    )]
    public function show(Request $request, $id)
    {
        $order = Order::with('items.product')
            ->where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$order) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        return response()->json(['success' => true, 'message' => 'Order is gotten successfully', 'data' => $order]);
    }
}