<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\SaleController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StoreCategoryController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// Public storefront
Route::view('/', 'home');

Route::redirect('/shop', '/shop/everyone');
Route::redirect('/shop/categories', '/shop/categories/everyone');

Route::get('/shop/{audience}', [ShopController::class, 'index'])
    ->whereIn('audience', ['men', 'women', 'everyone'])
    ->name('shop.index');

// Shopping session routes
Route::controller(CartController::class)->prefix('user/cart')->name('user.cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/add', 'add')->name('add');
    Route::patch('/{key}', 'update')->name('update');
    Route::delete('/{key}', 'remove')->name('remove');
});

Route::redirect('/categories', '/shop/categories/men');
Route::get('/shop/categories/{audience}', [StoreCategoryController::class, 'index'])
    ->whereIn('audience', ['men', 'women', 'everyone'])
    ->name('store.index');

Route::controller(WishlistController::class)->prefix('user/wishlist')->name('user.wishlist.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/toggle', 'toggle')->name('toggle');
});

// Customer account and authentication
Route::prefix('account')->name('account.')->group(function () {
    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [CustomerAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [CustomerAuthController::class, 'sendOtp'])->name('send-otp');
        Route::get('/verify', [CustomerAuthController::class, 'showVerify'])->name('verify');
        Route::post('/verify', [CustomerAuthController::class, 'verifyOtp'])->name('verify.submit');
        Route::post('/verify/resend', [CustomerAuthController::class, 'resendOtp'])->name('resend');
    });

    Route::middleware('auth:customer')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
        Route::put('/profile', [AccountController::class, 'update'])->name('update');
        Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
    });
});

// Customer checkout and orders
Route::middleware('auth:customer')->group(function () {
    Route::controller(CheckoutController::class)->prefix('user/checkout')->name('checkout.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'place')->name('place');
    });

    Route::controller(OrderController::class)->prefix('user/orders')->name('orders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{order}', 'show')->name('show');
        Route::get('/{order}/invoice', 'invoice')->name('invoice');
        Route::post('/{order}/cancel', 'cancel')->name('cancel');
        Route::post('/{order}/reorder', 'reorder')->name('reorder');
        Route::get('/{order}/pay', 'pay')->name('pay');
        Route::post('/{order}/pay', 'processPay')->name('pay.process');
        Route::get('/{order}/address', 'editAddress')->name('address');
        Route::put('/{order}/address', 'updateAddress')->name('address.update');
    });
});

Route::get('product/{product}', [ShopController::class, 'show']);

// Admin authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// Admin panel
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::view('/', 'admin.index');

    Route::controller(AdminProductController::class)->prefix('products')->group(function () {
        Route::get('/', 'index');
        Route::get('/create', 'create');
        Route::post('/', 'store');
        Route::get('/{product}/edit', 'edit');
        Route::put('/{product}', 'update');
        Route::delete('/{product}', 'destroy');
    });

    Route::controller(AdminCategoryController::class)->prefix('categories')->name('admin.categories.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::delete('/{category}', 'destroy')->name('destroy');
    });

    Route::get('/audiences/{audience}/categories', [AdminProductController::class, 'getCategoriesByAudience'])->name('admin.audiences.categories');

    Route::controller(SizeController::class)->prefix('sizes')->name('admin.sizes.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');

        Route::post('/{size}/variants', 'storeVariant')->name('variants.store');
        Route::delete('/variants/{variant}', 'destroyVariant')->name('variants.destroy');

        Route::delete('/{size}', 'destroy')->name('destroy');
    });

    Route::controller(SaleController::class)->prefix('sales')->name('admin.sales.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{invoice}', 'show')->name('show');
        Route::get('/{invoice}/edit', 'edit')->name('edit');
        Route::put('/{invoice}', 'update')->name('update');
        Route::delete('/{invoice}', 'destroy')->name('destroy');
    });

    Route::controller(AdminOrderController::class)->prefix('orders')->name('admin.orders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{order}', 'show')->name('show');
        Route::patch('/{order}/status', 'updateStatus')->name('status');
        Route::post('/{order}/cancel', 'cancel')->name('cancel');
        Route::post('/{order}/address-seen', 'addressSeen')->name('address-seen');
    });

});
