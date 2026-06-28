<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class WishlistController extends Controller
{
    #[OA\Get(
        path: '/api/wishlist',
        operationId: 'getWishlist',
        summary: 'Get all wishlist items for the authenticated user',
        tags: ['Wishlist'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Wishlist items retrieved successfully — returns empty array if wishlist is empty',
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
                                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                    new OA\Property(
                                        property: 'product',
                                        type: 'object',
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 3),
                                            new OA\Property(property: 'name', type: 'string', example: 'Rose Perfume'),
                                            new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
                                            new OA\Property(property: 'image', type: 'string', nullable: true, example: 'products/abc.jpg'),
                                            new OA\Property(property: 'stock', type: 'integer', example: 100),
                                        ]
                                    ),
                                ]
                            )
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
        $items = Wishlist::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json(['success' => true, 'data' => $items]);
    }

    #[OA\Post(
        path: '/api/wishlist',
        operationId: 'addToWishlist',
        summary: 'Add a product to the wishlist — ignored if product is already in wishlist',
        tags: ['Wishlist'],
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
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Product added to wishlist successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            description: 'Newly created wishlist entry — note: product is NOT eager loaded on this response',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'user_id', type: 'integer', example: 1),
                                new OA\Property(property: 'product_id', type: 'integer', example: 3),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
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
                response: 409,
                description: 'Conflict — product is already in the user\'s wishlist',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Already in wishlist'),
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
                                new OA\Property(
                                    property: 'product_id',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The selected product_id is invalid.')
                                ),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
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

    #[OA\Delete(
        path: '/api/wishlist/{id}',
        operationId: 'removeFromWishlist',
        summary: 'Remove a specific item from the wishlist',
        tags: ['Wishlist'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Wishlist item ID — must belong to the authenticated user',
                schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Item removed from wishlist successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Removed from wishlist'),
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
                description: 'Wishlist item not found or does not belong to the authenticated user',
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
        $item = Wishlist::where('id', $id)
            ->where('user_id', $request->user()->id)->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        $item->delete();
        return response()->json(['success' => true, 'message' => 'Removed from wishlist']);
    }
}