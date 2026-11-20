@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center py-4">
    <div class="card shadow-lg p-4" style="width: 500px; max-width: 100%; background-color: #1e1e1e; color: #fff; border-radius: 10px;">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Editar Contrato #{{ $contrato->id }}</h4>
            <button type="button" class="btn btn-danger" onclick="window.history.back()">✖</button>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Verifique os dados informados:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('contratos.update', $contrato->id) }}" method="POST" enctype="multipart/form-data" class="mb-2" id="formContrato">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="data_inicio" class="form-label">Data de Início:</label>
                <input type="date" id="data_inicio" name="data_inicio"
                       class="form-control bg-dark text-white"
                       value="{{ old('data_inicio', ($contrato->data_inicio ? \Illuminate\Support\Carbon::parse($contrato->data_inicio)->format('Y-m-d') : '')) }}"
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
                           min="1" max="120" step="1"
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
                    <option value="taxa">Informar juros e calcular total/parcela</option>
                    <option value="parcela">Informar parcela e calcular taxa mensal</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="juros" class="form-label">Juros mensais (%):</label>
                <input type="number" id="juros" name="juros"
                       class="form-control bg-dark text-white"
                       step="0.000001" min="0"
                       value="{{ old('juros', $contrato->juros) }}" required>
                <small id="ajuda_juros" class="text-secondary">
                    Juros simples calculados sobre o capital original.
                </small>
            </div>

            <div class="mb-3" id="campo_parcela_desejada" style="display: none;">
                <label for="parcela_desejada" class="form-label">Valor desejado da parcela:</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark text-white">R$</span>
                    <input type="number" id="parcela_desejada"
                           class="form-control bg-dark text-white"
                           step="0.01" min="0.01" placeholder="Ex.: 420,00">
                </div>
                <small class="text-secondary">
                    A taxa mensal será calculada com juros simples e intervalo informado.
                </small>
            </div>

            <div class="mb-3">
                <label for="lucro" class="form-label">Lucro do Contrato:</label>
                <input type="text" id="lucro" class="form-control bg-dark text-white" readonly>
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
                <label for="vencimento_sabado" class="form-check-label">Vencimento aos sábados</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" id="vencimento_domingo"
                       name="vencimento_domingo" value="1"
                       class="form-check-input"
                       {{ old('vencimento_domingo', $contrato->vencimento_domingo) ? 'checked' : '' }}>
                <label for="vencimento_domingo" class="form-check-label">Vencimento aos domingos</label>
            </div>

            <hr class="border-secondary">

            <div class="mt-3">
                <p><strong>Total a receber:</strong> <span id="total_receber">R$ 0,00</span></p>
                <p><strong>Lucro estimado:</strong> <span id="lucro_texto">R$ 0,00</span></p>
                <p><strong>Valor da parcela:</strong> <span id="valor_parcela">R$ 0,00</span></p>
                <p><strong>Taxa mensal calculada:</strong> <span id="taxa_calculada">0,0000%</span></p>
            </div>

            <div id="erro_calculo" class="alert alert-danger mt-3 d-none" role="alert"></div>

            <button type="submit" class="btn btn-success w-100 mt-3">Salvar Alterações</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const valorInput = document.getElementById('valor');
    const jurosInput = document.getElementById('juros');
    const parcelasInput = document.getElementById('qtd_parcelas');
    const intervaloInput = document.getElementById('intervalo_dias');
    const modoInput = document.getElementById('modo_calculo');
    const parcelaInput = document.getElementById('parcela_desejada');
    const campoParcela = document.getElementById('campo_parcela_desejada');
    const ajudaJuros = document.getElementById('ajuda_juros');
    const form = document.getElementById('formContrato');

    function moeda(centavos) {
        return (centavos / 100).toLocaleString('pt-BR', {
            style: 'currency', currency: 'BRL',
            minimumFractionDigits: 2, maximumFractionDigits: 2
        });
    }

    function limpar(mensagem = '') {
        document.getElementById('total_receber').textContent = 'R$ 0,00';
        document.getElementById('lucro_texto').textContent = 'R$ 0,00';
        document.getElementById('valor_parcela').textContent = 'R$ 0,00';
        document.getElementById('taxa_calculada').textContent = '0,0000%';
        document.getElementById('lucro').value = 'R$ 0,00';
        const erro = document.getElementById('erro_calculo');
        erro.textContent = mensagem;
        erro.classList.toggle('d-none', !mensagem);
    }

    function atualizarTela(totalCentavos, lucroCentavos, parcelaCentavos, taxa) {
        document.getElementById('total_receber').textContent = moeda(totalCentavos);
        document.getElementById('lucro_texto').textContent = moeda(lucroCentavos);
        document.getElementById('valor_parcela').textContent = moeda(parcelaCentavos);
        document.getElementById('lucro').value = moeda(lucroCentavos);
        document.getElementById('taxa_calculada').textContent =
            taxa.toLocaleString('pt-BR', {minimumFractionDigits: 4, maximumFractionDigits: 6}) + '%';
    }

    function calcularValores() {
        const principalReais = Number(valorInput.value);
        const quantidade = Number(parcelasInput.value);
        const intervalo = Number(intervaloInput.value);
        const meses = quantidade * intervalo / 30;

        if (!Number.isFinite(principalReais) || principalReais <= 0 ||
            !Number.isInteger(quantidade) || quantidade < 1 || quantidade > 120 ||
            !Number.isInteger(intervalo) || intervalo < 1) {
            limpar('Informe capital, quantidade de parcelas e intervalo válidos.');
            return false;
        }

        const principalCentavos = Math.round(principalReais * 100);
        let totalCentavos;
        let taxaMensal;

        if (modoInput.value === 'parcela') {
            const parcelaReais = Number(parcelaInput.value);

            if (!Number.isFinite(parcelaReais) || parcelaReais <= 0) {
                limpar('Informe o valor desejado da parcela.');
                return false;
            }

            const parcelaCentavos = Math.round(parcelaReais * 100);
            totalCentavos = parcelaCentavos * quantidade;

            if (totalCentavos < principalCentavos) {
                limpar('O total das parcelas não pode ser menor que o capital emprestado.');
                ajudaJuros.textContent = 'A parcela precisa cobrir o capital e os juros.';
                return false;
            }

            taxaMensal = (totalCentavos - principalCentavos) / principalCentavos / meses * 100;
            jurosInput.value = taxaMensal.toFixed(6);
        } else {
            taxaMensal = Number(jurosInput.value);

            if (!Number.isFinite(taxaMensal) || taxaMensal < 0 || taxaMensal > 1000) {
                limpar('Informe uma taxa mensal entre 0% e 1.000%.');
                return false;
            }

            const jurosCentavos = Math.round(principalCentavos * (taxaMensal / 100) * meses);
            totalCentavos = principalCentavos + jurosCentavos;
        }

        const lucroCentavos = totalCentavos - principalCentavos;
        const parcelaCentavos = Math.round(totalCentavos / quantidade);

        atualizarTela(totalCentavos, lucroCentavos, parcelaCentavos, taxaMensal);
        ajudaJuros.textContent = modoInput.value === 'parcela'
            ? 'Taxa mensal calculada automaticamente usando juros simples.'
            : 'Juros simples sobre o capital original; período calculado pelo intervalo.';

        document.getElementById('erro_calculo').classList.add('d-none');
        return true;
    }

    function alterarModo() {
        const modoParcela = modoInput.value === 'parcela';
        campoParcela.style.display = modoParcela ? 'block' : 'none';
        parcelaInput.required = modoParcela;
        parcelaInput.disabled = !modoParcela;
        jurosInput.readOnly = modoParcela;
        if (!modoParcela) parcelaInput.value = '';
        calcularValores();
    }

    ['input', 'change'].forEach(evento => {
        valorInput.addEventListener(evento, calcularValores);
        jurosInput.addEventListener(evento, calcularValores);
        parcelasInput.addEventListener(evento, calcularValores);
        intervaloInput.addEventListener(evento, calcularValores);
        parcelaInput.addEventListener(evento, calcularValores);
    });

    modoInput.addEventListener('change', alterarModo);

    form.addEventListener('submit', function (event) {
        if (!calcularValores()) {
            event.preventDefault();
            document.getElementById('erro_calculo').scrollIntoView({behavior: 'smooth', block: 'center'});
        }
    });

    alterarModo();
});
</script>
@endsection
