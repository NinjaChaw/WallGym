<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;

Route::get('/', function () {
    return view('home');
});

Route::get('/shop', function () {
    return view('shop');
});

// Front-end product previews; no catalog or checkout backend yet.
Route::view('/product', 'product');
Route::view('/cart', 'cart.index');
Route::view('/checkout', 'checkout.index');
Route::view('/checkout/success', 'checkout.success');
Route::view('/shop/{product}', 'product')
    ->whereIn('product', ['swedish-wall', 'gymnastic-rings', 'exercise-mat']);

//Admin Riutes

// Layout preview only; no authentication or live admin operations yet.
Route::view('/admin', 'admin.dashboard');

//Category route
Route::resource('admin/categories', CategoryController::class)
    ->except(['show'])
    ->names('admin.categories');

Route::view('/admin/products', 'admin.products.index');
Route::view('/admin/products/create', 'admin.products.create');
Route::view('/admin/products/{product}/edit', 'admin.products.edit')->whereNumber('product');
Route::view('/admin/products/{product}', 'admin.products.show')->whereNumber('product');
Route::view('/admin/orders', 'admin.orders.index');
Route::view('/admin/orders/{order}', 'admin.orders.show')->whereNumber('order');
Route::view('/admin/settings', 'admin.settings.index');
Route::view('/admin/content/homepage', 'admin.content.homepage');
Route::view('/admin/content/faq', 'admin.content.faq');
