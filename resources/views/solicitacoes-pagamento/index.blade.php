
@extends('layouts.app')

@section('title', 'Pagamentos do Portal')

@section('content')
<style>
    .solicitacoes-page {
        color: #e8edf5;
    }

    .solicitacoes-page .page-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
    }

    .solicitacoes-page .page-heading h2 {
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }

    .solicitacoes-page .subtitle {
        color: #9299a5;
        font-size: 13px;
        margin-top: 5px;
    }

    .solicitacoes-page .stat-card,
    .solicitacoes-page .request-card {
        background: #1c1c1c;
        border: 1px solid #333;
        border-radius: 12px;
    }

    .solicitacoes-page .stat-card {
        padding: 17px;
        margin-bottom: 20px;
    }

    .solicitacoes-page .stat-label {
        color: #a5a5a5;
        font-size: 12px;
    }

    .solicitacoes-page .stat-value {
        font-size: 25px;
        font-weight: 700;
        margin-top: 5px;
    }

    .solicitacoes-page .request-card {
        padding: 17px;
        margin-bottom: 12px;
        transition: border-color .2s;
    }

    .solicitacoes-page .request-card:hover {
        border-color: #1689d4;
    }

    .solicitacoes-page .request-title {
        font-weight: 700;
        font-size: 15px;
        color: #fff;
    }

    .solicitacoes-page .request-info {
        color: #a8a8a8;
        font-size: 13px;
        line-height: 1.8;
    }

    .solicitacoes-page .request-value {
        color: #42d69b;
        font-weight: 700;
        font-size: 19px;
    }

    .solicitacoes-page .filter-area {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 18px;
    }

    .solicitacoes-page .filter-area a {
        color: #cbd5e1;
        background: #242424;
        border: 1px solid #3a3a3a;
        border-radius: 8px;
        padding: 8px 13px;
        text-decoration: none;
        font-size: 13px;
    }

    .solicitacoes-page .filter-area a.active {
        background: #0879bd;
        border-color: #0879bd;
        color: #fff;
    }

    .solicitacoes-page .btn-detail {
        background: #0879bd;
        color: #fff;
        border: 0;
        border-radius: 8px;
        padding: 9px 14px;
        font-size: 13px;
        text-decoration: none;
        display: inline-block;
    }

    .solicitacoes-page .btn-detail:hover {
        background: #09669c;
        color: #fff;
    }

    .solicitacoes-page .empty-state {
        text-align: center;
        padding: 45px 15px;
        color: #999;
        background: #1c1c1c;
        border: 1px dashed #444;
        border-radius: 12px;
    }

    .solicitacoes-page .empty-state i {
        font-size: 35px;
        margin-bottom: 12px;
        color: #555;
    }

    .solicitacoes-page .pagination {
        margin-top: 20px;
    }

    .solicitacoes-page .page-link {
        background: #222;
        border-color: #444;
        color: #ddd;
    }

    .solicitacoes-page .page-item.active .page-link {
        background: #0879bd;
        border-color: #0879bd;
    }

    @media (max-width: 600px) {
        .solicitacoes-page .page-heading h2 {
            font-size: 19px;
        }

        .solicitacoes-page .request-card {
            padding: 14px;
        }

        .solicitacoes-page .request-card .card-actions {
            margin-top: 12px;
        }

        .solicitacoes-page .btn-detail {
            display: block;
            text-align: center;
            width: 100%;
        }
    }
</style>

