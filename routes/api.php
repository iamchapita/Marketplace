<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentsControllers;
use App\Http\Controllers\MunicipalityControllers;
use App\Http\Controllers\DirectionController;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas de Inicio de sesón o registro
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
Route::post('/createDirection', [DirectionController::class, 'store'])->name('createDirection');

// Obtiene el usuario Autenticado
Route::get('/user', [UserController::class, 'user'])->middleware('auth:sanctum')->name('user');

// Rutas de Producto
Route::get('/products', [ProductController::class, 'getProducts'])->name('products');
Route::get('/product/{id}', [ProductController::class, 'getProductById'])->name('getProduct');
Route::put('/product/{id}', [ProductController::class, 'editProduct'])->middleware('auth:sanctum')->name('UpdateProduct');

Route::post('/createProduct', [ProductController::class, 'create'])->middleware('auth:sanctum')->name('createProduct');

Route::get('/categories', [CategoryController::class, 'index']);

// Obtencion de departamentos y muncipios
Route::get('/departments', [DepartmentsControllers::class, 'index']);
Route::get('/municipalities', [MunicipalityControllers::class, 'index']);

// Lista de Deseos
Route::get('/wishlist/{id}', [WishListController::class, 'index']);
Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
Route::delete('/wishlist/{product}', [WishlistController::class, 'delete'])->name('wishlist.delete');
