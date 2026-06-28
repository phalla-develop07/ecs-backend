<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/register',
        operationId: 'register',
        summary: 'Register a new user account',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        minLength: 2,
                        maxLength: 255,
                        example: 'Phalla Sok',
                        description: 'Required — letters, spaces, and hyphens only'
                    ),
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        maxLength: 255,
                        example: 'phalla@example.com',
                        description: 'Required — must be unique, valid RFC format with DNS check'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        format: 'password',
                        minLength: 8,
                        example: 'Secret@123',
                        description: 'Required — min 8 chars, must have uppercase, lowercase, number, symbol, and not be a known leaked password'
                    ),
                    new OA\Property(
                        property: 'password_confirmation',
                        type: 'string',
                        format: 'password',
                        example: 'Secret@123',
                        description: 'Required — must exactly match password'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Account created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Account created successfully. Welcome!'),
                        new OA\Property(property: 'token', type: 'string', example: '1|abc123...'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Phalla Sok'),
                                new OA\Property(property: 'email', type: 'string', example: 'phalla@example.com'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Registration failed due to validation errors.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'name', type: 'array', items: new OA\Items(type: 'string', example: 'Full name is required.')),
                                new OA\Property(property: 'email', type: 'array', items: new OA\Items(type: 'string', example: 'This email address is already registered.')),
                                new OA\Property(property: 'password', type: 'array', items: new OA\Items(type: 'string', example: 'Password confirmation does not match.')),
                                new OA\Property(property: 'password_confirmation', type: 'array', items: new OA\Items(type: 'string', example: 'Please confirm your password.')),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error during registration',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Registration failed. Please try again later.'),
                    ]
                )
            ),
        ]
    )]
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\pL\s\-]+$/u',
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
            'password_confirmation' => [
                'required',
                'string',
            ],
        ], [
            'name.required'                  => 'Full name is required.',
            'name.string'                    => 'Name must be a valid string.',
            'name.min'                       => 'Name must be at least 2 characters.',
            'name.max'                       => 'Name must not exceed 255 characters.',
            'name.regex'                     => 'Name may only contain letters, spaces, and hyphens.',
            'email.required'                 => 'Email address is required.',
            'email.string'                   => 'Email must be a valid string.',
            'email.email'                    => 'Please provide a valid email address.',
            'email.max'                      => 'Email must not exceed 255 characters.',
            'email.unique'                   => 'This email address is already registered. Please login or use a different email.',
            'password.required'              => 'Password is required.',
            'password.string'                => 'Password must be a valid string.',
            'password.confirmed'             => 'Password confirmation does not match.',
            'password.min'                   => 'Password must be at least 8 characters long.',
            'password_confirmation.required' => 'Please confirm your password.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed due to validation errors.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::create([
                'name'     => trim($request->name),
                'email'    => strtolower(trim($request->email)),
                'password' => Hash::make($request->password),
            ]);

            $token = $user->createToken('API Token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully. Welcome!',
                'token'   => $token,
                'user'    => [
                    'id'         => $user->id,
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'created_at' => $user->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again later.',
            ], 500);
        }
    }

    #[OA\Post(
        path: '/api/login',
        operationId: 'login',
        summary: 'Login and receive a Sanctum Bearer token',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        maxLength: 255,
                        example: 'phalla@example.com',
                        description: 'Required — valid email format, max 255 characters'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        format: 'password',
                        minLength: 8,
                        maxLength: 128,
                        example: 'Secret@123',
                        description: 'Required — min 8, max 128 characters (DoS protection)'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful — returns Sanctum token',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Login successful. Welcome back!'),
                        new OA\Property(property: 'token', type: 'string', example: '2|xyz789...'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Phalla Sok'),
                                new OA\Property(property: 'email', type: 'string', example: 'phalla@example.com'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Invalid credentials — vague message to prevent user enumeration',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'These credentials do not match our records.'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Login failed due to validation errors.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'email', type: 'array', items: new OA\Items(type: 'string', example: 'Email address is required.')),
                                new OA\Property(property: 'password', type: 'array', items: new OA\Items(type: 'string', example: 'Password must be at least 8 characters.')),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: 'Too many login attempts — rate limited (5 attempts per 60s per IP + email)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Too many login attempts. Please try again in 45 seconds.'),
                        new OA\Property(property: 'retry_after', type: 'integer', example: 45, description: 'Seconds remaining before the rate limit resets'),
                    ]
                )
            ),
        ]
    )]
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => [
                'required',
                'string',
                'email:rfc',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:128',
            ],
        ], [
            'email.required'    => 'Email address is required.',
            'email.string'      => 'Email must be a valid string.',
            'email.email'       => 'Please provide a valid email address.',
            'email.max'         => 'Email must not exceed 255 characters.',
            'password.required' => 'Password is required.',
            'password.string'   => 'Password must be a valid string.',
            'password.min'      => 'Password must be at least 8 characters.',
            'password.max'      => 'Password must not exceed 128 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed due to validation errors.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $throttleKey = 'login:' . Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return response()->json([
                'success'     => false,
                'message'     => "Too many login attempts. Please try again in {$seconds} seconds.",
                'retry_after' => $seconds,
            ], 429);
        }

        $email = strtolower(trim($request->input('email')));
        $user  = User::where('email', $email)->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            return response()->json([
                'success' => false,
                'message' => 'These credentials do not match our records.',
            ], 401);
        }

        RateLimiter::clear($throttleKey);

        $user->tokens()->delete();

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful. Welcome back!',
            'token'   => $token,
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'created_at' => $user->created_at,
            ],
        ], 200);
    }

    #[OA\Get(
        path: '/api/auth/profile',
        operationId: 'authProfile',
        summary: 'Get the currently authenticated user',
        tags: ['Auth'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profile retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Profile retrieved successfully.'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Phalla Sok'),
                                new OA\Property(property: 'email', type: 'string', example: 'phalla@example.com'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated — token missing, invalid, or expired',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated. Please login to access your profile.'),
                    ]
                )
            ),
        ]
    )]
    public function profile(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login to access your profile.',
            ], 401);
        }

        if (! $user instanceof \App\Models\User) {
            return response()->json([
                'success' => false,
                'message' => 'User account could not be resolved. Please login again.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully.',
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ], 200);
    }

    #[OA\Post(
        path: '/api/logout',
        operationId: 'logout',
        summary: 'Logout — revoke current token or all tokens across all devices',
        tags: ['Auth'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'logout_all',
                        type: 'boolean',
                        example: false,
                        nullable: true,
                        description: 'Optional — set true to revoke ALL tokens across every device. Defaults to false (current token only).'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logged out successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'You have been logged out successfully.',
                            description: 'Message varies: "logged out successfully" (single) or "logged out from all devices" (logout_all=true)'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated — no active session found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated. No active session found.'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed — logout_all must be a boolean',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Invalid request parameters.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'logout_all', type: 'array', items: new OA\Items(type: 'string', example: 'The logout_all field must be true or false.')),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: 'Server error during logout',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string', example: 'Logout failed. Please try again.'),
                    ]
                )
            ),
        ]
    )]
    public function logout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logout_all' => [
                'sometimes',
                'boolean',
            ],
        ], [
            'logout_all.boolean' => 'The logout_all field must be true or false.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request parameters.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. No active session found.',
            ], 401);
        }

        try {
            $logoutAll = filter_var($request->input('logout_all', false), FILTER_VALIDATE_BOOLEAN);

            if ($logoutAll) {
                $user->tokens()->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'You have been logged out from all devices successfully.',
                ], 200);
            }

            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'You have been logged out successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed. Please try again.',
            ], 500);
        }
    }
}