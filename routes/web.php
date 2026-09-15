<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/shop', function () {
    return view('shop');
});

// Front-end product previews; no catalog or checkout backend yet.
Route::view('/product', 'product');
Route::view('/shop/{product}', 'product')
    ->whereIn('product', ['swedish-wall', 'gymnastic-rings', 'exercise-mat']);

//Admin Riutes

// Layout preview only; no authentication or live admin operations yet.
Route::view('/admin', 'admin.dashboard');
Route::view('/admin/categories', 'admin.categories.index');
Route::view('/admin/categories/create', 'admin.categories.create');
Route::view('/admin/categories/{category}/edit', 'admin.categories.edit')->whereNumber('category');
Route::view('/admin/products', 'admin.products.index');
Route::view('/admin/products/create', 'admin.products.create');
Route::view('/admin/products/{product}/edit', 'admin.products.edit')->whereNumber('product');
Route::view('/admin/products/{product}', 'admin.products.show')->whereNumber('product');
Route::view('/admin/orders', 'admin.orders.index');
Route::view('/admin/orders/{order}', 'admin.orders.show')->whereNumber('order');
