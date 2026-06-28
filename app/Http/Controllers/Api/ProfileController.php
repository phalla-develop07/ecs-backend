<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    #[OA\Get(
        path: '/api/profile',
        operationId: 'getProfile',
        summary: 'Get the authenticated user\'s full profile',
        tags: ['Profile'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profile retrieved successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Your profile is gotten successfully'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Phalla Sok'),
                                new OA\Property(property: 'email', type: 'string', format: 'email', example: 'phalla@example.com'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated — Bearer token missing or invalid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
        ]
    )]
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Your profile is gotten successfully',
            'user'    => $request->user(),
        ]);
    }

    #[OA\Put(
        path: '/api/profile',
        operationId: 'updateProfile',
        summary: 'Update the authenticated user\'s name and email',
        tags: ['Profile'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email'],
                properties: [
                    new OA\Property(
                        property: 'name',
                        type: 'string',
                        maxLength: 255,
                        example: 'Phalla Sok',
                        description: 'Required — string, max 255 characters'
                    ),
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        example: 'new@example.com',
                        description: 'Required — must be a valid email and unique across all users except the current user'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Profile updated successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Your profile is updated successfully'),
                        new OA\Property(
                            property: 'user',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Phalla Sok'),
                                new OA\Property(property: 'email', type: 'string', example: 'new@example.com'),
                                new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                                new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2024-01-01T00:00:00.000000Z'),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated — Bearer token missing or invalid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The name field is required.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'name', type: 'array', items: new OA\Items(type: 'string', example: 'The name field is required.')),
                                new OA\Property(property: 'email', type: 'array', items: new OA\Items(type: 'string', example: 'The email has already been taken.')),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        $user->update($validated);
        return response()->json([
            'success' => true,
            'message' => 'Your profile is updated successfully',
            'user'    => $user,
        ]);
    }

    #[OA\Put(
        path: '/api/profile/password',
        operationId: 'changePassword',
        summary: 'Change the authenticated user\'s password',
        tags: ['Profile'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['current_password', 'new_password', 'new_password_confirmation'],
                properties: [
                    new OA\Property(
                        property: 'current_password',
                        type: 'string',
                        format: 'password',
                        example: 'OldSecret@123',
                        description: 'Required — must match the user\'s current password'
                    ),
                    new OA\Property(
                        property: 'new_password',
                        type: 'string',
                        format: 'password',
                        minLength: 6,
                        example: 'NewSecret@456',
                        description: 'Required — min 6 characters, must match new_password_confirmation'
                    ),
                    new OA\Property(
                        property: 'new_password_confirmation',
                        type: 'string',
                        format: 'password',
                        example: 'NewSecret@456',
                        description: 'Required — must exactly match new_password'
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Password changed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Password changed successfully'),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated — Bearer token missing or invalid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Unauthenticated.'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation failed — either field rules or incorrect current password',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The given data was invalid.'),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            properties: [
                                new OA\Property(
                                    property: 'current_password',
                                    type: 'array',
                                    items: new OA\Items(
                                        type: 'string',
                                        example: 'Current password is incorrect.'
                                    ),
                                    description: 'Thrown via ValidationException when Hash::check() fails'
                                ),
                                new OA\Property(
                                    property: 'new_password',
                                    type: 'array',
                                    items: new OA\Items(
                                        type: 'string',
                                        example: 'The new password must be at least 6 characters.'
                                    )
                                ),
                                new OA\Property(
                                    property: 'new_password_confirmation',
                                    type: 'array',
                                    items: new OA\Items(
                                        type: 'string',
                                        example: 'The new password confirmation does not match.'
                                    )
                                ),
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Current password is incorrect.'],
            ]);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        return response()->json(['success' => true, 'message' => 'Password changed successfully']);
    }
}