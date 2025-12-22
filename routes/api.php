<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/payment/create', [PaymentController::class, 'createTransaction']);
    Route::apiResource('users', UserController::class);
});

Route::post('/payment/notification', [PaymentController::class, 'notification']);
