<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'My API Documentation',
    description: 'API documentation for my Laravel application',
    contact: new OA\Contact(email: 'admin@example.com'),
    license: new OA\License(name: 'MIT', url: 'https://opensource.org/licenses/MIT')
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: 'API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Enter token in format: Bearer <token>'
)]
#[OA\Schema(
    schema: 'User',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'email', type: 'string', example: 'john@example.com'),
    ]
)]

#[OA\Schema(
    schema: 'Product',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'iPhone 15'),
        new OA\Property(property: 'price', type: 'number', example: 999.99),
    ]
)]

class OpenApi
{
}
