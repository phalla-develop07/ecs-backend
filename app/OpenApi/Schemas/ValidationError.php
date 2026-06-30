<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ValidationError',
    type: 'object',
    title: 'ValidationError',
    description: 'Returned when request validation fails (HTTP 422)',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            description: 'Map of field name to array of error message strings',
            example: ['email' => ['The email field is required.']]
        ),
    ]
)]
class ValidationError
{
}
