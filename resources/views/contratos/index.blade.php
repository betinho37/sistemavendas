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
        margin-bottom: 18px;
    }

    .contratos-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .contratos-filtros {
        background: #151b26;
        border: 1px solid #293344;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 18px;
    }

    .contratos-filtros label {
        color: #aeb9c9;
        font-size: .8rem;
        margin-bottom: 5px;
    }

    .contratos-filtros .form-control,
    .contratos-filtros .form-select {
        background-color: #202a39;
        border: 1px solid #39465a;
        color: #fff;
        min-height: 42px;
    }

    .contratos-filtros .form-control:focus,
    .contratos-filtros .form-select:focus {
        background-color: #202a39;
        color: #fff;
        border-color: #3995ff;
        box-shadow: none;
    }

    .contratos-filtros option {
        background: #202a39;
        color: #fff;
    }

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

    .contratos-paginacao {
        margin-top: 18px;
    }

    .contratos-paginacao nav {
        display: flex;
        justify-content: center;
    }

    .contratos-paginacao .pagination {
        flex-wrap: wrap;
        gap: 4px;
    }

    .contratos-paginacao .page-link {
        background: #202a39;
        border-color: #39465a;
        color: #e8edf5;
    }

    .contratos-paginacao .active .page-link {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    .contratos-resultado {
        color: #8995a5;
        font-size: .82rem;
        margin-top: 10px;
    }

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

        .contratos-filtros .btn {
            width: 100%;
            min-height: 42px;
        }
    }

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

    {{-- PESQUISA E FILTROS --}}
    <div class="contratos-filtros">
        <form method="GET" action="{{ route('contratos.index') }}">

            <div class="row g-3">

                <div class="col-12 col-md-4">
                    <label for="busca">Pesquisar contrato ou cliente</label>
                    <input
                        type="text"
                        name="busca"
                        id="busca"
                        class="form-control"
                        value="{{ request('busca') }}"
                        placeholder="Nome do cliente ou número do contrato"
                    >
                </div>

                <div class="col-6 col-md-3">
                    <label for="ordenar">Ordenar por</label>
                    <select name="ordenar" id="ordenar" class="form-select">
                        <option value="data" @selected(request('ordenar', 'data') == 'data')>
                            Data do contrato
                        </option>
                        <option value="cliente" @selected(request('ordenar') == 'cliente')>
                            Nome do cliente
                        </option>
                        <option value="valor" @selected(request('ordenar') == 'valor')>
                            Valor emprestado
                        </option>
                        <option value="total" @selected(request('ordenar') == 'total')>
                            Total a receber
                        </option>
                        <option value="lucro" @selected(request('ordenar') == 'lucro')>
                            Lucro
                        </option>
                        <option value="parcelas" @selected(request('ordenar') == 'parcelas')>
                            Quantidade de parcelas
                        </option>
                        <option value="vencimento" @selected(request('ordenar') == 'vencimento')>
                            Próximo vencimento
                        </option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label for="direcao">Ordem</label>
                    <select name="direcao" id="direcao" class="form-select">
                        <option value="desc" @selected(request('direcao', 'desc') == 'desc')>
                            Decrescente
                        </option>
                        <option value="asc" @selected(request('direcao') == 'asc')>
                            Crescente
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label for="situacao">Situação</label>
                    <select name="situacao" id="situacao" class="form-select">
                        <option value="">Todas as situações</option>
                        <option value="em_andamento" @selected(request('situacao') == 'em_andamento')>
                            Em andamento
                        </option>
                        <option value="quitado" @selected(request('situacao') == 'quitado')>
                            Quitados
                        </option>
                        <option value="atrasado" @selected(request('situacao') == 'atrasado')>
                            Com parcelas atrasadas
                        </option>
                        <option value="a_vencer" @selected(request('situacao') == 'a_vencer')>
                            Com parcelas a vencer
                        </option>
                    </select>
                </div>

<div class="col-12">
    <a href="{{ route('contratos.index') }}" class="btn btn-outline-light">
        Limpar filtros
    </a>
</div>

            </div>
        </form>

        <div class="contratos-resultado">
            {{ $contratos->total() }} contrato(s) encontrado(s).
        </div>
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
                            {{ $contrato->data_inicio ? \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') : '-' }}
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
                            Nenhum contrato encontrado com esses filtros.
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
                    <small class="d-block text-secondary mt-1">
                        Contrato #{{ $contrato->id }}
                    </small>
                </div>

                <div class="contrato-info">

                    <div class="item">
                        <span class="label">Data inicial</span>
                        <span class="valor">
                            {{ $contrato->data_inicio ? \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') : '-' }}
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
                Nenhum contrato encontrado com esses filtros.
            </div>
        @endforelse

    </div>

    {{-- PAGINAÇÃO --}}
    @if($contratos->hasPages())
        <div class="contratos-paginacao">
            {{ $contratos->links() }}
        </div>
    @endif

    <small class="contratos-rodape">
        📌 Cálculo das parcelas baseado em juros simples sobre o capital original.
    </small>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.contratos-filtros form');

    if (!form) return;

    const busca = form.querySelector('[name="busca"]');
    const filtros = form.querySelectorAll(
        'select[name="ordenar"], select[name="direcao"], select[name="situacao"]'
    );

    let temporizador;

    function enviarFormulario() {
        form.requestSubmit();
    }

    filtros.forEach(function (filtro) {
        filtro.addEventListener('change', enviarFormulario);
    });

    busca.addEventListener('input', function () {
        clearTimeout(temporizador);

        temporizador = setTimeout(function () {
            enviarFormulario();
        }, 400);
    });
});
</script>
@endsection
