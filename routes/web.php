<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SurpriseBiteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CartController;

// Programer abid

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
Route::get('/login/seller', [AuthController::class, 'showSellerLogin'])->name('login.seller');
Route::post('/login/seller', [AuthController::class, 'sellerLogin'])->name('login.seller.submit');
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
    Route::get('/admin/transactions', [SurpriseBiteController::class, 'adminTransactions'])->name('admin.transactions');
});

// Mitra Routes
Route::middleware(['auth', 'role:mitra'])->prefix('mitra')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Mitra\MitraDashboardController::class, 'index'])->name('mitra.dashboard');
    Route::post('/restaurants', [\App\Http\Controllers\Mitra\RestaurantController::class, 'store'])->name('mitra.restaurants.store');
    
    Route::get('/restaurants/{restaurant}/unlock', [\App\Http\Controllers\Mitra\RestaurantAccessController::class, 'showUnlockForm'])->name('mitra.restaurants.unlock.form');
    Route::post('/restaurants/{restaurant}/unlock', [\App\Http\Controllers\Mitra\RestaurantAccessController::class, 'unlock'])->name('mitra.restaurants.unlock');
    Route::post('/restaurants/{restaurant}/lock', [\App\Http\Controllers\Mitra\RestaurantAccessController::class, 'lock'])->name('mitra.restaurants.lock');

    // Locked Routes
    Route::middleware('restaurant.unlocked')->group(function () {
        Route::get('/restaurants/{restaurant}/manage', [\App\Http\Controllers\Mitra\RestaurantController::class, 'manage'])->name('mitra.restaurants.manage');
        Route::resource('restaurants.menus', \App\Http\Controllers\Mitra\MenuController::class);
        Route::resource('restaurants.orders', \App\Http\Controllers\Mitra\OrderController::class)->only(['index', 'show', 'update']);
    });
});
