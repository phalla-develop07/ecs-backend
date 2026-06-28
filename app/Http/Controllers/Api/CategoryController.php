<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    #[OA\Get(
        path: '/api/categories',
        operationId: 'getCategories',
        summary: 'Get all categories',
        tags: ['Categories'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Categories retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Categories are gotten successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Perfumes'),
                                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                    new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                ]
                            )
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index()
    {
        $categories = Category::all();

        return response()->json([
            'success' => true,
            'message' => 'Categories are gotten successfully',
            'data'    => $categories,
        ]);
    }
}