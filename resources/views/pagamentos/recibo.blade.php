
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pagamento</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 35px;
        }

        .recibo {
            border: 2px solid #222;
            padding: 28px;
        }

        .titulo {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .numero {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
        }

        .linha {
            border-bottom: 1px solid #ddd;
            padding: 11px 0;
        }

        .valor {
            font-size: 23px;
            font-weight: bold;
            color: #168447;
            text-align: center;
            margin: 25px 0;
        }

        .resumo {
            background: #f2f2f2;
            padding: 15px;
            margin-top: 20px;
        }

        .assinatura {
            margin-top: 65px;
            text-align: center;
        }

        .assinatura span {
            display: block;
            border-top: 1px solid #333;
            width: 65%;
            margin: 0 auto;
            padding-top: 8px;
        }
    </style>
</head>

<body>
<div class="recibo">

    <div class="titulo">RECIBO DE PAGAMENTO</div>

    <div class="numero">
        Recibo nº {{ str_pad($pagamento->id, 6, '0', STR_PAD_LEFT) }}
    </div>

    <div class="linha">
        <strong>Cliente:</strong> {{ $cliente->nome }}
    </div>

    <div class="linha">
        <strong>Contrato:</strong> Nº {{ $contrato->id }}
    </div>

    <div class="linha">
        <strong>Parcela:</strong> {{ $parcela->numero }}
    </div>

    <div class="linha">
        <strong>Data do recebimento:</strong>
        {{ $pagamento->data_pagamento->format('d/m/Y') }}
    </div>

    <div class="valor">
        R$ {{ number_format($pagamento->valor, 2, ',', '.') }}
    </div>

    <p>
        Recebemos de <strong>{{ $cliente->nome }}</strong>
        o valor acima informado, referente ao pagamento
        da parcela {{ $parcela->numero }} do contrato
        nº {{ $contrato->id }}.
    </p>

    <div class="resumo">
        <p>
            <strong>Valor original da parcela:</strong>
            R$ {{ number_format($parcela->valor, 2, ',', '.') }}
        </p>

        <p>
            <strong>Total recebido na parcela:</strong>
            R$ {{ number_format($totalRecebido, 2, ',', '.') }}
        </p>

        <p>
            <strong>Saldo restante da parcela:</strong>
            R$ {{ number_format($saldoRestante, 2, ',', '.') }}
        </p>
    </div>

    <div class="assinatura">
        <span>Assinatura do responsável pelo recebimento</span>
    </div>

    <p style="text-align:center; margin-top:30px; color:#666;">
        Documento emitido eletronicamente pelo sistema m1r4ge7.
    </p>

</div>
</body>
</html>
