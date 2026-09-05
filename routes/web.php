<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaystackPaymentController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProductQrController;
use App\Http\Controllers\PublicStorageController;
use App\Http\Controllers\SecurityPortalController;
use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/storage/{path}', [PublicStorageController::class, 'show'])
    ->where('path', '.*')
    ->name('storage.public');

Route::get('/', [CustomerController::class, 'home'])->name('home');
Route::get('/shop', [CustomerController::class, 'shop'])->name('shop');
Route::get('/scan', [CustomerController::class, 'scan'])->name('scan');
Route::redirect('/navigation', '/shop')->name('navigation');
Route::get('/cart', [CustomerController::class, 'cart'])->name('cart');
Route::middleware('auth')->get('/history', [CustomerController::class, 'history'])->name('history');
Route::get('/checkout', [CustomerController::class, 'checkout'])->name('checkout');
Route::get('/products/{product}/qr-code', [ProductQrController::class, 'show'])->name('products.qr');
Route::get('/products/{product}', [CustomerController::class, 'product'])->name('products.show');
Route::get('/payments/paystack/callback', [PaystackPaymentController::class, 'callback'])->name('payments.paystack.callback');

Route::get('/login', [WebAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::get('/register', [WebAuthController::class, 'showRegister'])->name('register');
Route::post('/register', [WebAuthController::class, 'register']);
Route::get('/forgot-password', [PasswordController::class, 'showForgot'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [PasswordController::class, 'showReset'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'reset'])->middleware('guest')->name('password.update');
Route::get('/verify-email-otp', [WebAuthController::class, 'showOtpVerification'])->name('verification.otp.notice');
Route::post('/verify-email-otp', [WebAuthController::class, 'verifyOtp'])->name('verification.otp.verify');
Route::post('/verify-email-otp/resend', [WebAuthController::class, 'resendOtp'])->name('verification.otp.resend');
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [PasswordController::class, 'showChange'])->name('password.change');
    Route::post('/change-password', [PasswordController::class, 'change'])->name('password.change.update');
});

Route::middleware('auth')->prefix('security')->group(function () {
    Route::get('/', [SecurityPortalController::class, 'index'])->name('security.index');
    Route::post('/lookup', [SecurityPortalController::class, 'lookup'])->name('security.lookup');
    Route::get('/receipts/{token}', [SecurityPortalController::class, 'show'])->name('security.receipts.show');
    Route::post('/receipts/{token}/verify', [SecurityPortalController::class, 'verify'])->name('security.receipts.verify');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/customers', [DashboardController::class, 'customers'])->name('admin.customers');
    Route::get('/inventory', [DashboardController::class, 'inventory'])->name('admin.inventory');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('admin.analytics');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('admin.settings');
    Route::get('/security', fn () => redirect()->route('security.index'))->name('admin.security');
    Route::post('/customers/{user}/password', [DashboardController::class, 'updateCustomerPassword'])->name('admin.customers.password');

    Route::get('products/qr-labels', [AdminProductController::class, 'qrLabels'])->name('admin.products.qr-labels');
    Route::resource('products', AdminProductController::class)->except(['show'])->names('admin.products');
    Route::get('orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::resource('categories', AdminCategoryController::class)->except(['show'])->names('admin.categories');

});
