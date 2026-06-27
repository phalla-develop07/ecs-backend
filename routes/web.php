<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WishlistController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

Route::prefix('admin')->name('admin.')->group(function () {

    // --- Public (no middleware) ---
    Route::get('/login', fn() => view('admin.auth.login'))->name('login');

    Route::post('/login', function (Request $request) {

        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email address is required.',
            'email.email'       => 'Please provide a valid email address.',
            'password.required' => 'Password is required.',
        ]);

        // ✅ Correct Auth usage
        if (Auth::attempt($credentials)) {

            $user = Auth::user();

            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->with('error', 'Access denied. Admins only.');
            }

            $request->session()->regenerate();

            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withInput($request->only('email'))
            ->with('error', 'Invalid email or password.');
    })->name('login.post');

    Route::post('/logout', function (Request $request) {

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    })->name('logout');

    // --- Protected (admin middleware) ---
    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('categories', CategoryController::class);
        Route::resource('products', ProductController::class);
        Route::resource('orders', OrderController::class);
        Route::resource('users', UserController::class);
        Route::resource('wishlists', WishlistController::class);
        Route::resource('reviews', ReviewController::class);
    });
});