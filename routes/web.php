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

