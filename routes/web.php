<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;

Route::get('/', function () {
    return view('home');
});

Route::get('/shop', [\App\Http\Controllers\ShopController::class, 'index'])->name('shop.index');

// The legacy product preview now leads to the live catalog.
Route::redirect('/product', '/shop');
Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [\App\Http\Controllers\CartController::class, 'store'])->name('cart.store');
Route::patch('/cart/{product}', [\App\Http\Controllers\CartController::class, 'update'])->whereNumber('product')->name('cart.update');
Route::delete('/cart', [\App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
Route::delete('/cart/{id}', [\App\Http\Controllers\CartController::class, 'destroy'])->whereNumber('id')->name('cart.destroy');
Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/quote', [\App\Http\Controllers\CheckoutController::class, 'quote'])->name('checkout.quote');
Route::get('/checkout/success', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/shop/{slug}', [\App\Http\Controllers\ProductController::class, 'show'])->name('shop.show');

//Admin Riutes

// Layout preview only; no authentication or live admin operations yet.
Route::view('/admin', 'admin.dashboard');

//Category route
Route::resource('admin/categories', CategoryController::class)
    ->except(['show'])
    ->names('admin.categories');

//Admin route
Route::resource('admin/products', ProductController::class)
    ->names('admin.products');

Route::resource('admin/orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index', 'show', 'update'])->names('admin.orders');
Route::view('/admin/settings', 'admin.settings.index');
Route::view('/admin/content/homepage', 'admin.content.homepage');
Route::view('/admin/content/faq', 'admin.content.faq');
