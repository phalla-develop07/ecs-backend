<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'NotFoundError',
    type: 'object',
    title: 'NotFoundError',
    description: 'Returned when the requested resource does not exist or does not belong to the authenticated user',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'Not found'),
    ]
)]
class NotFoundError
{
}
