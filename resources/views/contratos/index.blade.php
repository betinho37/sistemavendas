
@extends('layouts.app')

@section('title', 'Contratos')

@section('content')

<style>
    .contratos-page {
        color: #e8edf5;
        width: 100%;
    }

    .contratos-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .contratos-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }

    /* TABELA DESKTOP */
    .contratos-desktop {
        width: 100%;
        overflow-x: auto;
        border-radius: 12px;
        -webkit-overflow-scrolling: touch;
    }

    .contratos-desktop table {
        min-width: 1100px;
        margin-bottom: 0;
    }

    .contratos-desktop th,
    .contratos-desktop td {
        white-space: nowrap;
        vertical-align: middle;
    }

    .contratos-desktop .acoes {
        display: flex;
        gap: 6px;
    }

    /* MOBILE */
    .contratos-mobile {
        display: none;
    }

    .contrato-card {
        background: #151b26;
        border: 1px solid #293344;
        border-radius: 14px;
        padding: 15px;
        margin-bottom: 13px;
        width: 100%;
        box-shadow: 0 3px 10px rgba(0,0,0,.12);
    }

    .contrato-card .cliente {
        font-size: 1rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid #293344;
        overflow-wrap: anywhere;
    }

    .contrato-info {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 10px;
    }

    .contrato-info .item {
        min-width: 0;
    }

    .contrato-info .label {
        display: block;
        font-size: .72rem;
        color: #8d9aab;
        margin-bottom: 4px;
    }

    .contrato-info .valor {
        display: block;
        font-size: .9rem;
        font-weight: 600;
        color: #e8edf5;
        overflow-wrap: anywhere;
    }

    .contrato-info .destaque {
        color: #35d49a;
    }

    .contrato-info .lucro {
        color: #ffc857;
    }

    .contrato-acoes {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        margin-top: 16px;
        padding-top: 13px;
        border-top: 1px solid #293344;
    }

    .contrato-acoes a,
    .contrato-acoes button {
        width: 100%;
        min-height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .82rem;
        padding: 6px 4px;
        white-space: nowrap;
    }

    .contrato-acoes form {
        min-width: 0;
    }

    .contratos-vazio {
        background: #151b26;
        border: 1px solid #293344;
        border-radius: 12px;
        padding: 25px 15px;
        text-align: center;
        color: #ffc857;
    }

    .contratos-rodape {
        display: block;
        margin-top: 16px;
        font-size: .8rem;
        color: #8995a5;
    }

    /* TABLETS E CELULARES */
    @media (max-width: 767px) {
        .contratos-page {
            padding-left: 0;
            padding-right: 0;
        }

        .contratos-header {
            align-items: stretch;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .contratos-header h2 {
            font-size: 1.25rem;
        }

        .contratos-header .btn {
            width: 100%;
            min-height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .contratos-desktop {
            display: none;
        }

        .contratos-mobile {
            display: block;
        }
    }

    /* CELULARES PEQUENOS */
    @media (max-width: 380px) {
        .contrato-card {
            padding: 12px;
        }

        .contrato-info {
            gap: 12px 7px;
        }

        .contrato-info .valor {
            font-size: .83rem;
        }

        .contrato-acoes {
            gap: 5px;
        }

        .contrato-acoes a,
        .contrato-acoes button {
            font-size: .75rem;
        }
    }
</style>

<div class="container-fluid contratos-page">

    <div class="contratos-header">
        <h2>Lista de Contratos</h2>

        <a href="{{ route('contratos.create') }}" class="btn btn-primary">
            ➕ Novo Contrato
        </a>
    </div>

    {{-- DESKTOP --}}
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
                            <div class="acoes">
                                <a href="{{ route('contratos.show', $contrato->id) }}"
                                   class="btn btn-sm btn-info">Detalhes</a>

                                <a href="{{ route('contratos.edit', $contrato->id) }}"
                                   class="btn btn-sm btn-warning">Editar</a>

                                <form action="{{ route('contratos.destroy', $contrato->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Deseja excluir este contrato?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        Excluir
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-warning py-4">
                            Nenhum contrato cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MOBILE --}}
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
                          onsubmit="return confirm('Deseja excluir este contrato?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger btn-sm">
                            Excluir
                        </button>
                    </form>

                </div>
            </div>
        @empty
            <div class="contratos-vazio">
                Nenhum contrato cadastrado.
            </div>
        @endforelse

    </div>

    <small class="contratos-rodape">
        📌 Cálculo das parcelas baseado no sistema PRICE (juros compostos).
    </small>

</div>
@endsection
