<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function build()
    {
    return $this->view('mail')
                ->attach(storage_path('app/public/pdf/Productos_Categorias_Favoritas.pdf'), [
                    'as' => 'Productos_Categorias_Favoritas.pdf',
                    'mime' => 'application/pdf',
                ]);
    }

}
