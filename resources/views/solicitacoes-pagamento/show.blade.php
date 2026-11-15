@extends('layouts.app')

@section('title', 'Analisar Pagamento')

@section('content')
<style>
    .analise-page {
        color: #e8edf5;
    }

    .analise-page .page-title {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .analise-page .subtitle {
        color: #999;
        font-size: 13px;
        margin-bottom: 22px;
    }

    .analise-page .panel {
        background: #1c1c1c;
        border: 1px solid #353535;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 18px;
    }

    .analise-page .panel-title {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 17px;
        color: #fff;
    }

    .analise-page .info-label {
        color: #999;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .analise-page .info-value {
        color: #eee;
        font-size: 14px;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .analise-page .info-item {
        margin-bottom: 17px;
    }

    .analise-page .amount {
        font-size: 25px;
        color: #42d69b;
        font-weight: 800;
    }

    .analise-page .proof-frame {
        width: 100%;
        min-height: 500px;
        border: 1px solid #444;
        border-radius: 8px;
        background: #111;
    }

    .analise-page .proof-image {
        width: 100%;
        max-height: 700px;
        object-fit: contain;
        background: #111;
        border-radius: 8px;
    }

    .analise-page .btn-back {
        background: #303030;
        color: #ddd;
        border: 1px solid #444;
        border-radius: 8px;
        padding: 9px 14px;
        text-decoration: none;
        display: inline-block;
        font-size: 13px;
    }

    .analise-page .btn-approve {
        background: #15945e;
        color: #fff;
        border: 0;
        border-radius: 8px;
        padding: 11px 16px;
        font-weight: 700;
        width: 100%;
    }

    .analise-page .btn-reject {
        background: #b83240;
        color: #fff;
        border: 0;
        border-radius: 8px;
        padding: 11px 16px;
        font-weight: 700;
        width: 100%;
    }

    .analise-page .form-control {
        background: #111;
        border: 1px solid #444;
        color: #eee;
    }

    .analise-page .form-control:focus {
        background: #111;
        color: #fff;
        border-color: #1689d4;
        box-shadow: 0 0 0 .2rem rgba(22,137,212,.15);
    }

    .analise-page .form-control::placeholder {
        color: #777;
    }

    .analise-page .balance-value {
        font-size: 19px;
        font-weight: 700;
        color: #f5c451;
    }

    @media(max-width: 600px) {
        .analise-page .panel { padding: 15px; }
        .analise-page .page-title { font-size: 19px; }
        .analise-page .proof-frame { min-height: 350px; }
    }
</style>

@php
    $parcela = $solicitacao->parcela;
    $contrato = $parcela?->contrato;
    $cliente = $contrato?->cliente;

    $statusConfig = [
        'pendente' => ['Aguardando análise', 'warning'],
        'aprovado' => ['Aprovado', 'success'],
        'rejeitado' => ['Rejeitado', 'danger'],
    ];

    $config = $statusConfig[$solicitacao->status] ?? [
        ucfirst($solicitacao->status),
        'secondary',
    ];

    $valorSolicitado = (float) $solicitacao->valor;

    $podeAprovar = $solicitacao->status === 'pendente'
        && $valorSolicitado > 0
        && $valorSolicitado <= $saldoRestante;
@endphp

<div class="analise-page">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h2 class="page-title">
                <i class="fas fa-file-invoice-dollar me-2"></i>
                Analisar pagamento
            </h2>

            <div class="subtitle">
                Solicitação #{{ $solicitacao->id }}
            </div>
        </div>

        <a href="{{ route('admin.solicitacoes-pagamento.index') }}"
           class="btn-back">
            <i class="fas fa-arrow-left me-1"></i>
            Voltar
        </a>
    </div>

    <div class="panel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div class="panel-title mb-0">
                Situação da solicitação
            </div>

            <span class="badge bg-{{ $config[1] }} fs-6">
                {{ $config[0] }}
            </span>
        </div>

        <div class="row">
            <div class="col-6 col-md-3 info-item">
                <div class="info-label">Valor solicitado</div>
                <div class="amount">
                    R$ {{ number_format($valorSolicitado, 2, ',', '.') }}
                </div>
            </div>

            <div class="col-6 col-md-3 info-item">
                <div class="info-label">Total já pago no contrato</div>
                <div class="info-value">
                    R$ {{ number_format($totalPago, 2, ',', '.') }}
                </div>
            </div>

            <div class="col-6 col-md-3 info-item">
                <div class="info-label">Saldo restante do contrato</div>
                <div class="balance-value">
                    R$ {{ number_format($saldoRestante, 2, ',', '.') }}
                </div>
            </div>

            <div class="col-6 col-md-3 info-item">
                <div class="info-label">Data informada</div>
                <div class="info-value">
                    {{ $solicitacao->data_pagamento?->format('d/m/Y') ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    <div class="panel">
        <div class="panel-title">
            <i class="fas fa-user me-2"></i>
            Dados do cliente e contrato
        </div>

        <div class="row">
            <div class="col-md-6 info-item">
                <div class="info-label">Cliente</div>
                <div class="info-value">
                    {{ $cliente?->nome ?? $cliente?->name ?? 'Não identificado' }}
                </div>
            </div>

            <div class="col-6 col-md-3 info-item">
                <div class="info-label">Contrato</div>
                <div class="info-value">
                    {{ $contrato?->numero ?? $contrato?->id ?? '-' }}
                </div>
            </div>

            <div class="col-6 col-md-3 info-item">
                <div class="info-label">Parcela de referência</div>
                <div class="info-value">
                    {{ $parcela?->numero ?? '-' }}
                </div>
            </div>
        </div>

        @if($solicitacao->observacao)
            <div class="mt-2">
                <div class="info-label">Observação do cliente</div>
                <div class="info-value">
                    {{ $solicitacao->observacao }}
                </div>
            </div>
        @endif
    </div>

    <div class="panel">
        <div class="panel-title">
            <i class="fas fa-paperclip me-2"></i>
            Comprovante enviado pelo cliente
        </div>

        @php
            $extensao = strtolower(
                pathinfo(
                    $solicitacao->comprovante ?? '',
                    PATHINFO_EXTENSION
                )
            );

            $urlComprovante = route(
                'admin.solicitacoes-pagamento.comprovante',
                $solicitacao->id
            );
        @endphp

        @if(in_array($extensao, ['jpg', 'jpeg', 'png']))
            <a href="{{ $urlComprovante }}" target="_blank" rel="noopener">
                <img
                    src="{{ $urlComprovante }}"
                    alt="Comprovante de pagamento"
                    class="proof-image"
                >
            </a>
        @elseif($extensao === 'pdf')
            <iframe
                src="{{ $urlComprovante }}"
                class="proof-frame"
                title="Comprovante PDF"
            ></iframe>
        @else
            <div class="alert alert-warning">
                Não foi possível identificar o formato do comprovante.
            </div>
        @endif

        <div class="mt-3">
            <a href="{{ $urlComprovante }}"
               target="_blank"
               rel="noopener"
               class="btn-back">
                <i class="fas fa-external-link-alt me-1"></i>
                Abrir comprovante em nova aba
            </a>
        </div>
    </div>

    @if($solicitacao->status === 'pendente')

        <div class="row">

            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-title text-success">
                        <i class="fas fa-check-circle me-2"></i>
                        Aprovar pagamento
                    </div>

                    @if($podeAprovar)

                        <p class="text-secondary small">
                            Ao aprovar, o valor será distribuído automaticamente
                            entre as parcelas em aberto, começando pela mais antiga.
                        </p>

                        <div class="alert alert-success py-2">
                            Valor a registrar:
                            <strong>
                                R$ {{ number_format($valorSolicitado, 2, ',', '.') }}
                            </strong>
                        </div>

                        <div class="alert alert-info py-2 small">
                            O sistema criará os lançamentos necessários para
                            atualizar cada parcela atingida pelo pagamento.
                        </div>

                        <form
                            method="POST"
                            action="{{ route('admin.solicitacoes-pagamento.aprovar', $solicitacao->id) }}"
                            onsubmit="return confirm('Confirma a aprovação? O pagamento será distribuído entre as parcelas em aberto do contrato.');"
                        >
                            @csrf

                            <button type="submit" class="btn-approve">
                                <i class="fas fa-check me-2"></i>
                                Confirmar aprovação
                            </button>
                        </form>

                    @else

                        <div class="alert alert-danger mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Não é possível aprovar esta solicitação.
                            O valor solicitado ultrapassa o saldo total do contrato
                            ou não é válido.
                        </div>

                    @endif
                </div>
            </div>

            <div class="col-lg-6">
                <div class="panel h-100">
                    <div class="panel-title text-danger">
                        <i class="fas fa-times-circle me-2"></i>
                        Rejeitar pagamento
                    </div>

                    <p class="text-secondary small">
                        Informe o motivo da rejeição para deixar registrado.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('admin.solicitacoes-pagamento.rejeitar', $solicitacao->id) }}"
                        onsubmit="return confirm('Confirma a rejeição desta solicitação?');"
                    >
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small">
                                Motivo da rejeição
                            </label>

                            <textarea
                                name="motivo_rejeicao"
                                class="form-control"
                                rows="3"
                                minlength="3"
                                maxlength="1000"
                                required
                                placeholder="Ex.: Comprovante ilegível ou valor divergente."
                            >{{ old('motivo_rejeicao') }}</textarea>

                            @error('motivo_rejeicao')
                                <div class="text-danger small mt-1">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn-reject">
                            <i class="fas fa-times me-2"></i>
                            Rejeitar solicitação
                        </button>
                    </form>
                </div>
            </div>

        </div>

    @elseif($solicitacao->status === 'rejeitado')

        <div class="panel">
            <div class="panel-title text-danger">
                <i class="fas fa-times-circle me-2"></i>
                Solicitação rejeitada
            </div>

            <p class="mb-0">
                <strong>Motivo:</strong>
                {{ $solicitacao->motivo_rejeicao ?? 'Não informado.' }}
            </p>
        </div>

    @elseif($solicitacao->status === 'aprovado')

        <div class="panel">
            <div class="panel-title text-success">
                <i class="fas fa-check-circle me-2"></i>
                Pagamento aprovado
            </div>

            <p class="mb-0 text-secondary">
                Este pagamento já foi aprovado e distribuído entre as parcelas
                do contrato.
            </p>
        </div>

    @endif

</div>
@endsection
