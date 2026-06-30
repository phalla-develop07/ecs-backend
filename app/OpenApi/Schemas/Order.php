<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Order',
    type: 'object',
    title: 'Order',
    description: 'A placed order, created from the contents of a user\'s cart at checkout',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'total_amount', type: 'number', format: 'float', example: 89.97),
        new OA\Property(property: 'status', type: 'string', example: 'pending', description: 'Always "pending" on creation'),
        new OA\Property(property: 'address', type: 'string', example: '123 Main St, Phnom Penh'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
        new OA\Property(
            property: 'items',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/OrderItem')
        ),
    ]
)]
class Order
{
}
