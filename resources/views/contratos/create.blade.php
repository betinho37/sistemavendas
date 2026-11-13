
@extends('layouts.app')

@section('title', 'Criar Novo Contrato | m1r4ge7')

@section('content')

<div class="container d-flex justify-content-center align-items-center py-4">
    <div class="card shadow-lg p-4"
         style="width: 500px; max-width: 100%; background-color: #1e1e1e; color: #fff; border-radius: 10px;">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Criar Novo Contrato</h4>
            <button type="button" class="btn btn-danger"
                    onclick="window.history.back()">✖</button>
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

        <form action="{{ route('contratos.store') }}" method="POST" enctype="multipart/form-data" class="mb-2"> id="formContrato">
            @csrf

            <div class="mb-3">
                <label for="data_inicio" class="form-label">Data de Início:</label>
                <input type="date"
                       name="data_inicio"
                       id="data_inicio"
                       value="{{ old('data_inicio', date('Y-m-d')) }}"
                       class="form-control bg-dark text-white"
                       required>
            </div>

            <div class="mb-3">
                <label for="cliente_id" class="form-label">Nome do Cliente:</label>
                <select name="cliente_id"
                        id="cliente_id"
                        class="form-control bg-dark text-white"
                        required>
                    <option value="" disabled {{ old('cliente_id') ? '' : 'selected' }}>
                        Selecione um cliente
                    </option>

                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}"
                            {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="valor" class="form-label">Valor Principal:</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark text-white">R$</span>
                    <input type="number"
                           step="0.01"
                           min="0.01"
                           id="valor"
                           name="valor"
                           class="form-control bg-dark text-white"
                           value="{{ old('valor', 0) }}"
                           required>
                </div>
            </div>

            <div class="mb-3">
                <label for="modo_calculo" class="form-label">Modo de cálculo:</label>
                <select id="modo_calculo" class="form-control bg-dark text-white">
                    <option value="taxa" selected>Calcular parcela pela taxa</option>
                    <option value="parcela">Calcular juros pelo valor da parcela</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="juros" class="form-label">Juros mensais (%):</label>
                <input type="number"
                       id="juros"
                       name="juros"
                       step="0.000001"
                       min="0"
                       class="form-control bg-dark text-white"
                       value="{{ old('juros', 0) }}"
                       required>
                <small id="ajudaJuros" class="text-white-50">
                    Informe a taxa mensal desejada.
                </small>
            </div>

            <div class="mb-3">
                <label for="parcela_desejada" class="form-label">
                    Valor desejado de cada parcela (R$):
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-dark text-white">R$</span>
                    <input type="number"
                           id="parcela_desejada"
                           step="0.01"
                           min="0.01"
                           class="form-control bg-dark text-white"
                           placeholder="Ex.: 420,00"
                           disabled>
                </div>
                <small class="text-white-50">
                    Utilize este campo para descobrir os juros a partir da parcela.
                </small>
            </div>

            <div class="d-flex gap-3">
                <div class="mb-3 flex-grow-1">
                    <label for="qtd_parcelas" class="form-label">N.º de Parcelas:</label>
                    <input type="number"
                           id="qtd_parcelas"
                           name="qtd_parcelas"
                           min="1"
                           max="120"
                           step="1"
                           class="form-control bg-dark text-white"
                           value="{{ old('qtd_parcelas', 1) }}"
                           required>
                </div>

                <div class="mb-3 flex-grow-1">
                    <label for="intervalo_dias" class="form-label">Intervalo em dias:</label>
                    <input type="number"
                           id="intervalo_dias"
                           name="intervalo_dias"
                           min="1"
                           class="form-control bg-dark text-white"
                           value="{{ old('intervalo_dias', 30) }}"
                           required>
                </div>
            </div>

            <div class="mb-3">
                <label for="tipo_juros" class="form-label">Tipo de Juros:</label>
                <select id="tipo_juros"
                        class="form-control bg-dark text-white">
                    <option value="" disabled selected>Escolha um tipo</option>
                    <option value="boleto">Venda no boleto</option>
                    <option value="banco">Empréstimo pessoal (banco)</option>
                    <option value="rotativo">Cartão de crédito rotativo</option>
                    <option value="financeira">Financeiras (sem banco)</option>
                    <option value="agiota">Juristas / risco alto</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Lucro do Contrato:</label>
                <input type="text"
                       id="lucro"
                       class="form-control bg-dark text-white"
                       readonly>
            </div>

            <div class="mb-3">
                <label class="form-label">Observações:</label>
                <textarea name="observacao"
                          class="form-control bg-dark text-white"
                          rows="3">{{ old('observacao') }}</textarea>
            </div>

            <div class="form-check mb-2">
                <input class="form-check-input"
                       type="checkbox"
                       name="vencimento_sabado"
                       value="1"
                       {{ old('vencimento_sabado') ? 'checked' : '' }}>
                <label class="form-check-label">Vencimento aos sábados</label>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input"
                       type="checkbox"
                       name="vencimento_domingo"
                       value="1"
                       {{ old('vencimento_domingo') ? 'checked' : '' }}>
                <label class="form-check-label">Vencimento aos domingos</label>
            </div>

            <div class="rounded p-3 mt-3"
                 style="background: #292929; border: 1px solid #444;">

                <p class="mb-2">
                    <strong>Total a receber:</strong>
                    <span id="total_receber">R$ 0,00</span>
                </p>

                <p class="mb-2">
                    <strong>Lucro estimado:</strong>
                    <span id="lucro_texto">R$ 0,00</span>
                </p>

                <p class="mb-2">
                    <strong>Valor da parcela:</strong>
                    <span id="valor_parcela">R$ 0,00</span>
                </p>

                <p class="mb-0">
                    <strong>Taxa mensal calculada:</strong>
                    <span id="taxa_calculada">0,00%</span>
                </p>
            </div>

            <div id="erro_calculo"
                 class="alert alert-danger mt-3 d-none"
                 role="alert"></div>

            <button type="submit" id="btnCriarContrato"
                    class="btn btn-success w-100 mt-3">
                Criar Contrato
            </button>
        </form>
    </div>
</div>

<script>
(function () {
    'use strict';

    function iniciarCalculoContrato() {
        const $ = id => document.getElementById(id);

        const campoValor = $('valor');
        const campoJuros = $('juros');
        const campoParcelas = $('qtd_parcelas');
        const campoIntervalo = $('intervalo_dias');
        const campoModo = $('modo_calculo');
        const campoParcela = $('parcela_desejada');
        const formulario = $('formContrato');

        if (
            !campoValor ||
            !campoJuros ||
            !campoParcelas ||
            !campoIntervalo ||
            !campoModo ||
            !campoParcela ||
            !formulario
        ) {
            console.error('Campos do contrato não encontrados.');
            return;
        }

        const formatar = valor => {
            return (valor / 100).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
        };

        const ler = campo => {
            const valor = campo.value.trim();
            return valor === '' ? NaN : Number(valor);
        };

        function limparResultados(mensagem = '') {
            $('total_receber').textContent = 'R$ 0,00';
            $('lucro_texto').textContent = 'R$ 0,00';
            $('valor_parcela').textContent = 'R$ 0,00';
            $('taxa_calculada').textContent = '0,00%';
            $('lucro').value = 'R$ 0,00';

            $('erro_calculo').textContent = mensagem;
            $('erro_calculo').classList.toggle('d-none', !mensagem);
        }

        function encontrarTaxa(principal, parcela, quantidade) {
            const parcelaSemJuros = principal / quantidade;

            if (parcela < parcelaSemJuros - 0.5) {
                return null;
            }

            if (Math.abs(parcela - parcelaSemJuros) <= 0.5) {
                return 0;
            }

            function calcularParcela(taxa) {
                if (taxa === 0) {
                    return principal / quantidade;
                }

                const fator = Math.pow(1 + taxa, quantidade);

                return principal * (
                    (taxa * fator) / (fator - 1)
                );
            }

            let minimo = 0;
            let maximo = 10;

            if (calcularParcela(maximo) < parcela) {
                return null;
            }

            for (let i = 0; i < 120; i++) {
                const meio = (minimo + maximo) / 2;
                const resultado = calcularParcela(meio);

                if (resultado < parcela) {
                    minimo = meio;
                } else {
                    maximo = meio;
                }
            }

            return (minimo + maximo) / 2;
        }

        function atualizarModo() {
            const modo = campoModo.value;

            const calcularPelaParcela = modo === 'parcela';

            campoJuros.readOnly = calcularPelaParcela;
            campoParcela.disabled = !calcularPelaParcela;

            campoParcela.required = calcularPelaParcela;

            $('ajudaJuros').textContent = calcularPelaParcela
                ? 'Taxa mensal calculada automaticamente.'
                : 'Informe a taxa mensal desejada.';

            campoParcela.placeholder = calcularPelaParcela
                ? 'Ex.: 420,00'
                : 'Disponível no modo de cálculo pela parcela';

            if (!calcularPelaParcela) {
                campoParcela.value = '';
            }
        }

        function calcularValores() {
            const valor = ler(campoValor);
            const parcelas = ler(campoParcelas);
            const jurosInformados = ler(campoJuros);
            const intervalo = ler(campoIntervalo);
            const modo = campoModo.value;

            const principal = Math.round(valor * 100);

            if (
                !Number.isFinite(valor) ||
                valor <= 0 ||
                !Number.isInteger(parcelas) ||
                parcelas < 1 ||
                parcelas > 120 ||
                !Number.isInteger(intervalo) ||
                intervalo < 1
            ) {
                limparResultados('Informe um capital, quantidade de parcelas e intervalo válidos.');
                return false;
            }

            let taxa;
            let parcelaCentavos;

            if (modo === 'parcela') {
                const parcelaDesejada = ler(campoParcela);

                if (
                    !Number.isFinite(parcelaDesejada) ||
                    parcelaDesejada <= 0
                ) {
                    limparResultados('Informe o valor desejado de cada parcela.');
                    return false;
                }

                parcelaCentavos = Math.round(parcelaDesejada * 100);

                taxa = encontrarTaxa(
                    principal,
                    parcelaCentavos,
                    parcelas
                );

                if (taxa === null) {
                    limparResultados(
                        'A parcela informada é insuficiente para quitar o capital emprestado nesse prazo.'
                    );
                    return false;
                }

                const taxaPercentual = taxa * 100;

                campoJuros.value = taxaPercentual.toFixed(6);

            } else {
                if (
                    !Number.isFinite(jurosInformados) ||
                    jurosInformados < 0 ||
                    jurosInformados > 1000
                ) {
                    limparResultados('Informe uma taxa entre 0% e 1.000%.');
                    return false;
                }

                taxa = jurosInformados / 100;

                if (taxa === 0) {
                    parcelaCentavos = Math.round(principal / parcelas);
                } else {
                    const fator = Math.pow(1 + taxa, parcelas);

                    const parcelaCalculada =
                        principal * (
                            (taxa * fator) / (fator - 1)
                        );

                    parcelaCentavos = Math.round(parcelaCalculada);
                }
            }

            const totalCentavos = parcelaCentavos * parcelas;
            const lucroCentavos = totalCentavos - principal;

            $('total_receber').textContent = formatar(totalCentavos);
            $('lucro_texto').textContent = formatar(lucroCentavos);
            $('valor_parcela').textContent = formatar(parcelaCentavos);
            $('lucro').value = formatar(lucroCentavos);

            $('taxa_calculada').textContent =
                (taxa * 100).toLocaleString('pt-BR', {
                    minimumFractionDigits: 4,
                    maximumFractionDigits: 6
                }) + '%';

            $('erro_calculo').classList.add('d-none');

            return true;
        }

        campoModo.addEventListener('change', function () {
            atualizarModo();
            calcularValores();
        });

        [
            'valor',
            'juros',
            'qtd_parcelas',
            'intervalo_dias',
            'parcela_desejada'
        ].forEach(id => {
            $(id).addEventListener('input', calcularValores);
            $(id).addEventListener('change', calcularValores);
        });

        formulario.addEventListener('submit', function (event) {
            if (!calcularValores()) {
                event.preventDefault();
                $('erro_calculo').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });

        atualizarModo();
        calcularValores();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciarCalculoContrato);
    } else {
        iniciarCalculoContrato();
    }
})();
</script>

@endsection
