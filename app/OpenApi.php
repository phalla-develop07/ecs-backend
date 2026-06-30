<?php

namespace App;

use OpenApi\Attributes as OA;

/**
 * Global OpenAPI metadata only.
 *
 * Individual model schemas now live in their own files under
 * app/OpenApi/Schemas/ (one file per table/resource), so this class
 * stays small and only holds things that aren't tied to a single model:
 * API info, the server list, and the Sanctum security scheme.
 *
 * swagger-php scans every PHP file in the configured paths for
 * #[OA\...] attributes regardless of which class they sit on, so
 * splitting schemas into separate files requires no extra config —
 * as long as those files are inside the path(s) your l5-swagger /
 * openapi config already scans (typically the whole app/ directory).
 */
#[OA\Info(
    version: '1.0.0',
    title: 'ECS Shop API Documentation',
    description: 'API documentation for the ECS Shop e-commerce application (Laravel + Sanctum backend).',
    contact: new OA\Contact(email: 'admin@example.com'),
    license: new OA\License(name: 'MIT', url: 'https://opensource.org/licenses/MIT')
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000',
    description: 'Local development server'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum Token',
    description: "Enter the Sanctum token only — Swagger UI adds the 'Bearer ' prefix automatically. Obtain a token via /api/login or /api/register."
)]
class OpenApi
{
}
