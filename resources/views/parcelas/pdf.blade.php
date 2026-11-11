<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        .titulo { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
        .info { margin-bottom: 10px; }
        .pix, .boleto { padding: 10px; background: #ddd; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="titulo">Aviso de Pagamento</div>
    <p class="info">Cliente: {{ $parcela->contrato->cliente->nome }}</p>
    <p class="info">Valor: R$ {{ number_format($parcela->valor, 2, ',', '.') }}</p>
    <p class="info">Vencimento: {{ \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') }}</p>

    <div class="pix">Chave Pix:(61) 992761053</div>
</body>
</html>
