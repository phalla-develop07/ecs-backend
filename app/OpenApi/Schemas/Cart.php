<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CartItem',
    type: 'object',
    title: 'CartItem',
    description: 'A single line item in a user\'s shopping cart',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'product_id', type: 'integer', example: 3),
        new OA\Property(property: 'quantity', type: 'integer', example: 2),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
        new OA\Property(property: 'product', ref: '#/components/schemas/Product', nullable: true),
    ]
)]
class Cart
{
}
