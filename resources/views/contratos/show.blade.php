
@extends('layouts.app')

@section('title', 'Detalhes do Contrato')

@section('content')

@php
    $totalRecebido = 0;
    $totalSaldo = 0;
    $parcelasPagas = 0;
    $parcelasParciais = 0;
    $parcelasPendentes = 0;

    foreach ($contrato->parcelas as $item) {
        $recebido = (float) $item->pagamentos->sum('valor');
        $saldo = max(0, (float) $item->valor - $recebido);

        $totalRecebido += $recebido;
        $totalSaldo += $saldo;

        if ($saldo <= 0) {
            $parcelasPagas++;
        } elseif ($recebido > 0) {
            $parcelasParciais++;
        } else {
            $parcelasPendentes++;
        }
    }

    $totalParcelas = $contrato->parcelas->count();

    $percentualRecebido = ($contrato->total_receber ?? 0) > 0
        ? min(100, ($totalRecebido / $contrato->total_receber) * 100)
        : 0;

    $moeda = fn($valor) => 'R$ ' . number_format((float) $valor, 2, ',', '.');
@endphp

<style>
    .contrato-detalhes {
        color: #e2e8f0;
        font-size: 13px;
        width: 100%;
    }

    .contrato-detalhes .painel {
        background: #1b222c;
        border: 1px solid #343e4b;
        border-radius: 10px;
        overflow: hidden;
    }

    .contrato-detalhes .painel-header {
        background: #202936;
        border-bottom: 1px solid #343e4b;
        padding: 12px 15px;
    }

    .contrato-detalhes .indicador {
        background: #202936;
        border: 1px solid #343e4b;
        border-radius: 9px;
        padding: 12px;
        height: 100%;
        min-width: 0;
    }

    .contrato-detalhes .indicador small,
    .contrato-detalhes .detalhe-item small {
        display: block;
        color: #aab5c3;
        font-size: 11px;
        margin-bottom: 4px;
    }

    .contrato-detalhes .indicador strong {
        font-size: 16px;
        color: #f1f5f9;
        overflow-wrap: anywhere;
    }

    .contrato-detalhes .detalhe-item {
        padding: 8px 0;
        border-bottom: 1px solid #303a47;
        overflow-wrap: anywhere;
    }

    .contrato-detalhes .detalhe-item strong {
        color: #e8edf4;
        font-weight: 500;
    }

    .contrato-detalhes .table {
        --bs-table-bg: #1b222c;
        --bs-table-color: #e2e8f0;
        --bs-table-border-color: #35404d;
        margin-bottom: 0;
    }

    .contrato-detalhes .table thead th {
        background: #252f3c;
        color: #cbd5e1;
        font-size: 11px;
        padding: 10px 8px;
        white-space: nowrap;
    }

    .contrato-detalhes .table tbody td {
        padding: 9px 8px;
        font-size: 12px;
        vertical-align: middle;
    }

    .contrato-detalhes .form-control {
        background: #111820;
        color: #e2e8f0;
        border: 1px solid #3b4755;
        font-size: 13px;
        min-height: 38px;
    }

    .contrato-detalhes .form-control:focus {
        background: #111820;
        color: white;
        border-color: #5b9bd5;
        box-shadow: none;
    }

    .contrato-detalhes input[type="date"] {
        color-scheme: dark;
    }

    .contrato-detalhes input[type="file"] {
        font-size: 12px;
    }

    .contrato-detalhes input[type="file"]::file-selector-button {
        background: #303d4d;
        color: #fff;
        border: 0;
        padding: 7px 10px;
        margin: -5px 10px -5px -5px;
        border-radius: 4px;
    }

    .contrato-detalhes .historico {
        background: #151c25;
        border: 1px solid #303a47;
        border-radius: 7px;
        padding: 9px 11px;
    }

    .contrato-detalhes .historico-item {
        padding: 9px 0;
        border-bottom: 1px solid #303a47;
        font-size: 12px;
    }

    .contrato-detalhes .historico-item:last-child {
        border-bottom: 0;
    }

    .contrato-detalhes details {
        margin-top: 8px;
    }

    .contrato-detalhes details summary {
        cursor: pointer;
        color: #80b9ed;
        font-size: 12px;
        user-select: none;
        padding: 5px 0;
    }

    .contrato-detalhes .editor {
        background: #202936;
        border: 1px solid #3b4755;
        border-radius: 7px;
        padding: 12px;
        margin-top: 8px;
    }

    .contrato-detalhes .barra-progresso {
        height: 7px;
        background: #111820;
        border-radius: 10px;
        overflow: hidden;
    }

    .contrato-detalhes .barra-progresso div {
        height: 100%;
        background: #20a879;
    }

    .contrato-detalhes .texto-secundario {
        color: #9caaba;
    }

    .contrato-detalhes .acoes {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .contrato-detalhes .botoes-comprovante {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }

    .contrato-detalhes .botoes-comprovante .btn {
        font-size: 11px;
    }

    .parcelas-mobile {
        display: none;
    }

    .parcela-card {
        background: #151c25;
        border: 1px solid #35404d;
        border-radius: 10px;
        padding: 13px;
        margin-bottom: 12px;
    }

    .parcela-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-bottom: 11px;
        margin-bottom: 11px;
        border-bottom: 1px solid #35404d;
    }

    .parcela-card-header strong {
        font-size: 15px;
        color: #fff;
    }

    .parcela-card-info {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 13px 10px;
    }

    .parcela-card-info .item {
        min-width: 0;
    }

    .parcela-card-info small {
        display: block;
        color: #9caaba;
        font-size: 11px;
        margin-bottom: 4px;
    }

    .parcela-card-info strong {
        display: block;
        color: #e8edf4;
        font-size: 13px;
        overflow-wrap: anywhere;
    }

    .parcela-card-acoes {
        display: flex;
        flex-direction: column;
        gap: 9px;
        margin-top: 14px;
        padding-top: 13px;
        border-top: 1px solid #35404d;
    }

    .parcela-card-acoes .btn {
        width: 100%;
        min-height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .parcela-card .editor {
        background: #202936;
    }

    .contrato-detalhes .painel-corpo {
        min-width: 0;
    }

    @media(max-width: 767px) {
        .contrato-detalhes {
            padding-left: 0;
            padding-right: 0;
        }

        .contrato-detalhes .indicador {
            padding: 10px;
        }

        .contrato-detalhes .indicador strong {
            font-size: 14px;
        }

        .contrato-detalhes .painel-corpo {
            padding: 12px !important;
        }

        .parcelas-desktop {
            display: none;
        }

        .parcelas-mobile {
            display: block;
        }

        .contrato-detalhes .acoes {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .contrato-detalhes .acoes > * {
            width: 100%;
        }

        .contrato-detalhes .acoes .btn {
            width: 100%;
            min-height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .contrato-detalhes .painel-header {
            padding: 11px 12px;
        }

        .contrato-detalhes .botoes-comprovante {
            display: grid;
            grid-template-columns: 1fr;
        }

        .contrato-detalhes .botoes-comprovante .btn {
            width: 100%;
            min-height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }

    @media(max-width: 380px) {
        .parcela-card {
            padding: 10px;
        }

        .parcela-card-info {
            gap: 11px 7px;
        }

        .parcela-card-info strong {
            font-size: 12px;
        }

        .contrato-detalhes .indicador strong {
            font-size: 12px;
        }
    }
</style>

<div class="container-fluid contrato-detalhes py-2">

    <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
        <div class="min-w-0">
            <h5 class="text-white mb-1">Detalhes do contrato</h5>
            <div class="texto-secundario">
                {{ $contrato->cliente->nome ?? 'Cliente não informado' }}
            </div>
        </div>

        <a href="{{ route('contratos.index') }}" class="btn btn-secondary btn-sm">
            Voltar
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger py-2">
            @foreach($errors->all() as $erro)
                <div>{{ $erro }}</div>
            @endforeach
        </div>
    @endif

    <div class="row g-2 mb-3">
        <div class="col-6 col-xl-3">
            <div class="indicador">
                <small>Capital emprestado</small>
                <strong>{{ $moeda($contrato->valor) }}</strong>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="indicador">
                <small>Total a receber</small>
                <strong>{{ $moeda($contrato->total_receber) }}</strong>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="indicador">
                <small>Total recebido</small>
                <strong class="text-success">{{ $moeda($totalRecebido) }}</strong>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="indicador">
                <small>Saldo em aberto</small>
                <strong class="{{ $totalSaldo > 0 ? 'text-warning' : 'text-success' }}">
                    {{ $moeda($totalSaldo) }}
                </strong>
            </div>
        </div>
    </div>

    <div class="painel mb-3">
        <div class="painel-header">
            <h6 class="text-white mb-0">Informações do contrato</h6>
        </div>

        <div class="painel-corpo p-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="detalhe-item">
                        <small>Cliente</small>
                        <strong>{{ $contrato->cliente->nome ?? 'Não informado' }}</strong>
                    </div>

                    <div class="detalhe-item">
                        <small>Data de início</small>
                        <strong>{{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }}</strong>
                    </div>

                    <div class="detalhe-item">
                        <small>Valor do contrato</small>
                        <strong>{{ $moeda($contrato->valor) }}</strong>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="detalhe-item">
                        <small>Quantidade de parcelas</small>
                        <strong>{{ $contrato->qtd_parcelas }}</strong>
                    </div>

                    <div class="detalhe-item">
                        <small>Intervalo</small>
                        <strong>{{ $contrato->intervalo_dias }} dias</strong>
                    </div>

                    <div class="detalhe-item">
                        <small>Juros</small>
                        <strong>{{ number_format($contrato->juros, 2, ',', '.') }}%</strong>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="detalhe-item">
                        <small>Valor da parcela</small>
                        <strong>{{ $moeda($contrato->valor_parcela) }}</strong>
                    </div>

                    <div class="detalhe-item">
                        <small>Lucro previsto</small>
                        <strong class="text-success">{{ $moeda($contrato->lucro) }}</strong>
                    </div>

                    <div class="detalhe-item">
                        <small>Observações</small>
                        <strong>{{ $contrato->observacao ?: 'Nenhuma' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="painel mb-3">
        <div class="painel-header">
            <h6 class="text-white mb-0">Resumo dos recebimentos</h6>
        </div>

        <div class="painel-corpo p-3">
            <div class="row mb-3">
                <div class="col-4 text-center">
                    <strong class="text-success fs-5">{{ $parcelasPagas }}</strong>
                    <div class="texto-secundario">Pagas</div>
                </div>

                <div class="col-4 text-center">
                    <strong class="text-warning fs-5">{{ $parcelasParciais }}</strong>
                    <div class="texto-secundario">Parciais</div>
                </div>

                <div class="col-4 text-center">
                    <strong class="text-danger fs-5">{{ $parcelasPendentes }}</strong>
                    <div class="texto-secundario">Pendentes</div>
                </div>
            </div>

            <div class="d-flex justify-content-between small mb-1">
                <span class="texto-secundario">Progresso financeiro</span>
                <strong>{{ number_format($percentualRecebido, 1, ',', '.') }}%</strong>
            </div>

            <div class="barra-progresso">
                <div style="width: {{ $percentualRecebido }}%"></div>
            </div>
        </div>
    </div>

    <div class="painel mb-3">
        <div class="painel-header d-flex justify-content-between align-items-center">
            <h6 class="text-white mb-0">Parcelas e recebimentos</h6>
            <span class="badge bg-secondary">{{ $totalParcelas }} parcelas</span>
        </div>

        <div class="painel-corpo p-2">

            {{-- DESKTOP --}}
            <div class="parcelas-desktop">
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Vencimento</th>
                                <th>Valor</th>
                                <th>Recebido</th>
                                <th>Saldo</th>
                                <th>Status</th>
                                <th style="min-width:190px">Ações</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($contrato->parcelas as $parcela)
                                @php
                                    $recebido = (float) $parcela->pagamentos->sum('valor');
                                    $saldo = max(0, (float) $parcela->valor - $recebido);
                                @endphp

                                <tr>
                                    <td>{{ $parcela->numero }}</td>

                                    <td class="text-nowrap">
                                        {{ $parcela->data_vencimento->format('d/m/Y') }}
                                    </td>

                                    <td class="text-nowrap">{{ $moeda($parcela->valor) }}</td>
                                    <td class="text-success text-nowrap">{{ $moeda($recebido) }}</td>

                                    <td class="{{ $saldo > 0 ? 'text-warning' : 'text-success' }} text-nowrap">
                                        {{ $moeda($saldo) }}
                                    </td>

                                    <td>
                                        @if($saldo <= 0)
                                            <span class="badge bg-success">Paga</span>
                                        @elseif($recebido > 0)
                                            <span class="badge bg-warning text-dark">Parcial</span>
                                        @else
                                            <span class="badge bg-danger">Pendente</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($saldo > 0)
                                            <form action="{{ route('parcelas.pagamentos.store', $parcela->id) }}"
                                                  method="POST"
                                                  enctype="multipart/form-data"
                                                  class="mb-2">
                                                @csrf

                                                <input type="number"
                                                       name="valor"
                                                       class="form-control form-control-sm mb-1"
                                                       min="0.01"
                                                       step="0.01"
                                                       placeholder="Valor recebido"
                                                       required>

                                                <input type="date"
                                                       name="data_pagamento"
                                                       class="form-control form-control-sm mb-1"
                                                       value="{{ now()->format('Y-m-d') }}"
                                                       required>

                                                <label class="small mb-1">Comprovante (opcional)</label>
                                                <input type="file"
                                                       name="comprovante"
                                                       class="form-control form-control-sm mb-2"
                                                       accept=".pdf,.jpg,.jpeg,.png">

                                                <button type="submit" class="btn btn-success btn-sm w-100">
                                                    Registrar recebimento
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-success small">Totalmente recebida</span>
                                        @endif

                                        <details>
                                            <summary>Editar parcela</summary>

                                            <div class="editor">
                                                <form action="{{ route('parcelas.update', $parcela->id) }}"
                                                      method="POST">
                                                    @csrf

                                                    <label class="small mb-1">Número</label>
                                                    <input type="number"
                                                           name="numero"
                                                           class="form-control form-control-sm mb-2"
                                                           min="1"
                                                           value="{{ $parcela->numero }}"
                                                           required>

                                                    <label class="small mb-1">Vencimento</label>
                                                    <input type="date"
                                                           name="data_vencimento"
                                                           class="form-control form-control-sm mb-2"
                                                           value="{{ $parcela->data_vencimento->format('Y-m-d') }}"
                                                           required>

                                                    <label class="small mb-1">Valor da parcela</label>
                                                    <input type="number"
                                                           name="valor"
                                                           class="form-control form-control-sm mb-2"
                                                           min="0.01"
                                                           step="0.01"
                                                           value="{{ number_format($parcela->valor, 2, '.', '') }}"
                                                           required>

                                                    <button class="btn btn-warning btn-sm w-100">
                                                        Salvar parcela
                                                    </button>
                                                </form>
                                            </div>
                                        </details>
                                    </td>
                                </tr>

                                @if($parcela->pagamentos->isNotEmpty())
                                    <tr>
                                        <td colspan="7">
                                            <div class="historico">
                                                <div class="fw-semibold text-white small mb-1">
                                                    Histórico de recebimentos
                                                </div>

                                                @foreach($parcela->pagamentos as $pagamento)
                                                    <div class="historico-item">
                                                        <div class="d-flex flex-wrap justify-content-between gap-2">
                                                            <div>
                                                                <span class="texto-secundario">
                                                                    {{ $pagamento->data_pagamento->format('d/m/Y') }}
                                                                </span>

                                                                <strong class="text-success ms-2">
                                                                    {{ $moeda($pagamento->valor) }}
                                                                </strong>
                                                            </div>

                                                            <div>
                                                                <span class="texto-secundario">Pago por:</span>
                                                                <strong class="text-white">
                                                                    {{ $contrato->cliente->nome ?? 'Cliente não identificado' }}
                                                                </strong>
                                                            </div>
                                                        </div>

                                                        <div class="botoes-comprovante">
                                                            <a href="{{ route('pagamentos.recibo', $pagamento->id) }}"
                                                               target="_blank"
                                                               class="btn btn-outline-light btn-sm">
                                                                <i class="fas fa-file-pdf me-1"></i>
                                                                Emitir recibo
                                                            </a>

                                                            @if($pagamento->comprovante)
                                                                <a href="{{ route('pagamentos.comprovante', $pagamento->id) }}"
                                                                   target="_blank"
                                                                   class="btn btn-outline-info btn-sm">
                                                                    <i class="fas fa-eye me-1"></i>
                                                                    Ver comprovante
                                                                </a>

                                                                <a href="{{ route('pagamentos.comprovante.baixar', $pagamento->id) }}"
                                                                   class="btn btn-outline-light btn-sm">
                                                                    <i class="fas fa-download me-1"></i>
                                                                    Baixar comprovante
                                                                </a>
                                                            @endif
                                                        </div>

                                                        <details>
                                                            <summary>Editar recebimento</summary>

                                                            <div class="editor">
                                                                <form action="{{ route('pagamentos.update', $pagamento->id) }}"
                                                                      method="POST"
                                                                      enctype="multipart/form-data">
                                                                    @csrf
                                                                    @method('PUT')

                                                                    <label class="small mb-1">Valor recebido</label>
                                                                    <input type="number"
                                                                           name="valor"
                                                                           class="form-control form-control-sm mb-2"
                                                                           min="0.01"
                                                                           step="0.01"
                                                                           value="{{ number_format($pagamento->valor, 2, '.', '') }}"
                                                                           required>

                                                                    <label class="small mb-1">Data do recebimento</label>
                                                                    <input type="date"
                                                                           name="data_pagamento"
                                                                           class="form-control form-control-sm mb-2"
                                                                           value="{{ $pagamento->data_pagamento->format('Y-m-d') }}"
                                                                           required>

                                                                    <label class="small mb-1">Substituir comprovante (opcional)</label>
                                                                    <input type="file"
                                                                           name="comprovante"
                                                                           class="form-control form-control-sm mb-2"
                                                                           accept=".pdf,.jpg,.jpeg,.png">

                                                                    @if($pagamento->comprovante)
                                                                        <small class="text-info d-block mb-2">
                                                                            Já existe um comprovante anexado.
                                                                            Envie outro arquivo somente se desejar substituí-lo.
                                                                        </small>
                                                                    @endif

                                                                    <button class="btn btn-warning btn-sm w-100">
                                                                        Salvar recebimento
                                                                    </button>
                                                                </form>

                                                                <form action="{{ route('pagamentos.destroy', $pagamento->id) }}"
                                                                      method="POST"
                                                                      class="mt-2"
                                                                      onsubmit="return confirm('Deseja realmente excluir este recebimento?')">
                                                                    @csrf
                                                                    @method('DELETE')

                                                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                                        Excluir recebimento
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </details>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3">
                                        Nenhuma parcela cadastrada.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MOBILE --}}
            <div class="parcelas-mobile">
                @forelse($contrato->parcelas as $parcela)
                    @php
                        $recebido = (float) $parcela->pagamentos->sum('valor');
                        $saldo = max(0, (float) $parcela->valor - $recebido);
                    @endphp

                    <div class="parcela-card">
                        <div class="parcela-card-header">
                            <strong>Parcela {{ $parcela->numero }}</strong>

                            @if($saldo <= 0)
                                <span class="badge bg-success">Paga</span>
                            @elseif($recebido > 0)
                                <span class="badge bg-warning text-dark">Parcial</span>
                            @else
                                <span class="badge bg-danger">Pendente</span>
                            @endif
                        </div>

                        <div class="parcela-card-info">
                            <div class="item">
                                <small>Vencimento</small>
                                <strong>{{ $parcela->data_vencimento->format('d/m/Y') }}</strong>
                            </div>

                            <div class="item">
                                <small>Valor da parcela</small>
                                <strong>{{ $moeda($parcela->valor) }}</strong>
                            </div>

                            <div class="item">
                                <small>Valor recebido</small>
                                <strong class="text-success">{{ $moeda($recebido) }}</strong>
                            </div>

                            <div class="item">
                                <small>Saldo restante</small>
                                <strong class="{{ $saldo > 0 ? 'text-warning' : 'text-success' }}">
                                    {{ $moeda($saldo) }}
                                </strong>
                            </div>
                        </div>

                        <div class="parcela-card-acoes">
                            @if($saldo > 0)
                                <details>
                                    <summary>
                                        <span class="btn btn-success">
                                            Registrar recebimento
                                        </span>
                                    </summary>

                                    <div class="editor">
                                        <form action="{{ route('parcelas.pagamentos.store', $parcela->id) }}"
                                              method="POST"
                                              enctype="multipart/form-data">
                                            @csrf

                                            <label class="small mb-1">Valor recebido</label>
                                            <input type="number"
                                                   name="valor"
                                                   class="form-control mb-2"
                                                   min="0.01"
                                                   step="0.01"
                                                   placeholder="Informe o valor"
                                                   required>

                                            <label class="small mb-1">Data do pagamento</label>
                                            <input type="date"
                                                   name="data_pagamento"
                                                   class="form-control mb-2"
                                                   value="{{ now()->format('Y-m-d') }}"
                                                   required>

                                            <label class="small mb-1">Comprovante (opcional)</label>
                                            <input type="file"
                                                   name="comprovante"
                                                   class="form-control mb-3"
                                                   accept=".pdf,.jpg,.jpeg,.png">

                                            <button type="submit" class="btn btn-success">
                                                Confirmar recebimento
                                            </button>
                                        </form>
                                    </div>
                                </details>
                            @else
                                <div class="text-center text-success small py-2">
                                    <strong>Parcela totalmente recebida</strong>
                                </div>
                            @endif

                            <details>
                                <summary>
                                    <span class="btn btn-warning">
                                        Editar parcela
                                    </span>
                                </summary>

                                <div class="editor">
                                    <form action="{{ route('parcelas.update', $parcela->id) }}"
                                          method="POST">
                                        @csrf

                                        <label class="small mb-1">Número da parcela</label>
                                        <input type="number"
                                               name="numero"
                                               class="form-control mb-2"
                                               min="1"
                                               value="{{ $parcela->numero }}"
                                               required>

                                        <label class="small mb-1">Vencimento</label>
                                        <input type="date"
                                               name="data_vencimento"
                                               class="form-control mb-2"
                                               value="{{ $parcela->data_vencimento->format('Y-m-d') }}"
                                               required>

                                        <label class="small mb-1">Valor da parcela</label>
                                        <input type="number"
                                               name="valor"
                                               class="form-control mb-2"
                                               min="0.01"
                                               step="0.01"
                                               value="{{ number_format($parcela->valor, 2, '.', '') }}"
                                               required>

                                        <button type="submit" class="btn btn-warning">
                                            Salvar alterações
                                        </button>
                                    </form>
                                </div>
                            </details>
                        </div>

                        @if($parcela->pagamentos->isNotEmpty())
                            <details>
                                <summary>
                                    Histórico de recebimentos ({{ $parcela->pagamentos->count() }})
                                </summary>

                                <div class="historico">
                                    @foreach($parcela->pagamentos as $pagamento)
                                        <div class="historico-item">
                                            <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                                                <strong class="text-success">
                                                    {{ $moeda($pagamento->valor) }}
                                                </strong>

                                                <span class="texto-secundario">
                                                    {{ $pagamento->data_pagamento->format('d/m/Y') }}
                                                </span>
                                            </div>

                                            <div class="small texto-secundario mb-2">
                                                Pago por:
                                                <strong class="text-white">
                                                    {{ $contrato->cliente->nome ?? 'Cliente não identificado' }}
                                                </strong>
                                            </div>

                                            <div class="botoes-comprovante">
                                                <a href="{{ route('pagamentos.recibo', $pagamento->id) }}"
                                                   target="_blank"
                                                   class="btn btn-outline-light btn-sm">
                                                    <i class="fas fa-file-pdf me-1"></i>
                                                    Emitir recibo
                                                </a>

                                                @if($pagamento->comprovante)
                                                    <a href="{{ route('pagamentos.comprovante', $pagamento->id) }}"
                                                       target="_blank"
                                                       class="btn btn-outline-info btn-sm">
                                                        <i class="fas fa-eye me-1"></i>
                                                        Ver comprovante
                                                    </a>

                                                    <a href="{{ route('pagamentos.comprovante.baixar', $pagamento->id) }}"
                                                       class="btn btn-outline-light btn-sm">
                                                        <i class="fas fa-download me-1"></i>
                                                        Baixar comprovante
                                                    </a>
                                                @endif
                                            </div>

                                            <details>
                                                <summary>Editar recebimento</summary>

                                                <div class="editor">
                                                    <form action="{{ route('pagamentos.update', $pagamento->id) }}"
                                                          method="POST"
                                                          enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')

                                                        <label class="small mb-1">Valor recebido</label>
                                                        <input type="number"
                                                               name="valor"
                                                               class="form-control mb-2"
                                                               min="0.01"
                                                               step="0.01"
                                                               value="{{ number_format($pagamento->valor, 2, '.', '') }}"
                                                               required>

                                                        <label class="small mb-1">Data do recebimento</label>
                                                        <input type="date"
                                                               name="data_pagamento"
                                                               class="form-control mb-2"
                                                               value="{{ $pagamento->data_pagamento->format('Y-m-d') }}"
                                                               required>

                                                        <label class="small mb-1">Substituir comprovante (opcional)</label>
                                                        <input type="file"
                                                               name="comprovante"
                                                               class="form-control mb-2"
                                                               accept=".pdf,.jpg,.jpeg,.png">

                                                        @if($pagamento->comprovante)
                                                            <small class="text-info d-block mb-2">
                                                                Já existe um comprovante anexado.
                                                                Envie outro arquivo somente se desejar substituí-lo.
                                                            </small>
                                                        @endif

                                                        <button type="submit" class="btn btn-warning">
                                                            Salvar recebimento
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('pagamentos.destroy', $pagamento->id) }}"
                                                          method="POST"
                                                          class="mt-2"
                                                          onsubmit="return confirm('Deseja realmente excluir este recebimento?')">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-outline-danger">
                                                            Excluir recebimento
                                                        </button>
                                                    </form>
                                                </div>
                                            </details>
                                        </div>
                                    @endforeach
                                </div>
                            </details>
                        @endif
                    </div>
                @empty
                    <div class="text-center text-warning py-4">
                        Nenhuma parcela cadastrada.
                    </div>
                @endforelse
            </div>

        </div>
    </div>

    <div class="acoes mb-3">
        <a href="{{ route('contratos.index') }}" class="btn btn-secondary btn-sm">
            Voltar
        </a>

        <a href="{{ route('contratos.edit', $contrato->id) }}" class="btn btn-warning btn-sm">
            Editar contrato
        </a>

        <a href="{{ route('contratos.gerarPdf', $contrato->id) }}"
           class="btn btn-danger btn-sm"
           target="_blank">
            Gerar PDF
        </a>

        <form action="{{ route('contratos.destroy', $contrato->id) }}"
              method="POST"
              onsubmit="return confirm('Tem certeza que deseja excluir este contrato?')">
            @csrf
            @method('DELETE')

            <button class="btn btn-outline-danger btn-sm">
                Excluir contrato
            </button>
        </form>
    </div>

</div>

@endsection
