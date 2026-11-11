<p>Olá, {{ $parcela->contrato->cliente->nome }},</p>
<p>Seu pagamento de R$ {{ number_format($parcela->valor, 2, ',', '.') }} vence em breve.</p>
<p>Baixe o boleto ou pague via Pix.</p>
