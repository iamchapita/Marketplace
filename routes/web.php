<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ProductPdfController;
use App\Mail\Notification;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Envia el Correo
Route::get('/send', function(){
    Mail::to('edusalgado00@gmail.com')->send(new Notification());
    return 'Correo Enviado';
});

Route::get('/products/pdf', [ProductPdfController::class , 'generatePdf']);

