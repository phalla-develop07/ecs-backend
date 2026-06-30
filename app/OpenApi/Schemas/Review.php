<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Review',
    type: 'object',
    title: 'Review',
    description: 'A user-submitted product review — one per user per product',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'product_id', type: 'integer', example: 3),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'rating', type: 'integer', minimum: 1, maximum: 5, example: 4, description: 'Star rating from 1 (lowest) to 5 (highest)'),
        new OA\Property(property: 'comment', type: 'string', maxLength: 1000, example: 'Great quality, very satisfied!'),
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
)]
class Review
{
}
