<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentsControllers;
use App\Http\Controllers\MunicipalityControllers;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\WishListController;
use App\Http\Controllers\RatingsController;
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

// Rutas de Inicio de sesión, registro y actualización de usuario
Route::post('/register', [UserController::class, 'register'])->name('register');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
Route::post('/setToSeller', [UserController::class, 'setToSeller'])->middleware('auth:sanctum')->name('setToSeller');
Route::post('/createDirection', [DirectionController::class, 'store'])->name('createDirection');

// Ruta de usuario Autenticado
Route::get('/user', [UserController::class, 'user'])->middleware('auth:sanctum')->name('user');

// Ruta de Detalles de Usuario Vendedor
Route::post('/sellerDetails', [UserController::class, 'getSellerDetails']);

// Rutas de Producto
Route::post('/getProductImages', [ProductController::class, 'getProductImages'])->name('productImages');
Route::get('/products', [ProductController::class, 'getProducts'])->name('products');
Route::post('/getProductsBySeller', [ProductController::class, 'getProductsBySeller']);
Route::get('/product/{id}', [ProductController::class, 'getProductById'])->name('getProduct');
Route::get('/productsWishList/{userId}', [ProductController::class, 'getProductsWithWishlistStatus'])->middleware('auth:sanctum');
Route::post('/createProduct', [ProductController::class, 'create'])->middleware('auth:sanctum')->name('createProduct');
Route::post('/updateProduct', [ProductController::class, 'update'])->middleware('auth:sanctum');
Route::post('/setWasSoldValue', [ProductController::class, 'setWasSoldValue'])->middleware('auth:sanctum');
Route::post('/setIsAvailableValue', [ProductController::class, 'setIsAvailable'])->middleware('auth:sanctum');
Route::post('/setIsBannedValue', [ProductController::class, 'setIsBanned'])->middleware('auth:sanctum');
Route::post('/orderByProducts', [ProductController::class, 'orderByProducts'])->name('sortProducts');

// Ruta de Categorias
Route::get('/categories', [CategoryController::class, 'index']);

// Ruta de departamentos y muncipios
Route::get('/departments', [DepartmentsControllers::class, 'index']);
Route::get('/municipalities', [MunicipalityControllers::class, 'index']);

// Ruta de Lista de Deseos
Route::post('/wishlist', [WishListController::class, 'index'])->name('getWishlist');
Route::post('/wishlistInsert', [WishlistController::class, 'store'])->name('wishlistStore');
Route::post('/wishlistDelete', [WishlistController::class, 'delete'])->name('wishlistDelete');

// Ruta para Crear PDF
Route::get('/products/pdf', [ProductController::class, 'generatePDF'])->name('createPDF');

Route::post('/productst', [ProductController::class, 'getProductst'])->name('productst');
Route::get('/buscaproduct', [ProductController::class, 'buscaproduct']);

// Rutas de estadisticas
Route::get('/getUsersStatistics', [UserController::class, 'getUsersStatistics'])->middleware('auth:sanctum');
Route::get('/getProductsStatistics', [ProductController::class, 'getProductsStatistics'])->middleware('auth:sanctum');

// Rutas de Administrador
Route::get('/getAllUsers/{registersPerPage?}/{page?}', [UserController::class, 'getAllUsers']);

Route::post('/rating', [RatingsController::class, 'addRating']);
Route::post('/getrating', [RatingsController::class, 'ratingValue']);
