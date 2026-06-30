<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UnauthenticatedError',
    type: 'object',
    title: 'UnauthenticatedError',
    description: 'Returned when the Sanctum Bearer token is missing, invalid, or expired',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
    ]
)]
class UnauthenticatedError
{
}
