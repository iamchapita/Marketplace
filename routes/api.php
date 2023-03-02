<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/products', [ProductController::class, 'getProduct']);
Route::get('/product/{id}', [ProductController::class, 'getProductId']);
Route::put('/product/{id}', [ProductController::class, 'editProduct']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/logout', [UserController::class, 'logout']);
Route::post('product','App\Http\Controllers\ProductController@create');

//Creat Departaments
Route::post('/departments', [DepartmentsControllers::class, 'CreateDepartaments']);
Route::post('/municipality', [MunicipalityControllers::class, 'CreateMunicipality']);