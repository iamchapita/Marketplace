<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Demsey extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function build()
    {
    return $this->view('mail')
                ->attach(storage_path('app/public/pdf/Demsey_Categorias_Favoritas.pdf'), [
                    'as' => 'Demsey_Categorias_Favoritas.pdf',
                    'mime' => 'application/pdf',
                ]);
    }

}
