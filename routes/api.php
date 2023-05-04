<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DepartmentsControllers;
use App\Http\Controllers\MunicipalityControllers;
use App\Http\Controllers\DirectionController;
use App\Http\Controllers\WishListController;
use App\Http\Controllers\RatingsController;
use App\Http\Controllers\ProductPdfController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\UsersCategoriesController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;
use App\Mail\Notification;


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
Route::get('/popularProducts', [ProductController::class, 'getPopularProducts'])->name('popularProducts');
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

Route::post('/productst', [ProductController::class, 'getProductst'])->name('productst');
Route::get('/buscaproduct', [ProductController::class, 'buscaproduct']);

// Rutas de estadisticas
Route::get('/getUsersStatistics', [UserController::class, 'getUsersStatistics'])->middleware('auth:sanctum');
Route::get('/getProductsStatistics', [ProductController::class, 'getProductsStatistics'])->middleware('auth:sanctum');
Route::get('/getComplaintsStatistics', [ComplaintController::class, 'getComplaintsStatistics'])->middleware('auth:sanctum');

// Rutas de Administrador
// Usuarios
Route::get('/getAllUsers/{registersPerPage?}/{page?}', [UserController::class, 'getAllUsers'])->middleware('auth:sanctum');
// Productos
Route::get('/getAllProducts/{registersPerPage?}/{page?}', [ProductController::class, 'getAllProducts'])->middleware('auth:sanctum');
// Denuncias
Route::get('/getAllComplaints/{registersPerPage?}/{page?}', [ComplaintController::class, 'getAllComplaints'])->middleware('auth:sanctum');
// Bannear Usuarios
Route::post('/setUserIsBanned', [UserController::class, 'setIsBanned'])->middleware('auth:sanctum');
// Bannear Producto
Route::post('/setProductIsBanned', [ProductController::class, 'setIsBanned'])->middleware('auth:sanctum');
// Producto Vendidos
Route::get('/getProductoVendidos', [ProductController::class, 'getProductosVendido']);
// Producto Insertados por mes año y dia
Route::get('/getProductsInsertadosMes', [ProductController::class, 'getProductsInsertadosMes']);
// Usuarios ActivosSS
Route::get('getActiveUsers', [UserController::class, 'getActiveUsers']);
Route::get('getActiveUsers6Month', [UserController::class, 'getActiveUsers6Month']);
Route::get('getActiveUsers1yeart', [UserController::class, 'getActiveUsers1yeart']);

// Rutas de Denuncias
Route::post('/createComplaint', [ComplaintController::class, 'create'])->middleware('auth:sanctum');
Route::post('/getAComplaint', [ComplaintController::class, 'getAComplaint'])->middleware('auth:sanctum');
Route::post('/getComplaintById', [ComplaintController::class, 'getComplaintById']);
Route::post('/getComplaintEvidences', [ComplaintController::class, 'getComplaintEvidences']);

// Rutas de calificaion de usuario
Route::post('/setRating', [RatingsController::class, 'setRating'])->middleware('auth:sanctum');;
Route::post('/getRating', [RatingsController::class, 'getRating']);

// Rutas de Suscripciones
Route::post('/subscription', [UsersCategoriesController::class, 'store']);
Route::get('/getSubscription', [UsersCategoriesController::class, 'getSubscriptionCategory']);
Route::post('/state', [SubscriptionController::class, 'state']);
Route::get('/getState', [SubscriptionController::class, 'getSubscription']);

//Envia el Correo
Route::get('/send', function () {
    set_time_limit(0);
    while (true) {
        Mail::to(['edusalgado00@gmail.com'])->send(new Eduardo());
        Mail::to(['edusalgado00@gmail.com'])->send(new Alejandro());
        Mail::to(['edusalgado00@gmail.com'])->send(new Alejandra());
        Mail::to(['edusalgado00@gmail.com'])->send(new Demsey());
        Mail::to(['edusalgado00@gmail.com'])->send(new Ana());
        sleep(60); //Son cada 60 segundos que se enviara el correo automaticamente, para no llenarse de tantos correos se puede aumentar el tiempo
    }              //Consejo aumentarlo
    return response()->json(['message' => 'Correos Enviados']);
});

Route::get('/generatePdf', [ProductPdfController::class, 'generatePdf']);
