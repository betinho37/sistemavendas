<?php


namespace App\Http\Controllers;

use App\Services\WhatsAppService;

class WhatsAppController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function enviarAviso($numero, $contrato)
    {
        $mensagem = "Olá, sua parcela do contrato #{$contrato->id} vence em breve! 
        Valor: R$ " . number_format($contrato->valor, 2, ',', '.') . " 
        Pague via PIX ou Boleto.";

        $this->whatsappService->enviarMensagem($numero, $mensagem);

        return back()->with('success', 'Mensagem enviada pelo WhatsApp!');
    }
}
