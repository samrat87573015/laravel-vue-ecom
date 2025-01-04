<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AttributeController;

Route::get('/', [ContactController::class, 'Home'])->name('home');

Route::post('/store-contact', [ContactController::class, 'store'])->name('contact.store');


//route group for authenticated user
Route::group(['middleware' => 'auth'], function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/admin-products', [ProductController::class, 'index'])->name('admin.products.index');

    Route::get('/admin-products/create', [ProductController::class, 'create'])->name('admin.product.create');

    Route::post('/store-products', [ProductController::class, 'store'])->name('products.store');

    Route::delete('/admin-product-delete/{id}', [ProductController::class, 'destroy'])->name('admin.product.delete');

});



Route::get('/products', [ProductController::class, 'index'])->name('products.index');

Route::get('/product/{id}', [ProductController::class, 'show'])->name('products.show');

Route::post('/add-to-cart', [CartController::class, 'addToCart'])->name('products.addToCart');

Route::get('/cart-list', [CartController::class, 'getCart'])->name('products.cartList');

Route::post('/delete-cart-item', [CartController::class, 'deleteCartItem'])->name('products.deleteCartItem');


Route::get('/attributes/create', [AttributeController::class, 'Create'])->name('admin.attributes.create');

Route::post('/store-attributes', [AttributeController::class, 'store'])->name('attributes.store');

Route::get('/admin-attribute-delete/{id}', [AttributeController::class, 'destroy'])->name('admin.attribute.delete');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
