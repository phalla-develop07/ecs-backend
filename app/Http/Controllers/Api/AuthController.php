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
        // ── 1. Validate input structure ------- 
        $validator = Validator::make($request->all(), [
            'email'    => [
                'required',
                'string',
                'email:rfc',        // Format check (skip DNS here — login must be fast)
                'max:255',          // Prevent oversized payloads
            ],
            'password' => [
                'required',
                'string',
                'min:8',            // Mirrors the minimum set during registration
                'max:128',          // Prevent extremely long password DoS attacks
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

        // ── 2. Rate limiting — 5 attempts / 60 s per IP + email combo ──────────
        $throttleKey = 'login:' . Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return response()->json([
                'success'        => false,
                'message'        => "Too many login attempts. Please try again in {$seconds} seconds.",
                'retry_after'    => $seconds,   // Lets the Vue frontend show a countdown timer
            ], 429);
        }

        // ── 3. Lookup + constant-time password check ───────────────────────────
        $email = strtolower(trim($request->input('email')));
        $user  = User::where('email', $email)->first();

        // Deliberate vague message — prevents user enumeration
        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            RateLimiter::hit($throttleKey, 60);

            return response()->json([
                'success' => false,
                'message' => 'These credentials do not match our records.',
            ], 401);
        }

        // ── 4. Clear rate limit on success ─────────────────────────────────────
        RateLimiter::clear($throttleKey);

        // ── 5. Single-session enforcement — revoke all previous tokens ─────────
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

    /**
     * Current User Profile
     */
    public function profile(Request $request)
    {
        // ── 1. Validate any optional query parameters ──────────────────────────
        //    profile() accepts no body, but we still guard unexpected inputs.
        $validator = Validator::make($request->all(), [
            // No body fields expected — reject anything suspicious
        ]);

        // ── 2. The auth:sanctum middleware already rejects unauthenticated
        //    requests with 401, but we add an explicit guard for safety
        //    in case this route is ever mistakenly left unprotected.
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login to access your profile.',
            ], 401);
        }

        // ── 3. Validate that the resolved user is a proper User model ──────────
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

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        // ── 1. Validate the optional logout_all flag ───────────────────────────
        $validator = Validator::make($request->all(), [
            'logout_all' => [
                'sometimes',    // Only validated if present
                'boolean',      // Must be true / false / 1 / 0
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

        // ── 2. Guard — middleware handles this, but explicit check adds safety ──
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
                // Revoke ALL tokens across every device / session
                $user->tokens()->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'You have been logged out from all devices successfully.',
                ], 200);
            }

            // Revoke only the current token (default behaviour)
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
