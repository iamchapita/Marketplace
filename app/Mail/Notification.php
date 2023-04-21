<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Message;


class Notification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    /*Recoge la Vista del Correo
    public function build()
    {
        return $this->view('mail');
    }*/

    public function build()
    {
    return $this->view('mail')
                ->attach(storage_path('app/pdf/prueba.pdf'), [
                    'as' => 'prueba.pdf',
                    'mime' => 'application/pdf',
                ]);
    }

}
