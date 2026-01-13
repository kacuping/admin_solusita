<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User Management
Route::middleware('auth')->group(function () {
    Route::controller(App\Http\Controllers\TransactionController::class)->group(function () {
        Route::get('/transaksi', 'index')->name('transaksi.index');
        Route::get('/transaksi/{transaction}', 'show')->name('transaksi.show');
        Route::post('/transaksi/{transaction}/assign', 'assign')->name('transaksi.assign');
        Route::post('/transaksi/{transaction}/complete', 'complete')->name('transaksi.complete');
        Route::post('/transaksi/{transaction}/cancel', 'cancel')->name('transaksi.cancel');
    });

    Route::resource('users/admin', App\Http\Controllers\AdminUserController::class)
        ->names('users.admin')
        ->parameters(['admin' => 'user']);

    Route::resource('users/pelanggan', App\Http\Controllers\PelangganUserController::class)
        ->names('users.pelanggan')
        ->parameters(['pelanggan' => 'user']);

    Route::resource('users/cleaner', App\Http\Controllers\CleanerUserController::class)
        ->names('users.cleaner')
        ->parameters(['cleaner' => 'user']);

    Route::resource('services/kategori', App\Http\Controllers\ServiceCategoryController::class)
        ->names('services.kategori')
        ->parameters(['kategori' => 'category']);

    Route::resource('services/layanan', App\Http\Controllers\ServiceController::class)
        ->names('services.layanan')
        ->parameters(['layanan' => 'layanan']);

    Route::resource('rating', App\Http\Controllers\RatingController::class)
        ->only(['index', 'show'])
        ->names('rating');

    Route::resource('payment-methods', App\Http\Controllers\PaymentMethodController::class)
        ->names('payment-methods');

    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
});

