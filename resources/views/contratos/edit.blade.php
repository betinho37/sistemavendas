
@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center py-4">
    <div class="card shadow-lg p-4" style="width: 500px; max-width: 100%; background-color: #1e1e1e; color: #fff; border-radius: 10px;">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Editar Contrato #{{ $contrato->id }}</h4>
            <button type="button" class="btn btn-danger" onclick="window.history.back()">✖</button>
        </div>

        <form action="{{ route('contratos.update', $contrato->id) }}" method="POST" enctype="multipart/form-data" class="mb-2"> id="formContrato">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="data_inicio" class="form-label">Data de Início:</label>
                <input type="date" id="data_inicio" name="data_inicio"
                       class="form-control bg-dark text-white"
                       value="{{ old('data_inicio', optional($contrato->data_inicio)->format('Y-m-d')) }}"
                       required>
            </div>

            <div class="mb-3">
                <label for="cliente_id" class="form-label">Nome do Cliente:</label>
                <select name="cliente_id" id="cliente_id" class="form-control bg-dark text-white" required>
                    <option value="" disabled>Selecione um cliente</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}"
                            {{ old('cliente_id', $contrato->cliente_id) == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="valor" class="form-label">Valor do Contrato:</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark text-white">R$</span>
                    <input type="number" id="valor" name="valor" step="0.01" min="0.01"
                           class="form-control bg-dark text-white"
                           value="{{ old('valor', $contrato->valor) }}" required>
                </div>
            </div>

            <div class="d-flex gap-3">
                <div class="mb-3 flex-grow-1">
                    <label for="qtd_parcelas" class="form-label">N.º de Parcelas:</label>
                    <input type="number" id="qtd_parcelas" name="qtd_parcelas"
                           min="1" step="1"
                           class="form-control bg-dark text-white"
                           value="{{ old('qtd_parcelas', $contrato->qtd_parcelas) }}" required>
                </div>

                <div class="mb-3 flex-grow-1">
                    <label for="intervalo_dias" class="form-label">Intervalo (dias):</label>
                    <input type="number" id="intervalo_dias" name="intervalo_dias"
                           min="1" step="1"
                           class="form-control bg-dark text-white"
                           value="{{ old('intervalo_dias', $contrato->intervalo_dias) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Forma de cálculo:</label>
                <select id="modo_calculo" class="form-select bg-dark text-white">
                    <option value="taxa">Informar juros e calcular parcela</option>
                    <option value="parcela">Informar parcela e calcular juros</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="juros" class="form-label">Juros mensais (%):</label>
                <input type="number" id="juros" name="juros"
                       class="form-control bg-dark text-white"
                       step="0.000001" min="0"
                       value="{{ old('juros', $contrato->juros) }}" required>
                <small id="ajuda_juros" class="text-secondary">
                    Informe a taxa de juros para calcular o valor da parcela.
                </small>
            </div>

            <div class="mb-3" id="campo_parcela_desejada" style="display: none;">
                <label for="parcela_desejada" class="form-label">Valor desejado da parcela:</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark text-white">R$</span>
                    <input type="number" id="parcela_desejada"
                           class="form-control bg-dark text-white"
                           step="0.01" min="0.01"
                           placeholder="Ex.: 420,00">
                </div>
                <small class="text-secondary">
                    A taxa será calculada automaticamente pela fórmula Price.
                </small>
            </div>

            <div class="mb-3">
                <label for="lucro" class="form-label">Lucro do Contrato:</label>
                <input type="text" id="lucro"
                       class="form-control bg-dark text-white" readonly>
            </div>

            <div class="mb-3">
                <label for="observacao" class="form-label">Observações:</label>
                <textarea id="observacao" name="observacao"
                          class="form-control bg-dark text-white">{{ old('observacao', $contrato->observacao) }}</textarea>
            </div>

            <div class="form-check mb-1">
                <input type="checkbox" id="vencimento_sabado"
                       name="vencimento_sabado" value="1"
                       class="form-check-input"
                       {{ old('vencimento_sabado', $contrato->vencimento_sabado) ? 'checked' : '' }}>
                <label for="vencimento_sabado" class="form-check-label">
                    Vencimento aos sábados
                </label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" id="vencimento_domingo"
                       name="vencimento_domingo" value="1"
                       class="form-check-input"
                       {{ old('vencimento_domingo', $contrato->vencimento_domingo) ? 'checked' : '' }}>
                <label for="vencimento_domingo" class="form-check-label">
                    Vencimento aos domingos
                </label>
            </div>

            <hr class="border-secondary">

            <div class="mt-3">
                <p><strong>Total a receber:</strong> <span id="total_receber">R$ 0,00</span></p>
                <p><strong>Lucro estimado:</strong> <span id="lucro_texto">R$ 0,00</span></p>
                <p><strong>Valor da parcela:</strong> <span id="valor_parcela">R$ 0,00</span></p>
            </div>

            <button type="submit" class="btn btn-success w-100 mt-3">
                Salvar Alterações
            </button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const valorInput = document.getElementById('valor');
    const jurosInput = document.getElementById('juros');
    const parcelasInput = document.getElementById('qtd_parcelas');
    const modoInput = document.getElementById('modo_calculo');
    const parcelaInput = document.getElementById('parcela_desejada');
    const campoParcela = document.getElementById('campo_parcela_desejada');
    const ajudaJuros = document.getElementById('ajuda_juros');
    const form = document.getElementById('formContrato');

    function moeda(valor) {
        return 'R$ ' + valor.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function atualizarTela(total, lucro, parcela) {
        document.getElementById('total_receber').textContent = moeda(total);
        document.getElementById('lucro_texto').textContent = moeda(lucro);
        document.getElementById('valor_parcela').textContent = moeda(parcela);
        document.getElementById('lucro').value = moeda(lucro);
    }

    function calcularParcela(principal, taxa, quantidade) {
        if (taxa === 0) {
            return principal / quantidade;
        }

        const fator = Math.pow(1 + taxa, quantidade);
        return principal * ((taxa * fator) / (fator - 1));
    }

    function encontrarTaxa(principal, parcela, quantidade) {
        const parcelaMinima = principal / quantidade;

        if (parcela < parcelaMinima) {
            return null;
        }

        if (Math.abs(parcela - parcelaMinima) < 0.000001) {
            return 0;
        }

        let inferior = 0;
        let superior = 1;

        while (
            calcularParcela(principal, superior, quantidade) < parcela &&
            superior < 100
        ) {
            superior *= 2;
        }

        if (superior >= 100) {
            return null;
        }

        for (let i = 0; i < 120; i++) {
            const meio = (inferior + superior) / 2;
            const calculada = calcularParcela(principal, meio, quantidade);

            if (calculada > parcela) {
                superior = meio;
            } else {
                inferior = meio;
            }
        }

        return (inferior + superior) / 2;
    }

    function calcularValores() {
        const principal = Math.round((parseFloat(valorInput.value) || 0) * 100) / 100;
        const quantidade = parseInt(parcelasInput.value, 10);
        let taxa;

        if (principal <= 0 || !Number.isInteger(quantidade) || quantidade <= 0) {
            atualizarTela(0, 0, 0);
            return false;
        }

        if (modoInput.value === 'parcela') {
            const parcelaDesejada = Math.round((parseFloat(parcelaInput.value) || 0) * 100) / 100;

            if (parcelaDesejada <= 0) {
                atualizarTela(0, 0, 0);
                return false;
            }

            taxa = encontrarTaxa(principal, parcelaDesejada, quantidade);

            if (taxa === null) {
                atualizarTela(0, 0, 0);
                ajudaJuros.textContent = 'A parcela precisa ser igual ou maior que o valor do contrato dividido pelo número de parcelas.';
                return false;
            }

            jurosInput.value = (taxa * 100).toFixed(6);
            ajudaJuros.textContent = 'Taxa calculada automaticamente pela fórmula Price.';
        } else {
            taxa = (parseFloat(jurosInput.value) || 0) / 100;

            if (taxa < 0) {
                atualizarTela(0, 0, 0);
                return false;
            }

            ajudaJuros.textContent = 'Informe a taxa de juros para calcular o valor da parcela.';
        }

        const valorParcela = calcularParcela(principal, taxa, quantidade);
        const parcelaCentavos = Math.round(valorParcela * 100);
        const totalCentavos = parcelaCentavos * quantidade;
        const total = totalCentavos / 100;
        const lucro = total - principal;

        atualizarTela(total, lucro, parcelaCentavos / 100);

        return true;
    }

    function alterarModo() {
        const modoParcela = modoInput.value === 'parcela';

        campoParcela.style.display = modoParcela ? 'block' : 'none';
        parcelaInput.required = modoParcela;
        parcelaInput.disabled = !modoParcela;

        jurosInput.readOnly = modoParcela;

        if (modoParcela) {
            ajudaJuros.textContent = 'A taxa será calculada automaticamente.';
        }

        calcularValores();
    }

    ['input', 'change'].forEach(evento => {
        valorInput.addEventListener(evento, calcularValores);
        parcelasInput.addEventListener(evento, calcularValores);
        jurosInput.addEventListener(evento, calcularValores);
        parcelaInput.addEventListener(evento, calcularValores);
    });

    modoInput.addEventListener('change', alterarModo);

    form.addEventListener('submit', function (event) {
        if (!calcularValores()) {
            event.preventDefault();
            alert('Confira o valor do contrato, a quantidade de parcelas e os valores informados.');
        }
    });

    alterarModo();
});
</script>
@endsection
