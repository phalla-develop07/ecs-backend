<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Product',
    type: 'object',
    title: 'Product',
    description: 'A purchasable product',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'category_id', type: 'integer', example: 2),
        new OA\Property(property: 'name', type: 'string', example: 'Rose Perfume'),
        new OA\Property(property: 'slug', type: 'string', example: 'rose-perfume'),
        new OA\Property(property: 'description', type: 'string', example: 'A lovely floral scent', nullable: true),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99),
        new OA\Property(property: 'stock', type: 'integer', example: 100),
        new OA\Property(property: 'image', type: 'string', example: 'products/abc.jpg', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
        new OA\Property(property: 'category', ref: '#/components/schemas/Category', nullable: true),
    ]
)]
class Product
{
}
