<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\CartController;

Route::get('/', function () {
    return redirect()->route('login');
});

// ============================================
// SINGLE LOGIN PAGE (Login page for both Customer and Admin)
// ============================================

Route::middleware('guest')->group(function () {
    // Single Login Page
    Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [CustomerAuthController::class, 'login']);
});

// ============================================
// CUSTOMER ROUTES (Protected)
// ============================================

Route::middleware('auth:web')->group(function () {
    // Product List (Customer)
    Route::get('/products', [CartController::class, 'productListing'])->name('customer.products');

    // Cart (Customer)
    Route::get('/cart', [CartController::class, 'showCart'])->name('customer.cart');

    // Logout (Shared logout)
    Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
});

// ============================================
// ADMIN ROUTES (Protected)
// ============================================

Route::prefix('admin')->group(function () {
    // Admin Protected Routes
    Route::middleware(['auth:web', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
        Route::get('/products', [AdminProductController::class, 'indexBlade'])->name('admin.products.list');
    });
});

// ============================================
// DEFAULT LARAVEL ROUTES (Unchanged)
// ============================================

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
