
@extends('layouts.app')

@section('title', 'Solicitações de Pagamento')

@section('content')
<div class="container-fluid py-3">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="mb-1">Solicitações de Pagamento</h3>
            <p class="text-secondary mb-0">Comprovantes enviados pelos clientes.</p>
        </div>

        <span class="badge bg-warning text-dark fs-6">
            {{ $pendentes }} pendente(s)
        </span>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('admin.solicitacoes-pagamento.index', ['status' => 'pendente']) }}"
           class="btn {{ $status === 'pendente' ? 'btn-primary' : 'btn-outline-secondary' }}">
            Pendentes
        </a>

        <a href="{{ route('admin.solicitacoes-pagamento.index', ['status' => 'aprovado']) }}"
           class="btn {{ $status === 'aprovado' ? 'btn-success' : 'btn-outline-secondary' }}">
            Aprovadas
        </a>

        <a href="{{ route('admin.solicitacoes-pagamento.index', ['status' => 'rejeitado']) }}"
           class="btn {{ $status === 'rejeitado' ? 'btn-danger' : 'btn-outline-secondary' }}">
            Rejeitadas
        </a>

        <a href="{{ route('admin.solicitacoes-pagamento.index', ['status' => 'todos']) }}"
           class="btn {{ $status === 'todos' ? 'btn-dark' : 'btn-outline-secondary' }}">
            Todas
        </a>
    </div>

    @forelse($solicitacoes as $solicitacao)
        @php
            $parcela = $solicitacao->parcela;
            $contrato = $parcela->contrato;
            $cliente = $contrato->cliente;
        @endphp

        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between gap-3">

                    <div>
                        <h5 class="mb-1">{{ $cliente->nome }}</h5>

                        <div class="text-secondary small">
                            Contrato #{{ $contrato->id }}
                            · Parcela {{ $parcela->numero }}
                        </div>

                        <div class="mt-2">
                            <strong>Valor informado:</strong>
                            R$ {{ number_format($solicitacao->valor, 2, ',', '.') }}
                        </div>

                        <div class="small text-secondary">
                            Data informada:
                            {{ $solicitacao->data_pagamento->format('d/m/Y') }}
                        </div>
                    </div>

                    <div class="text-end">
                        @if($solicitacao->status === 'pendente')
                            <span class="badge bg-warning text-dark">Pendente</span>
                        @elseif($solicitacao->status === 'aprovado')
                            <span class="badge bg-success">Aprovado</span>
                        @else
                            <span class="badge bg-danger">Rejeitado</span>
                        @endif

                        <div class="mt-3">
                            <a href="{{ route('admin.solicitacoes-pagamento.show', $solicitacao->id) }}"
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                Ver comprovante e detalhes
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary">
            Nenhuma solicitação encontrada nesta categoria.
        </div>
    @endforelse

    <div class="mt-3">
        {{ $solicitacoes->links() }}
    </div>

</div>
@endsection
