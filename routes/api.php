<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Middleware\AdminMiddleware;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ============================================
// API: Admin Product CRUD (Sanctum - Admin)
// ============================================

Route::middleware(['web', AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products/{product}', [ProductController::class, 'show']);
    Route::put('products/{product}', [ProductController::class, 'update']);
    Route::delete('products/{product}', [ProductController::class, 'destroy']);
    Route::post('products/{product}/toggle', [ProductController::class, 'toggle']);
});

// ============================================
// API: Customer Operations (Web Session - Customer)
// ============================================

// Customer Auth (Web session for blade apps)
Route::post('login', [ApiAuthController::class, 'login']);
Route::middleware('auth:web')->post('logout', [ApiAuthController::class, 'logout']);

// Customer Cart Operations (Web session)
Route::middleware('web')->group(function () {
    // Get products (AJAX for customer)
    Route::get('products', [CartController::class, 'apiProducts']);

    // Cart operations
    Route::post('cart/items', [CartController::class, 'addToCart']);
    Route::get('cart', [CartController::class, 'getCart']);
    Route::patch('cart/items/{productId}', [CartController::class, 'updateCartItem']);
    Route::delete('cart/items/{productId}', [CartController::class, 'deleteCartItem']);
    Route::post('cart/checkout', [CartController::class, 'checkout']);
});
