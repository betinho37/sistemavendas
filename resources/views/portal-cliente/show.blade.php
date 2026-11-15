<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Portal do Cliente | m1r4ge7</title>
    <meta name="theme-color" content="#101010">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            background: #101010;
            color: #e8edf5;
            font-family: Arial, sans-serif;
        }

        .portal-header {
            background: #181818;
            border-bottom: 1px solid #303030;
            padding: 18px 0;
        }

        .portal-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
        }

        .portal-brand-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #0879bd, #12b8a6);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
        }

        .portal-brand strong {
            display: block;
            font-size: 19px;
        }

        .portal-brand small {
            color: #9299a5;
            font-size: 10px;
            letter-spacing: 1.5px;
        }

        .portal-container {
            max-width: 1050px;
            margin: 0 auto;
            padding: 28px 15px 50px;
        }

        .welcome { margin-bottom: 25px; }

        .welcome h1 {
            font-size: 25px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .welcome p {
            color: #999;
            margin: 0;
            font-size: 14px;
        }

        .portal-card {
            background: #1b1b1b;
            border: 1px solid #333;
            border-radius: 13px;
            padding: 20px;
            margin-bottom: 18px;
        }

        .portal-card h2 {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 17px;
        }

        .contract-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin: 22px 0 15px;
        }

        .contract-heading h2 { margin: 0; }

        .contract-number {
            color: #8e9aaa;
            font-size: 13px;
        }

        .contract-balance {
            background: #172b2a;
            border: 1px solid #24564e;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 15px;
        }

        .contract-balance small {
            color: #a8c8c1;
            display: block;
            margin-bottom: 4px;
        }

        .contract-balance strong {
            color: #42d69b;
            font-size: 21px;
        }

        .installment {
            background: #222;
            border: 1px solid #383838;
            border-radius: 11px;
            padding: 16px;
            margin-top: 12px;
        }

        .installment-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 13px;
        }

        .installment-title {
            font-weight: 700;
            font-size: 15px;
        }

        .installment-values {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        .value-label {
            color: #9299a5;
            font-size: 11px;
            margin-bottom: 5px;
        }

        .value-number {
            color: #fff;
            font-weight: 700;
            font-size: 15px;
            overflow-wrap: anywhere;
        }

        .value-number.green { color: #42d69b; }
        .value-number.yellow { color: #f5c451; }

        .installment-form {
            border-top: 1px solid #393939;
            margin-top: 15px;
            padding-top: 15px;
        }

        .installment-form h3 {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 13px;
        }

        .form-label {
            color: #c9cdd4;
            font-size: 12px;
        }

        .form-control {
            background: #111;
            color: #fff;
            border: 1px solid #444;
            border-radius: 8px;
            padding: 10px;
        }

        .form-control:focus {
            background: #111;
            color: #fff;
            border-color: #1689d4;
            box-shadow: 0 0 0 .2rem rgba(22,137,212,.15);
        }

        .form-control::file-selector-button {
            background: #303030;
            color: #fff;
        }

        .btn-send {
            width: 100%;
            background: #0879bd;
            color: #fff;
            border: 0;
            border-radius: 8px;
            padding: 12px;
            font-weight: 700;
        }

        .btn-send:hover {
            background: #09669c;
            color: #fff;
        }

        .notice {
            background: rgba(245,196,81,.08);
            border: 1px solid rgba(245,196,81,.25);
            color: #e8c979;
            border-radius: 8px;
            padding: 12px;
            font-size: 12px;
            margin-top: 12px;
        }

        .empty-state {
            text-align: center;
            color: #999;
            padding: 35px 15px;
        }

        .empty-state i {
            font-size: 35px;
            color: #555;
            margin-bottom: 12px;
        }

        .portal-footer {
            border-top: 1px solid #303030;
            padding: 20px 15px;
            text-align: center;
            color: #777;
            font-size: 12px;
        }

        @media(max-width: 600px) {
            .portal-container { padding-top: 22px; }
            .welcome h1 { font-size: 21px; }
            .portal-card { padding: 15px; }
            .installment-values { grid-template-columns: repeat(2, 1fr); }
            .installment-top { align-items: flex-start; }
        }
    </style>
</head>

<body>

<header class="portal-header">
    <div class="container">
        <div class="portal-brand">
            <div class="portal-brand-icon">
                <i class="fas fa-bolt"></i>
            </div>

            <div>
                <strong>m1r4ge7</strong>
                <small>PORTAL DO CLIENTE</small>
            </div>
        </div>
    </div>
</header>

<main class="portal-container">

    <section class="welcome">
        <h1>Olá, {{ $cliente->nome }}!</h1>
        <p>
            Consulte seus contratos, acompanhe suas parcelas e envie comprovantes de pagamento.
        </p>
    </section>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Verifique os dados informados:</strong>

            @foreach($errors->all() as $erro)
                <div>{{ $erro }}</div>
            @endforeach
        </div>
    @endif

    <div class="portal-card">
        <h2>
            <i class="fas fa-user-circle me-2"></i>
            Seus dados
        </h2>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="value-label">Nome</div>
                <div class="value-number">{{ $cliente->nome }}</div>
            </div>

            @if($cliente->telefone)
                <div class="col-md-6">
                    <div class="value-label">Telefone</div>
                    <div class="value-number">{{ $cliente->telefone }}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="portal-card">
        <h2>
            <i class="fas fa-file-contract me-2"></i>
            Seus contratos e parcelas
        </h2>

        @forelse($cliente->contratos as $contrato)

            @php
                $saldoContrato = 0;
                $primeiraParcelaAbertaId = null;

                foreach ($contrato->parcelas as $item) {
                    $pagoItem = (float) $item->pagamentos->sum('valor');
                    $saldoItem = max(0, (float) $item->valor - $pagoItem);

                    $saldoContrato += $saldoItem;

                    if ($saldoItem > 0.009 && $primeiraParcelaAbertaId === null) {
                        $primeiraParcelaAbertaId = $item->id;
                    }
                }

                $saldoContrato = round($saldoContrato, 2);
            @endphp

            <div class="contract-heading">
                <div>
                    <h2>Contrato #{{ $contrato->numero ?? $contrato->id }}</h2>
                    <div class="contract-number">
                        {{ $contrato->created_at?->format('d/m/Y') ?? '' }}
                    </div>
                </div>
            </div>

            <div class="contract-balance">
                <small>Saldo total restante do contrato</small>
                <strong>
                    R$ {{ number_format($saldoContrato, 2, ',', '.') }}
                </strong>
            </div>

            @forelse($contrato->parcelas as $parcela)

                @php
                    $totalPago = (float) $parcela->pagamentos->sum('valor');
                    $valorParcela = (float) $parcela->valor;
                    $saldo = max(0, $valorParcela - $totalPago);
                    $quitada = $saldo <= 0.009;

                    $valorVencimento = $parcela->data_vencimento
                        ? \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y')
                        : '-';
                @endphp

                <div class="installment">

                    <div class="installment-top">
                        <div class="installment-title">
                            Parcela {{ $parcela->numero }}
                        </div>

                        @if($quitada)
                            <span class="badge bg-success">
                                <i class="fas fa-check me-1"></i>
                                Paga
                            </span>
                        @else
                            <span class="badge bg-warning text-dark">
                                Em aberto
                            </span>
                        @endif
                    </div>

                    <div class="installment-values">
                        <div>
                            <div class="value-label">Valor da parcela</div>
                            <div class="value-number">
                                R$ {{ number_format($valorParcela, 2, ',', '.') }}
                            </div>
                        </div>

                        <div>
                            <div class="value-label">Total recebido</div>
                            <div class="value-number green">
                                R$ {{ number_format($totalPago, 2, ',', '.') }}
                            </div>
                        </div>

                        <div>
                            <div class="value-label">Saldo restante</div>
                            <div class="value-number yellow">
                                R$ {{ number_format($saldo, 2, ',', '.') }}
                            </div>
                        </div>

                        <div>
                            <div class="value-label">Vencimento</div>
                            <div class="value-number">
                                {{ $valorVencimento }}
                            </div>
                        </div>
                    </div>

                    @if(
                        !$quitada
                        && $parcela->id === $primeiraParcelaAbertaId
                    )

                        <div class="installment-form">
                            <h3>
                                <i class="fas fa-cloud-upload-alt me-2"></i>
                                Informar pagamento
                            </h3>

                            <div class="notice mb-3">
                                Você pode informar um pagamento parcial ou antecipar
                                várias parcelas. O valor será distribuído entre as
                                parcelas em aberto, começando pela mais antiga.
                            </div>

                            <form
                                method="POST"
                                action="{{ route('portal.cliente.solicitacoes.store', $cliente->portal_token) }}"
                                enctype="multipart/form-data"
                            >
                                @csrf

                                <input
                                    type="hidden"
                                    name="parcela_id"
                                    value="{{ $parcela->id }}"
                                >

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Valor pago (R$)
                                        </label>

                                        <input
                                            type="number"
                                            name="valor"
                                            class="form-control"
                                            min="0.01"
                                            max="{{ number_format($saldoContrato, 2, '.', '') }}"
                                            step="0.01"
                                            value="{{ old('parcela_id') == $parcela->id ? old('valor') : '' }}"
                                            placeholder="0,00"
                                            required
                                        >

                                        <small class="text-secondary">
                                            Saldo máximo do contrato: R$
                                            {{ number_format($saldoContrato, 2, ',', '.') }}
                                        </small>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">
                                            Data do pagamento
                                        </label>

                                        <input
                                            type="date"
                                            name="data_pagamento"
                                            class="form-control"
                                            value="{{ old('parcela_id') == $parcela->id ? old('data_pagamento') : date('Y-m-d') }}"
                                            required
                                        >
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Comprovante</label>

                                        <input
                                            type="file"
                                            name="comprovante"
                                            class="form-control"
                                            accept=".pdf,.jpg,.jpeg,.png"
                                            required
                                        >

                                        <small class="text-secondary">
                                            PDF, JPG ou PNG. Tamanho máximo: 5 MB.
                                        </small>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">
                                            Observação (opcional)
                                        </label>

                                        <textarea
                                            name="observacao"
                                            class="form-control"
                                            rows="2"
                                            maxlength="1000"
                                            placeholder="Informações adicionais sobre o pagamento..."
                                        >{{ old('parcela_id') == $parcela->id ? old('observacao') : '' }}</textarea>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn-send">
                                            <i class="fas fa-paper-plane me-2"></i>
                                            Enviar comprovante
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div class="notice">
                                <i class="fas fa-info-circle me-1"></i>
                                O envio não confirma automaticamente o pagamento.
                                O registro será feito após a conferência e aprovação
                                pela administração.
                            </div>
                        </div>

                    @endif

                </div>

            @empty
                <div class="empty-state">
                    Nenhuma parcela cadastrada neste contrato.
                </div>
            @endforelse

        @empty
            <div class="empty-state">
                <i class="fas fa-folder-open d-block"></i>
                <strong>Nenhum contrato encontrado.</strong>
                <p class="mb-0 mt-2">
                    Entre em contato com a administração para obter informações.
                </p>
            </div>
        @endforelse
    </div>

</main>

<footer class="portal-footer">
    m1r4ge7 — Portal do Cliente
    <br>
    Acesso privado e individual.
</footer>

</body>
</html>
