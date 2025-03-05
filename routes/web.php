<?php

use App\Http\Controllers\PizzaController;
use App\Http\Controllers\DrinkController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShowcaseController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminMiddleware;

use Illuminate\Support\Facades\Route;

Route::get('/', [ShowcaseController::class, 'index'])->name('showcase');

Route::get('/my_orders', [OrderController::class, 'my_orders'])->name('orders.my_orders');

Route::get('/cart', [CartController::class, 'index'])->name('cart') ;
Route::group(['prefix' => 'cart', 'as' => 'cart.'], function () {
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/delete', [CartController::class, 'delete'])->name('delete');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/order', [CartController::class, 'order'])->name('order');
});

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/auth', [AuthController::class, 'index'])->name('auth_page');
Route::post('/login', [AuthController::class, 'auth'])->name('auth');

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/admin', [AdminController::class, 'index'])->name('admin')->middleware(AdminMiddleware::class);
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/add-pizza', [AdminController::class, 'addPizza'])->name('add-pizza');
    Route::get('/add-drink', [AdminController::class, 'addDrink'])->name('add-drink');
    Route::post('/pizzas', [PizzaController::class, 'store'])->name('pizzas.store');
    Route::post('/drinks', [DrinkController::class, 'store'])->name('drinks.store');
    Route::get('/delete-product', [AdminController::class, 'deleteProductList'])->name('delete-product');
    Route::post('/delete-product', [AdminController::class, 'deleteProduct'])->name('delete-product');
    Route::get('/change-product', [AdminController::class, 'changeProductList'])->name('change-product');
    Route::post('/change-product', [AdminController::class, 'changeProduct'])->name('change-product');
    Route::get('/order-statuses', [AdminController::class, 'changeOrderStatuses'])->name('order-statuses');
    Route::post('/order-statuses', [AdminController::class, 'changeOrderStatus'])->name('order-statuses');
});

Route::get('/pizzas/create', [PizzaController::class, 'create'])->name('pizzas.create');

Route::get('/drinks/create', [DrinkController::class, 'create'])->name('drinks.create');

