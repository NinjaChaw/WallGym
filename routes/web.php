<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

// Public storefront and guest shopping. All routes here retain Laravel's web/CSRF middleware.
Route::view('/', 'home')->name('home');
Route::redirect('/product', '/shop');

Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
});

Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::delete('/', 'clear')->name('clear');
    Route::patch('/{product}', 'update')->whereNumber('product')->name('update');
    Route::delete('/{id}', 'destroy')->whereNumber('id')->name('destroy');
});

// Checkout uses the guest's session cart; confirmation is scoped to their session order.
Route::prefix('checkout')->name('checkout.')->controller(CheckoutController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::get('/quote', 'quote')->name('quote');
    Route::get('/success', 'success')->name('success');
});

Route::prefix('admin')->name('admin.')->group(function () {
    // Only login is available without an authenticated admin account.
    Route::middleware('guest:admin')->controller(LoginController::class)->group(function () {
        Route::get('/login', 'create')->name('login');
        Route::post('/login', 'store')->name('login.store');
    });

    // Every route added to admin.php inherits admin authentication automatically.
    Route::middleware('auth:admin')->group(base_path('routes/admin.php'));
});
