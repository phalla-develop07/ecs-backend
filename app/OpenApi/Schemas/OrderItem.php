<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'OrderItem',
    type: 'object',
    title: 'OrderItem',
    description: 'A single line item within an order. Price is a snapshot taken at checkout time and may differ from the product\'s current price.',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'order_id', type: 'integer', example: 1),
        new OA\Property(property: 'product_id', type: 'integer', example: 3),
        new OA\Property(property: 'quantity', type: 'integer', example: 2),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 29.99, description: 'Price snapshot at the time of checkout'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
        new OA\Property(property: 'product', ref: '#/components/schemas/Product', nullable: true),
    ]
)]
class OrderItem
{
}
