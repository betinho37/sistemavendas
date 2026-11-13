@extends('layouts.app')

@section('title', 'Fluxo de Caixa')

@section('content')
@php
    $moeda = fn ($valor) => 'R$ ' . number_format((float) $valor, 2, ',', '.');
@endphp

<div class="container-fluid py-4 fluxo-caixa">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-white mb-1">Fluxo de Caixa</h3>
            <p class="text-secondary mb-0">Controle de entradas, saídas e capital disponível.</p>
        </div>

        <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#formMovimentacao">
            <i class="bi bi-plus-lg me-1"></i> Nova movimentação
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Verifique os dados informados:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORMULÁRIO --}}
    <div class="collapse mb-4" id="formMovimentacao">
        <div class="fc-panel">
            <h5 class="text-white mb-3">Registrar movimentação</h5>

            <form action="{{ route('fluxo-caixa.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select" required>
                            <option value="entrada" @selected(old('tipo') === 'entrada')>Entrada</option>
                            <option value="saida" @selected(old('tipo') === 'saida')>Saída</option>
                        </select>
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label">Categoria</label>
                        <input
                            type="text"
                            name="categoria"
                            class="form-control"
                            maxlength="100"
                            placeholder="Ex.: Capital inicial, despesa..."
                            value="{{ old('categoria') }}"
                            required
                        >
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label">Valor (R$)</label>
                        <input
                            type="number"
                            name="valor"
                            class="form-control"
                            min="0.01"
                            step="0.01"
                            placeholder="0,00"
                            value="{{ old('valor') }}"
                            required
                        >
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label">Data</label>
                        <input
                            type="date"
                            name="data"
                            class="form-control"
                            value="{{ old('data', now()->format('Y-m-d')) }}"
                            required
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label">Descrição</label>
                        <textarea
                            name="descricao"
                            class="form-control"
                            rows="2"
                            maxlength="2000"
                            placeholder="Detalhes da movimentação (opcional)"
                        >{{ old('descricao') }}</textarea>
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> Salvar movimentação
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- FILTRO --}}
    <div class="fc-panel mb-4">
        <form method="GET" action="{{ route('fluxo-caixa.index') }}" class="row g-3 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label">Data inicial</label>
                <input type="date" name="data_inicio" class="form-control"
                    value="{{ $inicio->format('Y-m-d') }}">
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label">Data final</label>
                <input type="date" name="data_fim" class="form-control"
                    value="{{ $fim->format('Y-m-d') }}">
            </div>

            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-filter me-1"></i> Filtrar
                </button>

                <a href="{{ route('fluxo-caixa.index') }}" class="btn btn-outline-light">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    {{-- INDICADORES --}}
    <div class="row g-3 mb-4">

        <div class="col-12 col-md-6 col-xl-4">
            <div class="fc-card">
                <div class="fc-icon blue"><i class="bi bi-wallet2"></i></div>
                <span>Saldo atual do caixa</span>
                <h4 class="{{ $saldoCaixa >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $moeda($saldoCaixa) }}
                </h4>
                <small>Considera todo o histórico registrado</small>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="fc-card">
                <div class="fc-icon green"><i class="bi bi-arrow-down-circle"></i></div>
                <span>Entradas no período</span>
                <h4 class="text-success">{{ $moeda($entradasPeriodo) }}</h4>
                <small>Recebimentos e entradas manuais</small>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-4">
            <div class="fc-card">
                <div class="fc-icon red"><i class="bi bi-arrow-up-circle"></i></div>
                <span>Saídas no período</span>
                <h4 class="text-danger">{{ $moeda($saidasPeriodo) }}</h4>
                <small>Empréstimos concedidos e saídas manuais</small>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="fc-card fc-card-small">
                <span>Total emprestado</span>
                <h5>{{ $moeda($totalEmprestado) }}</h5>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="fc-card fc-card-small">
                <span>Total recebido</span>
                <h5>{{ $moeda($totalRecebido) }}</h5>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="fc-card fc-card-small">
                <span>Capital recuperado</span>
                <h5>{{ $moeda($capitalRecuperado) }}</h5>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="fc-card fc-card-small">
                <span>Lucro recebido</span>
                <h5 class="text-success">{{ $moeda($lucroRecebido) }}</h5>
            </div>
        </div>
    </div>

    {{-- LUCRO PREVISTO --}}
    <div class="fc-panel mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="text-secondary">Lucro previsto a receber</span>
            <h4 class="text-info fw-bold mb-0">{{ $moeda($lucroPrevisto) }}</h4>
        </div>
        <small class="text-secondary">
            Juros pendentes das parcelas. Não compõem o saldo disponível do caixa.
        </small>
    </div>

    {{-- HISTÓRICO --}}
    <div class="fc-panel">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h5 class="text-white mb-1">Histórico de movimentações</h5>
                <small class="text-secondary">
                    {{ $inicio->format('d/m/Y') }} até {{ $fim->format('d/m/Y') }}
                </small>
            </div>

            <span class="badge bg-secondary">
                {{ count($historico) }} registros
            </span>
        </div>

        @if(count($historico))
            {{-- DESKTOP --}}
            <div class="table-responsive d-none d-md-block">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Categoria</th>
                            <th>Descrição</th>
                            <th class="text-end">Valor</th>
                            <th class="text-end">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historico as $item)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($item['data'])->format('d/m/Y') }}</td>
                                <td>
                                    @if($item['tipo'] === 'entrada')
                                        <span class="badge bg-success">Entrada</span>
                                    @else
                                        <span class="badge bg-danger">Saída</span>
                                    @endif
                                </td>
                                <td>{{ $item['categoria'] }}</td>
                                <td>{{ $item['descricao'] ?? '—' }}</td>
                                <td class="text-end fw-bold {{ $item['tipo'] === 'entrada' ? 'text-success' : 'text-danger' }}">
                                    {{ $item['tipo'] === 'entrada' ? '+' : '-' }} {{ $moeda($item['valor']) }}
                                </td>
                                <td class="text-end">
                                    @if(!empty($item['manual']))
                                        <form action="{{ route('fluxo-caixa.destroy', $item['id']) }}"
                                            method="POST"
                                            onsubmit="return confirm('Excluir esta movimentação?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Excluir">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-secondary">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- MOBILE --}}
            <div class="d-md-none">
                @foreach($historico as $item)
                    <div class="fc-history-item">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="min-w-0">
                                <strong class="text-white">{{ $item['categoria'] }}</strong>
                                <small class="d-block text-secondary mt-1">
                                    {{ \Carbon\Carbon::parse($item['data'])->format('d/m/Y') }}
                                </small>
                            </div>

                            <strong class="{{ $item['tipo'] === 'entrada' ? 'text-success' : 'text-danger' }} text-nowrap">
                                {{ $item['tipo'] === 'entrada' ? '+' : '-' }} {{ $moeda($item['valor']) }}
                            </strong>
                        </div>

                        @if(!empty($item['descricao']))
                            <p class="text-secondary small mt-2 mb-2">{{ $item['descricao'] }}</p>
                        @endif

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge {{ $item['tipo'] === 'entrada' ? 'bg-success' : 'bg-danger' }}">
                                {{ $item['tipo'] === 'entrada' ? 'Entrada' : 'Saída' }}
                            </span>

                            @if(!empty($item['manual']))
                                <form action="{{ route('fluxo-caixa.destroy', $item['id']) }}"
                                    method="POST"
                                    onsubmit="return confirm('Excluir esta movimentação?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash me-1"></i> Excluir
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="fc-empty">
                <i class="bi bi-cash-stack"></i>
                <p>Nenhuma movimentação encontrada neste período.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.fluxo-caixa {
    color: #e8edf5;
}

