<?php

namespace App\Mail;

namespace App\Mail;

use Illuminate\Mail\Mailable;

class AvisoVencimento extends Mailable
{
    public $parcela;

    public function __construct($parcela)
    {
        $this->parcela = $parcela;
    }

    public function build()
    {
        return $this->subject('Aviso de Vencimento')
                    ->view('emails.aviso_vencimento')
                    ->attach(storage_path('app/public/boleto_pix_'.$this->parcela->id.'.pdf'));
    }
}
