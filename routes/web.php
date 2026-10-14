<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\HomeController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\MomoController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\User\WishlistController;
use App\Http\Controllers\User\ChatController as UserChatController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;

/*
|--------------------------------------------------------------------------
| 🚚 THIRD-PARTY WEBHOOKS & CALLBACKS (GHN, MOMO IPN)
|--------------------------------------------------------------------------
| NOTE:
| - Bypassed CSRF in bootstrap/app.php
|--------------------------------------------------------------------------
*/

Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])->name('payment.momo.ipn');
Route::get('/payment/momo/callback', [MomoController::class, 'callback'])->name('user.payment.momo.callback');

/*
|--------------------------------------------------------------------------
| User / Client Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/bo-suu-tap', [HomeController::class, 'collection'])->name('collection.index');
Route::get('/san-pham/{product}', [HomeController::class, 'show'])->name('products.show');

// Public Wishlist API Routes (Handles guest prompt gracefully)
Route::post('/wishlist/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::get('/wishlist/count', [WishlistController::class, 'count'])->name('wishlist.count');

// Shopping Cart & Checkout Routes (Protected by Auth Middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout/process', [OrderController::class, 'processPayment'])->name('checkout.process');
    Route::post('/payment/process', [OrderController::class, 'processPayment'])->name('user.payment.process');

    // Orders & Payment Retry Routes
    Route::get('/user/orders', [OrderController::class, 'index'])->name('user.orders.index');
    Route::get('/user/orders/{order}', [OrderController::class, 'show'])->name('user.orders.show');
    Route::post('/user/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('user.orders.cancel');
    Route::get('/orders/{order}/pay/momo', [MomoController::class, 'payAgain'])->name('user.orders.momo.pay');
    Route::get('/orders/{order}/start-momo', [MomoController::class, 'start'])->name('user.orders.momo.start');


    // GHN Shipping & Location API Routes
    Route::get('/ghn/provinces', [CartController::class, 'getGhnProvinces'])->name('ghn.provinces');
    Route::get('/ghn/districts/{province_id}', [CartController::class, 'getGhnDistricts'])->name('ghn.districts');
    Route::get('/ghn/wards/{district_id}', [CartController::class, 'getGhnWards'])->name('ghn.wards');
    Route::post('/ghn/calculate-fee', [CartController::class, 'calculateGhnFee'])->name('ghn.fee');

    // User Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/verify-email-otp', [ProfileController::class, 'showVerifyEmailOtpForm'])->name('profile.verify_email.form');
    Route::post('/profile/verify-email-otp', [ProfileController::class, 'verifyEmailOtp'])->name('profile.verify_email');

    // User Livechat Routes
    Route::post('/chat/send', [UserChatController::class, 'send'])->name('user.chat.send');
    Route::get('/chat/messages', [UserChatController::class, 'getMessages'])->name('user.chat.messages');
    Route::get('/chat/unread-count', [UserChatController::class, 'unreadCount'])->name('user.chat.unread_count');

    // Wishlist Routes (User's private saved items)
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');
});

/*
|--------------------------------------------------------------------------
| Authentication & Email OTP Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/verify-otp', [RegisterController::class, 'showOtpForm'])->name('otp.verify.form');
Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/resend-otp', [RegisterController::class, 'resendOtp'])->name('otp.resend');

// Forgot & Reset Password Routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.forgot');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetOtp'])->name('password.email');
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
Route::post('/resend-reset-otp', [ForgotPasswordController::class, 'resendOtp'])->name('password.resend_otp');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Google Social Authentication Routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

/*
|--------------------------------------------------------------------------
| Redirects for Legacy URLs
|--------------------------------------------------------------------------
*/
Route::redirect('/products', '/admin/products');
Route::redirect('/categories', '/admin/categories');

use App\Http\Controllers\Admin\OrderController as AdminOrderController;

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\FinanceController;

/*
|--------------------------------------------------------------------------
| Admin / Backoffice Protected Routes Group (Middleware: Auth & Admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/stats', [DashboardController::class, 'apiStats'])->name('dashboard.stats');

    // Admin Reports & Analytics
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/charts', [ReportController::class, 'charts'])->name('reports.charts');

    // Admin Finance & Payment Transactions
    Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('finance/transactions', [FinanceController::class, 'transactions'])->name('finance.transactions');
    Route::patch('finance/{order}/status', [FinanceController::class, 'updateStatus'])->name('finance.update-status');

    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::patch('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::resource('categories', CategoryController::class);
    Route::patch('brands/{brand}/toggle-status', [BrandController::class, 'toggleStatus'])->name('brands.toggle-status');
    Route::resource('brands', BrandController::class);
    Route::resource('products', ProductController::class);
    Route::resource('orders', AdminOrderController::class)->only(['index', 'show', 'update']);
    Route::post('orders/{order}/sync-ghn', [AdminOrderController::class, 'syncGhn'])->name('orders.sync-ghn');
    Route::resource('users', UserController::class);

    // Admin Livechat Routes
    Route::get('chat/users', [AdminChatController::class, 'getUsers'])->name('chat.users');
    Route::get('chat/messages/{userId}', [AdminChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('chat/send', [AdminChatController::class, 'send'])->name('chat.send');
    Route::get('chat/unread-count', [AdminChatController::class, 'unreadCount'])->name('chat.unread_count');
});