.fc-panel,
.fc-card {
    background: #171d29;
    border: 1px solid #293244;
    border-radius: 13px;
}

.fc-panel {
    padding: 20px;
}

.fc-card {
    height: 100%;
    min-height: 145px;
    padding: 18px;
}

.fc-card span {
    display: block;
    color: #9ba8bb;
    font-size: 13px;
    margin-bottom: 10px;
}

.fc-card h4,
.fc-card h5 {
    font-weight: 700;
    margin: 0 0 8px;
    overflow-wrap: anywhere;
}

.fc-card h4 {
    font-size: 23px;
}

.fc-card h5 {
    font-size: 19px;
    color: #f4f7fb;
}

.fc-card small {
    color: #77869b;
    font-size: 11px;
}

.fc-card-small {
    min-height: 105px;
}

.fc-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    margin-bottom: 13px;
    font-size: 18px;
}

.fc-icon.blue { background: #173c65; color: #60a5fa; }
.fc-icon.green { background: #174b39; color: #5ee0a5; }
.fc-icon.red { background: #57272f; color: #ff8795; }

.fluxo-caixa .form-label {
    color: #b8c4d5;
    font-size: 13px;
}

.fluxo-caixa .form-control,
.fluxo-caixa .form-select {
    background-color: #202939;
    border-color: #3a4659;
    color: #f4f7fb;
}

.fluxo-caixa .form-control:focus,
.fluxo-caixa .form-select:focus {
    border-color: #3986c9;
    box-shadow: 0 0 0 .2rem rgba(57, 134, 201, .15);
}

.fluxo-caixa .form-control::placeholder {
    color: #77869b;
}

.fluxo-caixa .table {
    --bs-table-bg: transparent;
    --bs-table-color: #dce5f0;
    --bs-table-border-color: #303b4e;
}

.fluxo-caixa .table thead th {
    color: #8e9db1;
    font-size: 12px;
    white-space: nowrap;
}

.fc-history-item {
    background: #202939;
    border: 1px solid #303b4e;
    border-radius: 10px;
    padding: 13px;
    margin-bottom: 10px;
}

.fc-empty {
    text-align: center;
    padding: 40px 10px;
    color: #8290a5;
}

.fc-empty i {
    display: block;
    font-size: 30px;
    margin-bottom: 10px;
}

.fc-empty p {
    margin: 0;
    font-size: 13px;
}

@media (max-width: 576px) {
    .fc-panel {
        padding: 15px;
    }

    .fc-card {
        padding: 14px;
        min-height: 130px;
    }

    .fc-card h4 {
        font-size: 19px;
    }

    .fc-card h5 {
        font-size: 17px;
    }
}
</style>
@endpush



