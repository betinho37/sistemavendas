<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnviarParcelaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $cliente;
    public $parcela;
    public $pdfPath;

    public function __construct($cliente, $parcela, $pdfPath)
    {
        $this->cliente = $cliente;
        $this->parcela = $parcela;
        $this->pdfPath = $pdfPath;
    }

    /* public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->subject("Aviso de Vencimento - Parcela")
                    ->markdown('emails.parcela')
                    ->attach($this->pdfPath, [
                        'as' => 'boleto_pix.pdf',
                        'mime' => 'application/pdf',
                    ]);
    } */

    public function build()
    {
        return $this->subject('Aviso de Parcela - AcervoTech')
                    ->markdown('emails.parcela')
                    ->attach($this->pdfPath, [
                        'as' => 'boleto.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
