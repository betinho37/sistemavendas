
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
            <div class="row mb-4">
                {{-- COLUNA ESQUERDA --}}
                <div class="col-md-6">
                    <p>
                        <strong>Data de Início:</strong>
                        {{ \Carbon\Carbon::parse($contrato->data_inicio)->format('d/m/Y') }}
                    </p>

                    <p>
                        <strong>Cliente:</strong>
                        {{ $contrato->cliente->nome ?? 'Não informado' }}
                    </p>

                    <p>
                        <strong>Valor do Contrato:</strong>
                        R$ {{ number_format($contrato->valor, 2, ',', '.') }}
                    </p>

                    <p>
                        <strong>Quantidade de Parcelas:</strong>
                        {{ $contrato->qtd_parcelas }}
                    </p>
                </div>

                {{-- COLUNA DIREITA --}}
                <div class="col-md-6">
                    <p>
                        <strong>Intervalo entre Parcelas:</strong>
                        {{ $contrato->intervalo_dias }} dias
                    </p>

                    <p>
                        <strong>Juros Aplicados:</strong>
                        {{ number_format($contrato->juros, 2, ',', '.') }}%
                    </p>

                    <p>
                        <strong>Total a Receber:</strong>
                        R$ {{ number_format($contrato->total_receber ?? 0, 2, ',', '.') }}
                    </p>

                    <p>
                        <strong>Lucro do Contrato:</strong>
                        R$ {{ number_format($contrato->lucro ?? 0, 2, ',', '.') }}
                    </p>

                    <p>
                        <strong>Valor da Parcela:</strong>
                        R$ {{ number_format($contrato->valor_parcela ?? 0, 2, ',', '.') }}
                    </p>

                    <p>
                        <strong>Observações:</strong>
                        {{ $contrato->observacao ?? 'Nenhuma' }}
                    </p>
                </div>
            </div>

            <hr class="border-secondary">

            <h5 class="mb-3">Parcelas</h5>

            <div class="table-responsive">
                <table class="table table-dark table-bordered align-middle">
                    <thead class="table-secondary text-dark">
                        <tr>
                            <th>#</th>
                            <th>Vencimento</th>
                            <th>Valor</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($contrato->parcelas as $parcela)
                            <tr>
                                <td>{{ $parcela->numero }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y') }}
                                </td>

                                <td>
                                    R$ {{ number_format($parcela->valor, 2, ',', '.') }}
                                </td>

                                <td>
                                    <form
                                        action="{{ route('parcelas.updateStatus', $parcela->id) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PUT')

                                        <select
                                            name="status"
                                            onchange="this.form.submit()"
                                            class="form-select text-white {{ $parcela->status ? 'bg-success' : 'bg-danger' }}"
                                        >
                                            <option value="1" {{ $parcela->status ? 'selected' : '' }}>
                                                Paga
                                            </option>

                                            <option value="0" {{ !$parcela->status ? 'selected' : '' }}>
                                                Não Paga
                                            </option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    Nenhuma parcela cadastrada para este contrato.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex flex-wrap gap-2">
                <a href="{{ route('contratos.index') }}" class="btn btn-secondary">
                    Voltar
                </a>

                <a href="{{ route('contratos.edit', $contrato->id) }}" class="btn btn-warning">
                    Editar
                </a>

                <a
                    href="{{ route('contratos.gerarPdf', $contrato->id) }}"
                    class="btn btn-danger"
                    target="_blank"
                >
                    Gerar PDF
                </a>

                <form
                    action="{{ route('contratos.destroy', $contrato->id) }}"
                    method="POST"
                    onsubmit="return confirm('Tem certeza que deseja excluir este contrato?')"
                >
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-outline-danger">
                        Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
