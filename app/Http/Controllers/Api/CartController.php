<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class CartController extends Controller
{
    #[OA\Get(
        path: '/api/cart',
        operationId: 'getCart',
        summary: 'Get all cart items for the authenticated user',
        tags: ['Cart'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cart items retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'user_id', type: 'integer', example: 1),
                                    new OA\Property(property: 'product_id', type: 'integer', example: 3),
                                    new OA\Property(property: 'quantity', type: 'integer', example: 2),
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
                        new OA\Property(
                            property: 'total',
                            type: 'number',
                            format: 'float',
                            example: 59.98,
                            description: 'Sum of (product price × quantity) across all cart items'
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
        $items = Cart::with('product')
            ->where('user_id', $request->user()->id)->get();

        $total = $items->sum(fn($i) => $i->product->price * $i->quantity);

        return response()->json(['success' => true, 'data' => $items, 'total' => $total]);
    }

    #[OA\Post(
        path: '/api/cart',
        operationId: 'addToCart',
        summary: 'Add a product to cart — increments quantity if product already exists in cart',
        tags: ['Cart'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['product_id'],
                properties: [
                    new OA\Property(
                        property: 'product_id',
                        type: 'integer',
                        example: 3,
                        description: 'Required — must exist in products table'
                    ),
                    new OA\Property(
                        property: 'quantity',
                        type: 'integer',
                        minimum: 1,
                        example: 2,
                        description: 'Optional — must be integer min 1, defaults to 1 if omitted'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Product added to cart (or quantity incremented if already in cart)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'user_id', type: 'integer', example: 1),
                                new OA\Property(property: 'product_id', type: 'integer', example: 3),
                                new OA\Property(property: 'quantity', type: 'integer', example: 2),
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
                description: 'Validation failed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The product_id field is required.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'product_id', type: 'array', items: new OA\Items(type: 'string', example: 'The selected product_id is invalid.')),
                                new OA\Property(property: 'quantity', type: 'array', items: new OA\Items(type: 'string', example: 'The quantity must be at least 1.')),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1',
        ]);

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

    #[OA\Put(
        path: '/api/cart/{id}',
        operationId: 'updateCart',
        summary: 'Update the quantity of a specific cart item',
        tags: ['Cart'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Cart item ID — must belong to the authenticated user',
                schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['quantity'],
                properties: [
                    new OA\Property(
                        property: 'quantity',
                        type: 'integer',
                        minimum: 1,
                        example: 5,
                        description: 'Required — integer min 1, replaces the current quantity'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cart item quantity updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'user_id', type: 'integer', example: 1),
                                new OA\Property(property: 'product_id', type: 'integer', example: 3),
                                new OA\Property(property: 'quantity', type: 'integer', example: 5),
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
                description: 'Cart item not found or does not belong to the authenticated user',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Not found'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The quantity field is required.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'quantity', type: 'array', items: new OA\Items(type: 'string', example: 'The quantity must be at least 1.')),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $item = Cart::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$item) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        $item->update(['quantity' => $request->quantity]);
        return response()->json(['success' => true, 'data' => $item->load('product')]);
    }

    #[OA\Delete(
        path: '/api/cart/{id}',
        operationId: 'removeFromCart',
        summary: 'Remove a specific item from the cart',
        tags: ['Cart'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Cart item ID — must belong to the authenticated user',
                schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Cart item removed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Item removed'),
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
                description: 'Cart item not found or does not belong to the authenticated user',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Not found'),
                    ]
                )
            ),
        ]
    )]
    public function destroy(Request $request, $id)
    {
        $item = Cart::where('id', $id)->where('user_id', $request->user()->id)->first();
        if (!$item) return response()->json(['success' => false, 'message' => 'Not found'], 404);

        $item->delete();
        return response()->json(['success' => true, 'message' => 'Item removed']);
    }
}