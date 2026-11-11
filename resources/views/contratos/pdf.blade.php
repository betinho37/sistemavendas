<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Contrato - {{ $contrato->cliente->nome }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #ffffff;
            background-color: #212529;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            background-color: #343a40;
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        p {
            margin: 5px 0;
        }

        hr {
            margin: 20px 0;
            border: 0;
            border-top: 1px solid #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #777;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #000;
        }

        .text-success {
            color: #4caf50;
        }

        .text-danger {
            color: #ff5252;
        }
    </style>
</head>

<body>
<div class="container">

    <h2>Contrato - {{ $contrato->cliente->nome }}</h2>

    @php
        $totalReceber = $contrato->parcelas->sum('valor');
        $lucro = $totalReceber - $contrato->valor;
        $valorParcela = $contrato->parcelas->first()->valor ?? 0;
    @endphp

    <p><strong>Data de Início:</strong> {{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }}</p>
    <p><strong>Cliente:</strong> {{ $contrato->cliente->nome }}</p>
    <p><strong>Valor do Contrato:</strong> R$ {{ number_format($contrato->valor, 2, ',', '.') }}</p>
    <p><strong>Quantidade de Parcelas:</strong> {{ $contrato->qtd_parcelas }}</p>
    <p><strong>Intervalo entre Parcelas:</strong> {{ $contrato->intervalo_dias }} dias</p>
    <p><strong>Juros Aplicados:</strong> {{ number_format($contrato->juros, 2, ',', '.') }}%</p>
    <p><strong>Valor da Parcela:</strong> R$ {{ number_format($valorParcela, 2, ',', '.') }}</p>
    <p><strong>Total a Receber:</strong> R$ {{ number_format($totalReceber, 2, ',', '.') }}</p>

    <p><strong>Observações:</strong> {{ $contrato->observacao ?? 'Nenhuma' }}</p>

    <hr>

    <h3>Parcelas</h3>

    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Vencimento</th>
            <th>Valor</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($contrato->parcelas as $parcela)
            <tr>
                <td>{{ $parcela->numero }}</td>
                <td>{{ \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') }}</td>
                <td>R$ {{ number_format($parcela->valor, 2, ',', '.') }}</td>
                <td>
                    @if($parcela->status)
                        <span class="text-success">Paga</span>
                    @else
                        <span class="text-danger">Não Paga</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <hr>

    <p>Assinatura do Cliente: ________________________________</p>
    <p>Assinatura da Empresa: ______________________________</p>

</div>
</body>
</html>
