
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Aviso de vencimento</title>
</head>
<body>
    <p>Olá, {{ $cliente->nome }}.</p>

    <p>
        Este é um lembrete de que sua parcela
        nº {{ $parcela->numero }} está próxima do vencimento.
    </p>

    <p>
        <strong>Valor em aberto:</strong>
        R$ {{ number_format($valorEmAberto, 2, ',', '.') }}
    </p>

    <p>
        <strong>Vencimento:</strong>
        {{ $parcela->data_vencimento->format('d/m/Y') }}
    </p>

    <p>
        Consulte as informações de pagamento no documento PDF anexado.
    </p>

    <p>Atenciosamente.</p>
</body>
</html>
