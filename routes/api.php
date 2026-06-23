<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReviewController;

// ------- PUBLIC ROUTES ------- //
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::get('/categories',              [CategoryController::class, 'index']);
Route::get('/products/search',         [ProductController::class, 'search']);  // MUST be before /{id}
Route::get('/products',                [ProductController::class, 'index']);
Route::get('/products/{id}',           [ProductController::class, 'show']);
Route::get('/products/{id}/reviews',   [ReviewController::class, 'index']);

// ------- PROTECTED ROUTES ------- //
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::get('/user',     [AuthController::class, 'profile']);

    // Profile
    Route::get('/profile',           [ProfileController::class, 'show']);
    Route::put('/profile',           [ProfileController::class, 'update']);
    Route::put('/profile/password',  [ProfileController::class, 'changePassword']);

    // Wishlist
    Route::get('/wishlist',         [WishlistController::class, 'index']);
    Route::post('/wishlist',        [WishlistController::class, 'store']);
    Route::delete('/wishlist/{id}', [WishlistController::class, 'destroy']);

    // Cart
    Route::get('/cart',            [CartController::class, 'index']);
    Route::post('/cart',           [CartController::class, 'store']);
    Route::put('/cart/{id}',       [CartController::class, 'update']);
    Route::delete('/cart/{id}',    [CartController::class, 'destroy']);

    // Orders
    Route::post('/checkout',       [OrderController::class, 'checkout']);
    Route::get('/orders',          [OrderController::class, 'index']);
    Route::get('/orders/{id}',     [OrderController::class, 'show']);

    // Reviews
    Route::post('/products/{id}/reviews', [ReviewController::class, 'store']);

    // Admin — product management with image upload
    Route::post('/admin/products',        [ProductController::class, 'store']);
    Route::post('/admin/products/{id}',   [ProductController::class, 'update']);  // POST not PUT for multipart
    Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);
});
