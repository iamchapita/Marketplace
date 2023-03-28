<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentsControllers;
use App\Http\Controllers\MunicipalityControllers;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\WishListController;
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

// Rutas de Inicio de sesón o registro
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
Route::post('/createDirection', [DirectionController::class, 'store'])->name('createDirection');

// Ruta de usuario Autenticado
Route::get('/user', [UserController::class, 'user'])->middleware('auth:sanctum')->name('user');

// Ruta de Detalles de Usuario Vendedor
Route::post('/sellerDetails', [UserController::class, 'getSellerDetails']);

// Rutas de Producto
Route::post('/getProductImages', [ProductController::class, 'getProductImages'])->name('productImages');

Route::get('/products', [ProductController::class, 'getProducts'])->name('products');
Route::get('/productsWishList/{userId}', [ProductController::class, 'getProductsWithWishlistStatus'])->middleware('auth:sanctum');

Route::get('/product/{id}', [ProductController::class, 'getProductById'])->name('getProduct');
Route::put('/product/{id}', [ProductController::class, 'editProduct'])->middleware('auth:sanctum')->name('UpdateProduct');
Route::post('/createProduct', [ProductController::class, 'create'])->middleware('auth:sanctum')->name('createProduct');

// Ruta de Categorias
Route::get('/categories', [CategoryController::class, 'index']);

// Ruta de departamentos y muncipios
Route::get('/departments', [DepartmentsControllers::class, 'index']);
Route::get('/municipalities', [MunicipalityControllers::class, 'index']);

// Ruta de Lista de Deseos
Route::post('/wishlist', [WishListController::class, 'index'])->name('getWishlist');
Route::post('/wishlistInsert', [WishlistController::class, 'store'])->name('wishlistStore');
Route::post('/wishlistDelete', [WishlistController::class, 'delete'])->name('wishlistDelete');