<div class="solicitacoes-page">

    <div class="page-heading">
        <div>
            <h2>
                <i class="fas fa-file-invoice-dollar me-2"></i>
                Pagamentos do Portal
            </h2>
            <div class="subtitle">
                Analise comprovantes e aprove ou rejeite pagamentos enviados pelos clientes.
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-label">
            <i class="fas fa-clock me-1"></i>
            Solicitações aguardando análise
        </div>
        <div class="stat-value">
            {{ $pendentes }}
        </div>
    </div>

    <div class="filter-area">
        <a href="{{ route('admin.solicitacoes-pagamento.index', ['status' => 'pendente']) }}"
           class="{{ $status === 'pendente' ? 'active' : '' }}">
            <i class="fas fa-clock me-1"></i> Pendentes
        </a>

        <a href="{{ route('admin.solicitacoes-pagamento.index', ['status' => 'aprovado']) }}"
           class="{{ $status === 'aprovado' ? 'active' : '' }}">
            <i class="fas fa-check-circle me-1"></i> Aprovados
        </a>

        <a href="{{ route('admin.solicitacoes-pagamento.index', ['status' => 'rejeitado']) }}"
           class="{{ $status === 'rejeitado' ? 'active' : '' }}">
            <i class="fas fa-times-circle me-1"></i> Rejeitados
        </a>

        <a href="{{ route('admin.solicitacoes-pagamento.index', ['status' => 'todos']) }}"
           class="{{ $status === 'todos' ? 'active' : '' }}">
            <i class="fas fa-list me-1"></i> Todos
        </a>
    </div>

    @forelse($solicitacoes as $solicitacao)
        @php
            $parcela = $solicitacao->parcela;
            $contrato = $parcela?->contrato;
            $cliente = $contrato?->cliente;

            $statusConfig = [
                'pendente' => ['Aguardando análise', 'warning', 'clock'],
                'aprovado' => ['Aprovado', 'success', 'check-circle'],
                'rejeitado' => ['Rejeitado', 'danger', 'times-circle'],
            ];

            $config = $statusConfig[$solicitacao->status] ?? [
                ucfirst($solicitacao->status), 'secondary', 'question-circle'
            ];
        @endphp

        <div class="request-card">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <div class="request-title">
                        {{ $cliente?->nome ?? $cliente?->name ?? 'Cliente não identificado' }}
                    </div>

                    <div class="request-info">
                        <div>
                            <i class="fas fa-file-contract me-1"></i>
                            Contrato #{{ $contrato?->id ?? '-' }}
                        </div>

                        <div>
                            <i class="fas fa-list-ol me-1"></i>
                            Parcela {{ $parcela?->numero ?? '-' }}
                        </div>

                        <div>
                            <i class="fas fa-calendar-alt me-1"></i>
                            Data informada:
                            {{ $solicitacao->data_pagamento?->format('d/m/Y') ?? '-' }}
                        </div>

                        <div>
                            <i class="fas fa-paperclip me-1"></i>
                            Comprovante enviado
                        </div>
                    </div>
                </div>

                <div class="text-md-end">
                    <div class="request-value">
                        R$ {{ number_format((float) $solicitacao->valor, 2, ',', '.') }}
                    </div>

                    <span class="badge bg-{{ $config[1] }} mt-2">
                        <i class="fas fa-{{ $config[2] }} me-1"></i>
                        {{ $config[0] }}
                    </span>
                </div>
            </div>

            @if($solicitacao->observacao)
                <div class="request-info mt-2">
                    <strong>Observação:</strong>
                    {{ $solicitacao->observacao }}
                </div>
            @endif

            @if($solicitacao->status === 'rejeitado' && $solicitacao->motivo_rejeicao)
                <div class="alert alert-danger py-2 mt-3 mb-0 small">
                    <strong>Motivo da rejeição:</strong>
                    {{ $solicitacao->motivo_rejeicao }}
                </div>
            @endif

            <div class="card-actions mt-3">
                <a href="{{ route('admin.solicitacoes-pagamento.show', $solicitacao->id) }}"
                   class="btn-detail">
                    <i class="fas fa-eye me-1"></i>
                    Analisar solicitação
                </a>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-inbox d-block"></i>
            <div class="fw-semibold">Nenhuma solicitação encontrada</div>
            <div class="small mt-1">
                Não existem solicitações nesta categoria.
            </div>
        </div>
    @endforelse

    @if($solicitacoes->hasPages())
        <div>
            {{ $solicitacoes->links() }}
        </div>
    @endif

</div>
@endsection
