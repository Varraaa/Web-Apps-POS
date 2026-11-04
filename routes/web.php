<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDiscountController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminPosController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;

Route::middleware('buyer')->group(function (): void {
    Route::get('/', [StoreController::class, 'index'])->name('home');
    Route::get('/about', [StoreController::class, 'about'])->name('about');
    Route::get('/collection', [StoreController::class, 'collection'])->name('collection');
    Route::get('/shop', [StoreController::class, 'collection'])->name('shop');
    Route::get('/product/{product:slug}', [StoreController::class, 'show'])->name('products.show');
    Route::get('/buy/{product:slug}', [StoreController::class, 'buy'])->name('products.buy');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::get('/tracking', [OrderTrackingController::class, 'index'])->name('tracking.index');
    Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{product}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::delete('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/pos', [AdminPosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [AdminPosController::class, 'checkout'])->name('pos.checkout');
    Route::get('/pos/receipt/{order}', [AdminPosController::class, 'receipt'])->name('pos.receipt');
    Route::resource('products', AdminProductController::class)->except(['show']);
    Route::resource('discounts', AdminDiscountController::class)->except(['show']);
    Route::patch('/discounts/{discount}/toggle', [AdminDiscountController::class, 'toggle'])->name('discounts.toggle');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('/orders/{order}/packing-slip', [AdminOrderController::class, 'packingSlip'])->name('orders.packing-slip');
    Route::patch('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
});
