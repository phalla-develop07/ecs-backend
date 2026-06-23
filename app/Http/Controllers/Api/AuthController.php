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

class AuthController extends Controller
{
    /**
     * Register
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[\pL\s\-]+$/u', // Only letters, spaces, hyphens
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',           // Validate format + DNS record exists
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',               // Requires password_confirmation field
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),     // Check against known leaked passwords (HaveIBeenPwned)
            ],
            'password_confirmation' => [
                'required',
                'string',
            ],
        ], [
            // Custom error messages
            'name.required'           => 'Full name is required.',
            'name.string'             => 'Name must be a valid string.',
            'name.min'                => 'Name must be at least 2 characters.',
            'name.max'                => 'Name must not exceed 255 characters.',
            'name.regex'              => 'Name may only contain letters, spaces, and hyphens.',

            'email.required'          => 'Email address is required.',
            'email.string'            => 'Email must be a valid string.',
            'email.email'             => 'Please provide a valid email address.',
            'email.max'               => 'Email must not exceed 255 characters.',
            'email.unique'            => 'This email address is already registered. Please login or use a different email.',

            'password.required'       => 'Password is required.',
            'password.string'         => 'Password must be a valid string.',
            'password.confirmed'      => 'Password confirmation does not match.',
            'password.min'            => 'Password must be at least 8 characters long.',

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

    /**
     * Login
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Email address is required.',
            'email.string'      => 'Email must be a valid string.',
            'email.email'       => 'Please provide a valid email address.',
            'password.required' => 'Password is required.',
            'password.string'   => 'Password must be a valid string.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed due to validation errors.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Rate limiting: max 5 attempts per minute per IP+email combo
        $throttleKey = Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        $user = User::where('email', strtolower(trim($request->email)))->first();

        // Deliberate vague message to avoid user enumeration
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60); // Count failed attempt

            return response()->json([
                'success' => false,
                'message' => 'These credentials do not match our records.',
            ], 401);
        }

        // Clear rate limit on successful login
        RateLimiter::clear($throttleKey);

        // Revoke old tokens (single-session policy); remove if multi-device is needed
        $user->tokens()->delete();

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful. Welcome back!',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ], 200);
    }

    /**
     * Current User Profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login to access your profile.',
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

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. No active session found.',
            ], 401);
        }

        try {
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