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

Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/logout', [UserController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
Route::get('/user', [UserController::class, 'user'])->middleware('auth:sanctum')->name('user');


Route::get('/products', [ProductController::class, 'getProduct']);
Route::get('/product/{id}', [ProductController::class, 'getProductId']);
Route::put('/product/{id}', [ProductController::class, 'editProduct']);

Route::post('/createProduct', [ProductController::class, 'create']);

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/departments', [DepartmentsControllers::class, 'index']);
Route::get('/municipalities', [MunicipalityControllers::class, 'index']);

Route::post('/createDirection', [DirectionController::class, 'store']);
