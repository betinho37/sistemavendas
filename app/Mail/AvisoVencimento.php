<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class AvisoVencimento extends Mailable
{
    public $parcela;
    public $cliente;
    public $valorEmAberto;
    public $pdfPath;

    public function __construct(
        $parcela,
        $cliente,
        $valorEmAberto,
        $pdfPath = null
    ) {
        $this->parcela = $parcela;
        $this->cliente = $cliente;
        $this->valorEmAberto = $valorEmAberto;
        $this->pdfPath = $pdfPath;
    }

    public function build()
    {
        $email = $this->subject('Aviso de vencimento de parcela')
            ->view('emails.aviso_vencimento')
            ->with([
                'parcela' => $this->parcela,
                'cliente' => $this->cliente,
                'valorEmAberto' => $this->valorEmAberto,
            ]);

        if ($this->pdfPath && file_exists($this->pdfPath)) {
            $email->attach($this->pdfPath, [
                'as' => 'boleto_pix_' . $this->parcela->id . '.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
