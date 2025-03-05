<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\V1\PizzaController;
use \App\Http\Controllers\Api\V1\DrinkController;
use \App\Http\Controllers\Api\V1\OrderController;
use \App\Http\Controllers\Api\V1\AuthController;
use \App\Http\Controllers\Api\V1\CartController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->middleware(['throttle:api'])->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::prefix('v1')->middleware(['throttle:api', 'auth:sanctum'])->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);

    Route::apiResource('pizzas', PizzaController::class);
    Route::apiResource('drinks', DrinkController::class);
    Route::apiResource('orders', OrderController::class);

    // Маршруты для корзины
    Route::get('carts', [CartController::class, 'index']);
    Route::post('carts', [CartController::class, 'store']);
    Route::get('carts/{user_id}', [CartController::class, 'show']);
    Route::put('carts/{user_id}', [CartController::class, 'update']);
    Route::delete('carts/{user_id}', [CartController::class, 'destroy']);
});

