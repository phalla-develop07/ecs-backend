<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReviewController extends Controller
{
    #[OA\Get(
        path: '/api/products/{productId}/reviews',
        operationId: 'getReviews',
        summary: 'Get all reviews for a specific product — public, no auth required',
        tags: ['Reviews'],
        parameters: [
            new OA\Parameter(
                name: 'productId',
                in: 'path',
                required: true,
                description: 'Product ID to fetch reviews for',
                schema: new OA\Schema(type: 'integer', minimum: 1, example: 3)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Reviews retrieved successfully — returns empty array if no reviews yet',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'product_id', type: 'integer', example: 3),
                                    new OA\Property(property: 'user_id', type: 'integer', example: 1),
                                    new OA\Property(
                                        property: 'rating',
                                        type: 'integer',
                                        minimum: 1,
                                        maximum: 5,
                                        example: 4,
                                        description: 'Star rating from 1 (lowest) to 5 (highest)'
                                    ),
                                    new OA\Property(
                                        property: 'comment',
                                        type: 'string',
                                        example: 'Great quality, very satisfied!',
                                        description: 'Review text, max 1000 characters'
                                    ),
                                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                    new OA\Property(
                                        property: 'user',
                                        type: 'object',
                                        description: 'Only id and name are loaded — email and other fields are excluded',
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'name', type: 'string', example: 'Phalla Sok'),
                                        ]
                                    ),
                                ]
                            )
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index($productId)
    {
        $reviews = Review::with('user:id,name')
            ->where('product_id', $productId)->latest()->get();

        return response()->json(['success' => true, 'data' => $reviews]);
    }

    #[OA\Post(
        path: '/api/products/{productId}/reviews',
        operationId: 'storeReview',
        summary: 'Submit a review for a product — one review per user per product',
        tags: ['Reviews'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'productId',
                in: 'path',
                required: true,
                description: 'Product ID to review',
                schema: new OA\Schema(type: 'integer', minimum: 1, example: 3)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['rating', 'comment'],
                properties: [
                    new OA\Property(
                        property: 'rating',
                        type: 'integer',
                        minimum: 1,
                        maximum: 5,
                        example: 4,
                        description: 'Required — integer between 1 and 5'
                    ),
                    new OA\Property(
                        property: 'comment',
                        type: 'string',
                        maxLength: 1000,
                        example: 'Great quality, very satisfied!',
                        description: 'Required — string, max 1000 characters'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Review submitted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'product_id', type: 'integer', example: 3),
                                new OA\Property(property: 'user_id', type: 'integer', example: 1),
                                new OA\Property(property: 'rating', type: 'integer', example: 4),
                                new OA\Property(property: 'comment', type: 'string', example: 'Great quality, very satisfied!'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                new OA\Property(
                                    property: 'user',
                                    type: 'object',
                                    description: 'Only id and name are loaded',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 1),
                                        new OA\Property(property: 'name', type: 'string', example: 'Phalla Sok'),
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
                response: 409,
                description: 'Conflict — user has already submitted a review for this product',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'You already reviewed this product'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The rating field is required.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'rating',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The rating must be between 1 and 5.')
                                ),
                                new OA\Property(
                                    property: 'comment',
                                    type: 'array',
                                    items: new OA\Items(type: 'string', example: 'The comment must not be greater than 1000 characters.')
                                ),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

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