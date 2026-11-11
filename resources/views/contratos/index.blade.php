@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Lista de Contratos</h2>

    <a href="{{ route('contratos.create') }}" class="btn btn-primary mb-3">
        ➕ Novo Contrato
    </a>

    <div class="table-responsive">
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

                    <td class="text-center">
                        {{ $contrato->qtd_parcelas }}
                    </td>

                    <td class="text-center">
                        {{ $contrato->intervalo_dias }} dias
                    </td>

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
                            <a href="{{ route('contratos.show', $contrato->id) }}" class="btn btn-sm btn-info">
                                Detalhes
                            </a>

                            <a href="{{ route('contratos.edit', $contrato->id) }}" class="btn btn-sm btn-warning">
                                Editar
                            </a>

                            <form action="{{ route('contratos.destroy', $contrato->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Deseja excluir este contrato?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">
                                    Excluir
                                </button>
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

    <small class="text-muted">
        📌 Cálculo das parcelas baseado no sistema <strong>PRICE (juros compostos)</strong>.
    </small>
</div>
@endsection
