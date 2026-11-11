<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $apiUrl;
    protected $token;
    protected $phoneId;

    public function __construct()
    {
        $this->apiUrl = env('WHATSAPP_API_URL');
        $this->token = env('WHATSAPP_TOKEN');
        $this->phoneId = env('WHATSAPP_PHONE_ID');
    }

    public function enviarMensagem($telefone, $mensagem)
    {
        $endpoint = "{$this->apiUrl}/{$this->phoneId}/messages";

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->token}",
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'messaging_product' => 'whatsapp',
            'to' => $telefone, // Número do cliente com DDI, ex: 5511999999999
            'type' => 'text',
            'text' => ['body' => $mensagem],
        ]);

        return $response->json();
    }
}
