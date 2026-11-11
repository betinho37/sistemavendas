
@extends('layouts.app')

@section('title', 'Contratos')

@section('content')
<style>
    .contratos-page {
        color: #e8edf5;
    }

    .contratos-page .contratos-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .contratos-page .contratos-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .contrato-card {
        background: #151b26;
        border: 1px solid #293344;
        border-radius: 14px;
        padding: 16px;
        margin-bottom: 14px;
    }

    .contrato-card .cliente {
        font-size: 1.05rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 14px;
        overflow-wrap: anywhere;
    }

    .contrato-info {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 13px;
    }

    .contrato-info .item {
        min-width: 0;
    }

    .contrato-info .label {
        display: block;
        font-size: .75rem;
        color: #8d9aab;
        margin-bottom: 4px;
    }

    .contrato-info .valor {
        font-size: .92rem;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .contrato-info .destaque {
        color: #35d49a;
    }

    .contrato-info .lucro {
        color: #ffc857;
    }

    .contrato-acoes {
        display: flex;
        gap: 8px;
        margin-top: 16px;
        padding-top: 14px;
        border-top: 1px solid #293344;
    }

    .contrato-acoes a,
    .contrato-acoes button {
        flex: 1;
        min-height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
    }

    .contratos-desktop {
        overflow-x: auto;
        border-radius: 12px;
    }

    .contratos-desktop table {
        min-width: 1100px;
        margin-bottom: 0;
    }

    .contratos-desktop th,
    .contratos-desktop td {
        white-space: nowrap;
    }

    .contratos-mobile {
        display: none;
    }

    .contratos-rodape {
        display: block;
        margin-top: 16px;
        font-size: .8rem;
        color: #8995a5;
    }

    @media (max-width: 767px) {
        .contratos-page {
            padding-left: 2px;
            padding-right: 2px;
        }

        .contratos-page .contratos-header {
            align-items: stretch;
        }

        .contratos-page .contratos-header h2 {
            font-size: 1.3rem;
        }

        .contratos-page .contratos-header .btn {
            width: 100%;
            min-height: 44px;
        }

        .contratos-desktop {
            display: none;
        }

        .contratos-mobile {
            display: block;
        }

        .contrato-card {
            padding: 14px;
        }
    }
</style>

<div class="container contratos-page">

    <div class="contratos-header">
        <h2>Lista de Contratos</h2>

        <a href="{{ route('contratos.create') }}" class="btn btn-primary">
            ➕ Novo Contrato
        </a>
    </div>

    {{-- VERSÃO DESKTOP --}}
    <div class="contratos-desktop">
        <table class="table table-dark table-bordered text-white align-middle">
            <thead class="text-center">
                <tr>
                    <th>Cliente</th>
                    <th>Data Inicial</th>
                    <th>Valor</th>
                    <th>Parcelas</th>
                    <th>Intervalo</th>
                    <th>Juros</th>
                    <th>Total a Receber</th>
                    <th>Lucro</th>
                    <th>Valor da Parcela</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                @forelse($contratos as $contrato)
                    <tr>
                        <td>{{ $contrato->cliente->nome ?? 'Não informado' }}</td>

                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }}
                        </td>

                        <td class="text-end">
                            R$ {{ number_format($contrato->valor, 2, ',', '.') }}
                        </td>

                        <td class="text-center">{{ $contrato->qtd_parcelas }}</td>

                        <td class="text-center">{{ $contrato->intervalo_dias }} dias</td>

                        <td class="text-center text-info">
                            {{ number_format($contrato->juros, 2, ',', '.') }}%
                        </td>

                        <td class="text-end text-success">
                            R$ {{ number_format($contrato->total_receber, 2, ',', '.') }}
                        </td>

                        <td class="text-end text-warning">
                            R$ {{ number_format($contrato->lucro, 2, ',', '.') }}
                        </td>

                        <td class="text-end fw-bold">
                            R$ {{ number_format($contrato->valor_parcela, 2, ',', '.') }}
                        </td>

                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('contratos.show', $contrato->id) }}"
                                   class="btn btn-sm btn-info">Detalhes</a>

                                <a href="{{ route('contratos.edit', $contrato->id) }}"
                                   class="btn btn-sm btn-warning">Editar</a>

                                <form action="{{ route('contratos.destroy', $contrato->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Deseja excluir este contrato?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-warning">
                            Nenhum contrato cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- VERSÃO MOBILE --}}
    <div class="contratos-mobile">
        @forelse($contratos as $contrato)
            <div class="contrato-card">

                <div class="cliente">
                    {{ $contrato->cliente->nome ?? 'Não informado' }}
                </div>

                <div class="contrato-info">

                    <div class="item">
                        <span class="label">Data inicial</span>
                        <span class="valor">
                            {{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }}
                        </span>
                    </div>

                    <div class="item">
                        <span class="label">Valor emprestado</span>
                        <span class="valor">
                            R$ {{ number_format($contrato->valor, 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="item">
                        <span class="label">Parcelas</span>
                        <span class="valor">{{ $contrato->qtd_parcelas }}</span>
                    </div>

                    <div class="item">
                        <span class="label">Intervalo</span>
                        <span class="valor">{{ $contrato->intervalo_dias }} dias</span>
                    </div>

                    <div class="item">
                        <span class="label">Juros</span>
                        <span class="valor text-info">
                            {{ number_format($contrato->juros, 2, ',', '.') }}%
                        </span>
                    </div>

                    <div class="item">
                        <span class="label">Valor da parcela</span>
                        <span class="valor">
                            R$ {{ number_format($contrato->valor_parcela, 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="item">
                        <span class="label">Total a receber</span>
                        <span class="valor destaque">
                            R$ {{ number_format($contrato->total_receber, 2, ',', '.') }}
                        </span>
                    </div>

                    <div class="item">
                        <span class="label">Lucro</span>
                        <span class="valor lucro">
                            R$ {{ number_format($contrato->lucro, 2, ',', '.') }}
                        </span>
                    </div>

                </div>

                <div class="contrato-acoes">

                    <a href="{{ route('contratos.show', $contrato->id) }}"
                       class="btn btn-info btn-sm">
                        Detalhes
                    </a>

                    <a href="{{ route('contratos.edit', $contrato->id) }}"
                       class="btn btn-warning btn-sm">
                        Editar
                    </a>

                    <form action="{{ route('contratos.destroy', $contrato->id) }}"
                          method="POST"
                          style="flex: 1;"
                          onsubmit="return confirm('Deseja excluir este contrato?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            Excluir
                        </button>
                    </form>

                </div>
            </div>
        @empty
            <div class="contrato-card text-center text-warning">
                Nenhum contrato cadastrado.
            </div>
        @endforelse
    </div>

    <small class="contratos-rodape">
        📌 Cálculo das parcelas baseado no sistema PRICE (juros compostos).
    </small>

</div>
@endsection
