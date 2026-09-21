<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

// Loaded by web.php inside the admin prefix, admin. names, and auth:admin middleware.
// Put new admin pages and actions here so they inherit the same protection.
Route::view('/', 'admin.dashboard')->name('dashboard');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::resource('categories', CategoryController::class)->except(['show']);
Route::resource('products', ProductController::class);
Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);

Route::view('/settings', 'admin.settings.index')->name('settings.index');
Route::prefix('content')->name('content.')->group(function () {
    Route::view('/homepage', 'admin.content.homepage')->name('homepage');
    Route::view('/faq', 'admin.content.faq')->name('faq');
});
