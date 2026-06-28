<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        operationId: 'getProducts',
        summary: 'Get all products (paginated, 12 per page)',
        tags: ['Products'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Products retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Products are gotten successfully'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                new OA\Property(property: 'total', type: 'integer', example: 50),
                                new OA\Property(property: 'per_page', type: 'integer', example: 12),
                                new OA\Property(
                                    property: 'data',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'category_id', type: 'integer', example: 2),
                                            new OA\Property(property: 'name', type: 'string', example: 'Rose Perfume'),
                                            new OA\Property(property: 'slug', type: 'string', example: 'rose-perfume'),
                                            new OA\Property(property: 'description', type: 'string', example: 'A lovely scent'),
                                            new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
                                            new OA\Property(property: 'stock', type: 'integer', example: 100),
                                            new OA\Property(property: 'image', type: 'string', example: 'products/abc.jpg', nullable: true),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(12);

        return response()->json([
            'success' => true,
            'message' => 'Products are gotten successfully',
            'data'    => $products,
        ]);
    }

    #[OA\Get(
        path: '/api/products/{id}',
        operationId: 'getProduct',
        summary: 'Get a single product by ID',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Product ID — must be a positive integer',
                schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Rose Perfume'),
                                new OA\Property(property: 'slug', type: 'string', example: 'rose-perfume'),
                                new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
                                new OA\Property(property: 'stock', type: 'integer', example: 100),
                                new OA\Property(property: 'image', type: 'string', nullable: true),
                                new OA\Property(
                                    property: 'category',
                                    type: 'object',
                                    properties: [
                                        new OA\Property(property: 'id', type: 'integer', example: 2),
                                        new OA\Property(property: 'name', type: 'string', example: 'Perfumes'),
                                    ]
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Product not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Product not found'),
                    ]
                )
            ),
        ]
    )]
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $product,
        ]);
    }

    #[OA\Get(
        path: '/api/products/search',
        operationId: 'searchProducts',
        summary: 'Search products by name or description',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'q',
                in: 'query',
                required: true,
                description: 'Search keyword — required, searches both name and description fields',
                schema: new OA\Schema(type: 'string', minLength: 1, example: 'perfume')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Matching products returned (paginated)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'total', type: 'integer', example: 3),
                                new OA\Property(
                                    property: 'data',
                                    type: 'array',
                                    items: new OA\Items(
                                        properties: [
                                            new OA\Property(property: 'id', type: 'integer', example: 1),
                                            new OA\Property(property: 'name', type: 'string', example: 'Rose Perfume'),
                                            new OA\Property(property: 'price', type: 'number', example: 29.99),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Search keyword is missing',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Search keyword is required'),
                    ]
                )
            ),
        ]
    )]
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Search keyword is required',
            ], 422);
        }

        $products = Product::with('category')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data'    => $products,
        ]);
    }

    #[OA\Post(
        path: '/api/admin/products',
        operationId: 'storeProduct',
        summary: 'Create a new product (admin only)',
        tags: ['Admin – Products'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['category_id', 'name', 'price', 'stock'],
                    properties: [
                        new OA\Property(
                            property: 'category_id',
                            type: 'integer',
                            example: 1,
                            description: 'Required — must exist in categories table'
                        ),
                        new OA\Property(
                            property: 'name',
                            type: 'string',
                            maxLength: 255,
                            example: 'Rose Perfume',
                            description: 'Required — max 255 characters'
                        ),
                        new OA\Property(
                            property: 'description',
                            type: 'string',
                            example: 'A lovely floral scent',
                            nullable: true,
                            description: 'Optional — any text'
                        ),
                        new OA\Property(
                            property: 'price',
                            type: 'number',
                            format: 'float',
                            minimum: 0,
                            example: 29.99,
                            description: 'Required — numeric, minimum 0'
                        ),
                        new OA\Property(
                            property: 'stock',
                            type: 'integer',
                            minimum: 0,
                            example: 100,
                            description: 'Required — integer, minimum 0'
                        ),
                        new OA\Property(
                            property: 'image',
                            type: 'string',
                            format: 'binary',
                            nullable: true,
                            description: 'Optional — jpg/jpeg/png/webp, max 2MB'
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Product created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product created'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 10),
                                new OA\Property(property: 'name', type: 'string', example: 'Rose Perfume'),
                                new OA\Property(property: 'slug', type: 'string', example: 'rose-perfume'),
                                new OA\Property(property: 'price', type: 'number', example: 29.99),
                                new OA\Property(property: 'stock', type: 'integer', example: 100),
                                new OA\Property(property: 'image', type: 'string', nullable: true, example: 'products/abc.jpg'),
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
                        new OA\Property(property: 'message', type: 'string', example: 'The category_id field is required.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'category_id', type: 'array', items: new OA\Items(type: 'string')),
                                new OA\Property(property: 'name', type: 'array', items: new OA\Items(type: 'string')),
                                new OA\Property(property: 'price', type: 'array', items: new OA\Items(type: 'string')),
                                new OA\Property(property: 'stock', type: 'array', items: new OA\Items(type: 'string')),
                                new OA\Property(property: 'image', type: 'array', items: new OA\Items(type: 'string')),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'category_id' => $validated['category_id'],
            'name'        => $validated['name'],
            'slug'        => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'price'       => $validated['price'],
            'stock'       => $validated['stock'],
            'image'       => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product created',
            'data'    => $product->load('category'),
        ], 201);
    }

    #[OA\Post(
        path: '/api/admin/products/{id}',
        operationId: 'updateProduct',
        summary: 'Update a product (admin only) — use POST with _method=PUT for multipart',
        tags: ['Admin – Products'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Product ID — must exist',
                schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            property: '_method',
                            type: 'string',
                            example: 'PUT',
                            description: 'Required for multipart — Laravel method spoofing'
                        ),
                        new OA\Property(
                            property: 'category_id',
                            type: 'integer',
                            example: 2,
                            description: 'Optional — must exist in categories table'
                        ),
                        new OA\Property(
                            property: 'name',
                            type: 'string',
                            maxLength: 255,
                            example: 'Updated Perfume Name',
                            description: 'Optional — max 255 characters. Slug is auto-regenerated.'
                        ),
                        new OA\Property(
                            property: 'description',
                            type: 'string',
                            nullable: true,
                            example: 'Updated description',
                            description: 'Optional — any text, nullable'
                        ),
                        new OA\Property(
                            property: 'price',
                            type: 'number',
                            format: 'float',
                            minimum: 0,
                            example: 49.99,
                            description: 'Optional — numeric, minimum 0'
                        ),
                        new OA\Property(
                            property: 'stock',
                            type: 'integer',
                            minimum: 0,
                            example: 50,
                            description: 'Optional — integer, minimum 0'
                        ),
                        new OA\Property(
                            property: 'image',
                            type: 'string',
                            format: 'binary',
                            nullable: true,
                            description: 'Optional — jpg/jpeg/png/webp, max 2MB. Replaces old image.'
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product updated'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Updated Perfume Name'),
                                new OA\Property(property: 'slug', type: 'string', example: 'updated-perfume-name'),
                                new OA\Property(property: 'price', type: 'number', example: 49.99),
                                new OA\Property(property: 'stock', type: 'integer', example: 50),
                                new OA\Property(property: 'image', type: 'string', nullable: true),
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
                description: 'Product not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Product not found'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The price must be at least 0.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'category_id', type: 'array', items: new OA\Items(type: 'string')),
                                new OA\Property(property: 'name', type: 'array', items: new OA\Items(type: 'string')),
                                new OA\Property(property: 'price', type: 'array', items: new OA\Items(type: 'string')),
                                new OA\Property(property: 'stock', type: 'array', items: new OA\Items(type: 'string')),
                                new OA\Property(property: 'image', type: 'array', items: new OA\Items(type: 'string')),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        $validated = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'stock'       => 'sometimes|integer|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated',
            'data'    => $product->load('category'),
        ]);
    }

    #[OA\Delete(
        path: '/api/admin/products/{id}',
        operationId: 'deleteProduct',
        summary: 'Delete a product (admin only)',
        tags: ['Admin – Products'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Product ID — must exist',
                schema: new OA\Schema(type: 'integer', minimum: 1, example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Product deleted successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Product deleted'),
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
                description: 'Product not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Product not found'),
                    ]
                )
            ),
        ]
    )]
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
            ], 404);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted',
        ]);
    }
}
