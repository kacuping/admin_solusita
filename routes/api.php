<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\CleanerController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\PaymentMethodController;

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/payment/notification', [PaymentController::class, 'notification']);

// Public Master Data (Categories, Services, Cleaners)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::get('/cleaners', [CleanerController::class, 'index']);
Route::get('/cleaners/{id}', [CleanerController::class, 'show']);
Route::get('/payment-methods', [PaymentMethodController::class, 'index']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->role,
                'address' => $user->address,
                'avatar' => $user->avatar,
                'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null,
            ],
        ]);
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::post('/payment/create', [PaymentController::class, 'createTransaction']);
    Route::apiResource('users', UserController::class);
    Route::apiResource('transactions', TransactionController::class)->only(['index', 'show']);
    Route::post('/transactions/{id}/assign', [TransactionController::class, 'assign']);
    Route::post('/transactions/{id}/complete', [TransactionController::class, 'complete']);
});
