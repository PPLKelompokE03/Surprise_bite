<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SurpriseBiteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CartController;

Route::get('/', [SurpriseBiteController::class, 'home'])->name('home');
Route::get('/browse', [SurpriseBiteController::class, 'browse'])->name('browse');
Route::get('/impact', [SurpriseBiteController::class, 'impact'])->name('impact');
Route::get('/about', [SurpriseBiteController::class, 'about'])->name('about');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::get('/login/admin', [AuthController::class, 'showAdminLogin'])->name('login.admin');
Route::post('/login/admin', [AuthController::class, 'adminLogin'])->name('login.admin.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/boxes/{slug}', [SurpriseBiteController::class, 'box'])->name('boxes.show');

Route::middleware('customer')->group(function () {
    // Cart Routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/item/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/item/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/data', [CartController::class, 'getCartData'])->name('cart.data');
    Route::post('/cart/validate-checkout', [CartController::class, 'validateForCheckout'])->name('cart.validate-checkout');

    Route::get('/checkout/{slug}', [SurpriseBiteController::class, 'checkoutDelivery'])->name('checkout.delivery');
    Route::post('/checkout/{slug}/delivery', [SurpriseBiteController::class, 'checkoutDeliverySubmit'])->name('checkout.delivery.submit');
    Route::get('/checkout/{slug}/payment', [SurpriseBiteController::class, 'checkoutPayment'])->name('checkout.payment');
    Route::post('/checkout/{slug}/pay', [SurpriseBiteController::class, 'checkoutPay'])->name('checkout.pay');
    Route::get('/checkout/{slug}/success', [SurpriseBiteController::class, 'checkoutSuccess'])->name('checkout.success');

    // Xendit Payment Routes
    Route::get('/payment/checkout/{order_id}', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/success/{order_id}', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/failed/{order_id}', [PaymentController::class, 'failed'])->name('payment.failed');
    Route::get('/payment/status/{order_id}', [PaymentController::class, 'checkStatus'])->name('payment.status');
});

// Midtrans Webhook (public, tidak perlu auth)
Route::post('/webhook/midtrans', [PaymentController::class, 'webhook'])->name('webhook.midtrans');

Route::middleware('admin')->group(function () {
    Route::get('/admin', [SurpriseBiteController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/admin/impact', [SurpriseBiteController::class, 'adminImpact'])->name('admin.impact');
});
