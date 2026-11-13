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
        $this->apiUrl = rtrim((string) env('WHATSAPP_API_URL'), '/');
        $this->token = env('WHATSAPP_TOKEN');
        $this->phoneId = env('WHATSAPP_PHONE_ID');
    }

    public function estaConfigurado(): bool
    {
        return !empty($this->apiUrl)
            && !empty($this->token)
            && !empty($this->phoneId);
    }

    public function enviarMensagem($telefone, $mensagem): array
    {
        if (!$this->estaConfigurado()) {
            throw new \RuntimeException(
                'Integração do WhatsApp não configurada.'
            );
        }

        $telefone = preg_replace('/\D/', '', (string) $telefone);

        if (strlen($telefone) < 10) {
            throw new \RuntimeException('Telefone inválido.');
        }

        if (!str_starts_with($telefone, '55')) {
            $telefone = '55' . $telefone;
        }

        $endpoint = "{$this->apiUrl}/{$this->phoneId}/messages";

        $response = Http::withToken($this->token)
            ->acceptJson()
            ->timeout(20)
            ->post($endpoint, [
                'messaging_product' => 'whatsapp',
                'to' => $telefone,
                'type' => 'text',
                'text' => [
                    'body' => $mensagem,
                ],
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException(
                'Erro na API do WhatsApp: ' . $response->body()
            );
        }

        return $response->json() ?? [];
    }
}
