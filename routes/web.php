<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ProductPdfController;
use App\Mail\Notification;
use App\Mail\Alejandro;
use App\Mail\Ana;
use App\Mail\Demsey;
use App\Mail\Alejandra;
use App\Mail\Eduardo;



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
Route::get('/pdf', [ProductPdfController::class, 'generatePdf']);

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
