
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card bg-dark text-white shadow">
        <div class="card-header border-secondary">
            <h4 class="mb-0">
                Detalhes do Contrato – {{ $contrato->cliente->nome ?? 'Não informado' }}
            </h4>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $erro)
                        <div>{{ $erro }}</div>
                    @endforeach
                </div>
            @endif

            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Data de Início:</strong>
                        {{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }}
                    </p>
                    <p><strong>Cliente:</strong>
                        {{ $contrato->cliente->nome ?? 'Não informado' }}
                    </p>
                    <p><strong>Valor do Contrato:</strong>
                        R$ {{ number_format($contrato->valor, 2, ',', '.') }}
                    </p>
                    <p><strong>Quantidade de Parcelas:</strong>
                        {{ $contrato->qtd_parcelas }}
                    </p>
                </div>

                <div class="col-md-6">
                    <p><strong>Intervalo:</strong> {{ $contrato->intervalo_dias }} dias</p>
                    <p><strong>Juros:</strong> {{ number_format($contrato->juros, 2, ',', '.') }}%</p>
                    <p><strong>Total a Receber:</strong>
                        R$ {{ number_format($contrato->total_receber ?? 0, 2, ',', '.') }}
                    </p>
                    <p><strong>Lucro:</strong>
                        R$ {{ number_format($contrato->lucro ?? 0, 2, ',', '.') }}
                    </p>
                    <p><strong>Valor da Parcela:</strong>
                        R$ {{ number_format($contrato->valor_parcela ?? 0, 2, ',', '.') }}
                    </p>
                    <p><strong>Observações:</strong> {{ $contrato->observacao ?? 'Nenhuma' }}</p>
                </div>
            </div>

            <hr class="border-secondary">

            <h5 class="mb-3">Parcelas e recebimentos</h5>

            <div class="table-responsive">
                <table class="table table-dark table-bordered align-middle">
                    <thead class="table-secondary text-dark">
                        <tr>
                            <th>#</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th>Recebido</th>
                            <th>Saldo</th>
                            <th>Status</th>
                            <th>Registrar pagamento</th>
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
                                <td>{{ $parcela->data_vencimento->format('d/m/Y') }}</td>
                                <td>R$ {{ number_format($parcela->valor, 2, ',', '.') }}</td>
                                <td class="text-success">
                                    R$ {{ number_format($recebido, 2, ',', '.') }}
                                </td>
                                <td class="{{ $saldo > 0 ? 'text-warning' : 'text-success' }}">
                                    R$ {{ number_format($saldo, 2, ',', '.') }}
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
                                <td style="min-width: 230px;">
                                    @if($saldo > 0)
                                        <form action="{{ route('parcelas.pagamentos.store', $parcela->id) }}"
                                              method="POST">
                                            @csrf

                                            <input type="number"
                                                   name="valor"
                                                   class="form-control form-control-sm mb-2"
                                                   min="0.01"
                                                   max="{{ number_format($saldo, 2, '.', '') }}"
                                                   step="0.01"
                                                   placeholder="Valor recebido"
                                                   required>

                                            <input type="date"
                                                   name="data_pagamento"
                                                   class="form-control form-control-sm mb-2"
                                                   value="{{ now()->format('Y-m-d') }}"
                                                   required>

                                            <input type="text"
                                                   name="observacao"
                                                   class="form-control form-control-sm mb-2"
                                                   placeholder="Observação (opcional)">

                                            <button type="submit" class="btn btn-sm btn-success w-100">
                                                Registrar recebimento
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-success">Totalmente recebida</span>
                                    @endif
                                </td>
                            </tr>

                            @if($parcela->pagamentos->isNotEmpty())
                                <tr>
                                    <td colspan="7">
                                        <strong>Histórico de recebimentos:</strong>
                                        <ul class="mb-0">
                                            @foreach($parcela->pagamentos as $pagamento)
                                                <li>
                                                    {{ $pagamento->data_pagamento->format('d/m/Y') }}
                                                    — R$ {{ number_format($pagamento->valor, 2, ',', '.') }}
                                                    — {{ $pagamento->user->name ?? 'Usuário não identificado' }}
                                                    @if($pagamento->observacao)
                                                        — {{ $pagamento->observacao }}
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    Nenhuma parcela cadastrada.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex flex-wrap gap-2">
                <a href="{{ route('contratos.index') }}" class="btn btn-secondary">Voltar</a>
                <a href="{{ route('contratos.edit', $contrato->id) }}" class="btn btn-warning">Editar</a>
                <a href="{{ route('contratos.gerarPdf', $contrato->id) }}"
                   class="btn btn-danger" target="_blank">Gerar PDF</a>

                <form action="{{ route('contratos.destroy', $contrato->id) }}"
                      method="POST"
                      onsubmit="return confirm('Tem certeza que deseja excluir este contrato?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
