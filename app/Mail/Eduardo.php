<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Eduardo extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function build()
    {
    return $this->view('mail')
                ->attach(storage_path('app/public/pdf/Eduardo_Categorias_Favoritas.pdf'), [
                    'as' => 'Eduardo_Categorias_Favoritas.pdf',
                    'mime' => 'application/pdf',
                ]);
    }

}
