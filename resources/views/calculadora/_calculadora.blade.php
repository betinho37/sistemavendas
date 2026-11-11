```blade
@extends('layouts.app')

@section('title', 'Painel Financeiro | m1r4ge7')

@section('content')
<style>
    .m1r7 {
        --m-bg: #090b10;
        --m-panel: #11151d;
        --m-panel2: #171c26;
        --m-border: rgba(212, 175, 85, .20);
        --m-gold: #d7b568;
        --m-gold-light: #f1d99b;
        --m-text: #f3f4f6;
        --m-muted: #9ba4b3;
        --m-green: #4ade80;
        --m-red: #fb7185;
        --m-blue: #7dd3fc;
        color: var(--m-text);
        padding: 24px 0 40px;
    }

    .m1r7 * { box-sizing: border-box; }

    .m1r7 .m-hero {
        position: relative;
        overflow: hidden;
        padding: 28px;
        border: 1px solid var(--m-border);
        border-radius: 22px;
        background:
            radial-gradient(ellipse at 90% 0%, rgba(215,181,104,.13), transparent 45%),
            linear-gradient(135deg, #171b24, #0c0f15 70%);
        margin-bottom: 22px;
    }

    .m1r7 .m-brand {
        color: var(--m-gold-light);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 4px;
        text-transform: uppercase;
    }

    .m1r7 .m-hero h1 {
        margin: 10px 0;
        font-size: clamp(25px, 4vw, 38px);
        font-weight: 850;
        letter-spacing: -1.2px;
        color: #fff;
    }

    .m1r7 .m-hero h1 span { color: var(--m-gold); }

    .m1r7 .m-subtitle {
        color: var(--m-muted);
        margin: 0;
        line-height: 1.7;
    }

    .m1r7 .m-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border: 1px solid var(--m-border);
        border-radius: 999px;
        background: rgba(215,181,104,.07);
        color: var(--m-gold-light);
        font-size: 12px;
        font-weight: 700;
        margin-top: 17px;
    }

    .m1r7 .m-panel {
        background: var(--m-panel);
        border: 1px solid var(--m-border);
        border-radius: 18px;
        margin-bottom: 20px;
        overflow: hidden;
        box-shadow: 0 12px 35px rgba(0,0,0,.13);
    }

    .m1r7 .m-panel-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 19px 22px;
        border-bottom: 1px solid rgba(255,255,255,.06);
        background: linear-gradient(90deg, rgba(215,181,104,.07), transparent);
    }

    .m1r7 .m-step {
        display: grid;
        place-items: center;
        flex: 0 0 36px;
        height: 36px;
        border-radius: 11px;
        background: rgba(215,181,104,.12);
        border: 1px solid var(--m-border);
        color: var(--m-gold-light);
        font-weight: 800;
    }

    .m1r7 .m-panel-head h2 {
        color: var(--m-text);
        font-size: 16px;
        font-weight: 800;
        margin: 0;
    }

    .m1r7 .m-panel-head p {
        margin: 4px 0 0;
        color: var(--m-muted);
        font-size: 12px;
    }

    .m1r7 .m-panel-body { padding: 22px; }

    .m1r7 .form-label {
        color: #d6dbe4;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .m1r7 .form-control {
        min-height: 45px;
        background: #0b0e14 !important;
        color: #f9fafb !important;
        border: 1px solid #2a303c !important;
        border-radius: 10px !important;
        box-shadow: none !important;
    }

    .m1r7 .form-control:focus {
        border-color: var(--m-gold) !important;
        box-shadow: 0 0 0 3px rgba(215,181,104,.12) !important;
    }

    .m1r7 .form-control:disabled,
    .m1r7 .form-control:read-only {
        opacity: .75;
        cursor: not-allowed;
    }

    .m1r7 .form-control::placeholder { color: #687181; }

    .m1r7 input[type=number] { font-variant-numeric: tabular-nums; }

    .m1r7 .m-help {
        display: block;
        color: var(--m-muted);
        font-size: 11px;
        margin-top: 7px;
        line-height: 1.5;
    }

    .m1r7 .m-section-label {
        color: var(--m-gold);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin-bottom: 16px;
    }

    .m1r7 .m-metric {
        height: 100%;
        min-height: 116px;
        padding: 18px;
        border: 1px solid rgba(255,255,255,.07);
        border-radius: 14px;
        background: linear-gradient(145deg, var(--m-panel2), #11141b);
    }

    .m1r7 .m-metric.featured {
        border-color: rgba(215,181,104,.34);
        background: linear-gradient(145deg, rgba(215,181,104,.12), #12151c 75%);
    }

    .m1r7 .m-metric-label {
        display: flex;
        align-items: center;
        gap: 7px;
        color: var(--m-muted);
        font-size: 12px;
        margin-bottom: 12px;
    }

    .m1r7 .m-metric-value {
        font-size: clamp(20px, 2.3vw, 28px);
        line-height: 1.2;
        font-weight: 850;
        letter-spacing: -.7px;
        overflow-wrap: anywhere;
        font-variant-numeric: tabular-nums;
    }

    .m1r7 .gold { color: var(--m-gold-light); }
    .m1r7 .green { color: var(--m-green); }
    .m1r7 .red { color: var(--m-red); }
    .m1r7 .blue { color: var(--m-blue); }

    .m1r7 .m-status {
        padding: 22px;
        border-radius: 15px;
        border: 1px solid rgba(255,255,255,.10);
        background: #171b24;
        color: var(--m-text);
    }

    .m1r7 .m-status.alert-success {
        background: rgba(34,197,94,.09);
        border-color: rgba(74,222,128,.30);
        color: #dcfce7;
    }

    .m1r7 .m-status.alert-warning {
        background: rgba(234,179,8,.09);
        border-color: rgba(250,204,21,.30);
        color: #fef3c7;
    }

    .m1r7 .m-status.alert-danger {
        background: rgba(244,63,94,.09);
        border-color: rgba(251,113,133,.30);
        color: #ffe4e6;
    }

    .m1r7 .m-status.alert-secondary {
        background: #171b24;
        color: var(--m-text);
    }

    .m1r7 .m-status .fw-bold { letter-spacing: .4px; }

    .m1r7 .m-table-wrap {
        border: 1px solid rgba(255,255,255,.07);
        border-radius: 12px;
        overflow-x: auto;
    }

    .m1r7 .table {
        --bs-table-bg: transparent;
        --bs-table-color: #e5e7eb;
        --bs-table-border-color: rgba(255,255,255,.08);
        margin-bottom: 0;
        vertical-align: middle;
    }

    .m1r7 .table thead th {
        background: #1b202a;
        color: var(--m-gold-light);
        font-size: 11px;
        letter-spacing: .6px;
        text-transform: uppercase;
        padding: 14px;
        white-space: nowrap;
    }

    .m1r7 .table tbody td {
        padding: 13px 14px;
        font-size: 13px;
    }

    .m1r7 .table-striped>tbody>tr:nth-of-type(odd)>* {
        --bs-table-bg-type: rgba(255,255,255,.025);
        color: #e5e7eb;
    }

    .m1r7 .text-muted { color: var(--m-muted) !important; }

    .m1r7 .btn-gold {
        min-height: 44px;
        padding: 11px 18px;
        background: linear-gradient(135deg, #f1d99b, #c59b48);
        color: #17130b;
        border: 0;
        border-radius: 10px;
        font-weight: 800;
        transition: transform .18s ease, filter .18s ease;
    }

    .m1r7 .btn-gold:hover {
        color: #17130b;
        filter: brightness(1.07);
        transform: translateY(-1px);
    }

    .m1r7 .btn-gold:disabled {
        opacity: .65;
        cursor: wait;
        transform: none;
    }

    .m1r7 .btn-outline-gold {
        color: var(--m-gold-light);
        border: 1px solid var(--m-border);
        background: rgba(215,181,104,.04);
        border-radius: 10px;
        min-height: 40px;
    }

    .m1r7 .btn-outline-gold:hover {
        background: rgba(215,181,104,.12);
        color: white;
        border-color: var(--m-gold);
    }

    .m1r7 .m-note {
        padding: 14px 16px;
        border-radius: 11px;
        background: rgba(125,211,252,.055);
        border: 1px solid rgba(125,211,252,.15);
        color: #b7c7d9;
        font-size: 12px;
        line-height: 1.65;
    }

    .m1r7 .m-footer-note {
        text-align: center;
        color: var(--m-muted);
        font-size: 11px;
        line-height: 1.7;
        padding: 8px 15px;
    }

    @media (max-width: 767px) {
        .m1r7 { padding-top: 12px; }
        .m1r7 .m-hero { padding: 22px 18px; border-radius: 16px; }
        .m1r7 .m-panel { border-radius: 14px; }
        .m1r7 .m-panel-head { padding: 16px; }
        .m1r7 .m-panel-body { padding: 16px; }
        .m1r7 .m-metric { min-height: 100px; padding: 14px; }
        .m1r7 .m-metric-value { font-size: 21px; }
        .m1r7 .m-panel-head h2 { font-size: 14px; }
    }
</style>

<div class="container-fluid m1r7">
    <div class="mx-auto" style="max-width:1150px;">

        <header class="m-hero">
            <div class="m-brand">♛ m1r4ge7 · Gestão financeira</div>
            <h1>PAINEL <span>FINANCEIRO</span></h1>
            <p class="m-subtitle">
                Simule, analise a rentabilidade e avalie os riscos antes de contratar uma operação.
            </p>
            <div class="m-tag">● ANÁLISE INTELIGENTE DE VIABILIDADE</div>
        </header>

        {{-- DADOS DA OPERAÇÃO --}}
        <section class="m-panel">
            <div class="m-panel-head">
                <div class="m-step">01</div>
                <div>
                    <h2>Dados da operação</h2>
                    <p>Informe o capital, a quantidade de parcelas e escolha como deseja calcular.</p>
                </div>
            </div>

            <div class="m-panel-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="clienteNome" class="form-label">Nome do cliente</label>
                        <input type="text" id="clienteNome" class="form-control" placeholder="Nome do cliente">
                    </div>

                    <div class="col-md-6">
                        <label for="valor" class="form-label">Capital emprestado (R$)</label>
                        <input type="number" id="valor" class="form-control" min="0.01" step="0.01" value="1000">
                    </div>

                    <div class="col-md-4">
                        <label for="modoCalculo" class="form-label">Modo de cálculo</label>
                        <select id="modoCalculo" class="form-control">
                            <option value="taxa">Calcular parcela pela taxa</option>
                            <option value="parcela">Calcular juros pela parcela</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="juros" class="form-label">Juros mensais (%)</label>
                        <input type="number" id="juros" class="form-control" min="0" max="1000" step="0.01" value="10">
                        <small class="m-help" id="ajudaJuros">Informe a taxa para calcular o valor da parcela.</small>
                    </div>

                    <div class="col-md-4">
                        <label for="parcelaDesejada" class="form-label">Valor desejado da parcela (R$)</label>
                        <input type="number" id="parcelaDesejada" class="form-control" min="0.01" step="0.01" placeholder="Ex.: 420,00" disabled>
                        <small class="m-help">No modo inverso, informe quanto deseja cobrar por mês.</small>
                    </div>

                    <div class="col-md-6">
                        <label for="parcelas" class="form-label">Quantidade de parcelas</label>
                        <input type="number" id="parcelas" class="form-control" min="1" max="120" step="1" value="2">
                    </div>

                    <div class="col-md-6">
                        <label for="intervalo" class="form-label">Intervalo entre parcelas (dias)</label>
                        <input type="number" id="intervalo" class="form-control" min="1" max="365" step="1" value="30">
                    </div>

                </div>
            </div>
        </section>

        {{-- PARÂMETROS INTERNOS MANTIDOS PARA O CÁLCULO --}}
        <section class="m-panel" style="display:none;">
            <div class="m-panel-head">
                <div class="m-step">02</div>
                <div>
                    <h2>Parâmetros de risco e rentabilidade</h2>
                    <p>Critérios internos utilizados na análise financeira.</p>
                </div>
            </div>

            <div class="m-panel-body">
                <div class="m-section-label">Custos da operação</div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="custosOperacionais" class="form-label">Despesas operacionais (R$)</label>
                        <input type="number" id="custosOperacionais" class="form-control" min="0" step="0.01" value="0">
                    </div>

                    <div class="col-md-4">
                        <label for="custoCapital" class="form-label">Custo do capital ao mês (%)</label>
                        <input type="number" id="custoCapital" class="form-control" min="0" max="100" step="0.1" value="2">
                    </div>

                    <div class="col-md-4">
                        <label for="probInadimplencia" class="form-label">Probabilidade estimada de inadimplência (%)</label>
                        <input type="number" id="probInadimplencia" class="form-control" min="0" max="100" step="0.5" value="5">
                    </div>
                </div>

                <div class="m-section-label mt-4">Critérios de decisão</div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="recuperacaoInadimplencia" class="form-label">Recuperação estimada do principal (%)</label>
                        <input type="number" id="recuperacaoInadimplencia" class="form-control" min="0" max="100" step="1" value="30">
                    </div>

                    <div class="col-md-4">
                        <label for="margemMinima" class="form-label">Margem líquida mínima (%)</label>
                        <input type="number" id="margemMinima" class="form-control" min="0" max="1000" step="0.5" value="5">
                    </div>

                    <div class="col-md-4">
                        <label for="riscoMaximo" class="form-label">Limite de risco desejado (%)</label>
                        <input type="number" id="riscoMaximo" class="form-control" min="0" max="100" step="0.5" value="10">
                    </div>

                    <div class="col-md-4">
                        <label for="prazoMaximoDias" class="form-label">Prazo máximo desejado (dias)</label>
                        <input type="number" id="prazoMaximoDias" class="form-control" min="1" max="3650" step="1" value="180">
                    </div>
                </div>

                <button type="button" id="btnRestaurarParametros" class="btn btn-outline-gold mt-4">
                    Restaurar parâmetros padrão
                </button>
            </div>
        </section>

        {{-- RESUMO FINANCEIRO --}}
        <section class="m-panel">
            <div class="m-panel-head">
                <div class="m-step">03</div>
                <div>
                    <h2>Resumo financeiro</h2>
                    <p>Veja os números essenciais da operação simulada.</p>
                </div>
            </div>

            <div class="m-panel-body">
                <div id="erroCalculo" class="alert alert-danger d-none" role="alert"></div>

                <div class="row g-3">
                    <div class="col-6 col-lg-4">
                        <div class="m-metric">
                            <div class="m-metric-label">↗ Total contratado</div>
                            <div class="m-metric-value gold" id="totalGeral">R$ 0,00</div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-4">
                        <div class="m-metric">
                            <div class="m-metric-label">＋ Lucro bruto</div>
                            <div class="m-metric-value green" id="lucro">R$ 0,00</div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-4">
                        <div class="m-metric">
                            <div class="m-metric-label">▤ Valor da parcela</div>
                            <div class="m-metric-value" id="valorParcela">R$ 0,00</div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-4">
                        <div class="m-metric">
                            <div class="m-metric-label">◷ Prazo total</div>
                            <div class="m-metric-value blue" id="prazoTotal">0 dias</div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-4">
                        <div class="m-metric featured">
                            <div class="m-metric-label">◆ Lucro líquido estimado</div>
                            <div class="m-metric-value gold" id="lucroLiquido">R$ 0,00</div>
                        </div>
                    </div>

                    <div class="col-6 col-lg-4">
                        <div class="m-metric">
                            <div class="m-metric-label">％ Margem líquida</div>
                            <div class="m-metric-value" id="margemLiquida">0%</div>
                        </div>
                    </div>
                </div>

                <div id="painelViabilidade" class="m-status alert-secondary mt-4" role="status" aria-live="polite">
                    Preencha os dados para avaliar a operação.
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <div class="m-metric">
                            <div class="m-metric-label">⚠ Perda esperada por inadimplência</div>
                            <div class="m-metric-value red" id="perdaEsperada">R$ 0,00</div>
                            <small class="m-help">Estimativa estatística, não uma perda confirmada.</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="m-metric">
                            <div class="m-metric-label">◷ Custo estimado do capital</div>
                            <div class="m-metric-value" id="custoCapitalTotal">R$ 0,00</div>
                        </div>
                    </div>
                </div>

                <p class="m-note mt-3 mb-0" id="observacaoParcelas">
                    A análise será atualizada automaticamente.
                </p>
            </div>
        </section>

        {{-- SIMULADOR DE ATRASOS --}}
        <section class="m-panel">
            <div class="m-panel-head">
                <div class="m-step">04</div>
                <div>
                    <h2>Simulador de atrasos</h2>
                    <p>Compare o custo adicional de receber depois do prazo.</p>
                </div>
            </div>

            <div class="m-panel-body">
                <p class="m-note mb-3">
                    Cenários estimados considerando o custo adicional do capital pelo tempo de atraso.
                    Não representam previsão de inadimplência nem incluem multa ou juros de mora.
                </p>

                <div class="m-table-wrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Atraso simulado</th>
                                <th>Custo adicional</th>
                                <th>Lucro líquido estimado</th>
                                <th>Margem estimada</th>
                            </tr>
                        </thead>

                        <tbody id="tabelaCenarios">
                            <tr>
                                <td colspan="4" class="text-center text-muted">Aguardando simulação.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- CRONOGRAMA --}}
        <section class="m-panel">
            <div class="m-panel-head">
                <div class="m-step">05</div>
                <div>
                    <h2>Cronograma de parcelas</h2>
                    <p>Confira as datas previstas e os valores de cada pagamento.</p>
                </div>
            </div>

            <div class="m-panel-body">
                <div class="m-table-wrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Parcela</th>
                                <th>Vencimento previsto</th>
                                <th>Valor</th>
                            </tr>
                        </thead>

                        <tbody id="tabelaParcelas">
                            <tr>
                                <td colspan="3" class="text-center text-muted">Aguardando simulação.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    <button type="button" id="btnExportarPdf" class="btn btn-gold">
                        ↓ Exportar relatório em PDF
                    </button>
                </div>
            </div>
        </section>

        {{-- RELATÓRIO PARA PDF --}}
        <div id="pdfParcelas" style="display:none;background:#fff;color:#222;padding:24px;font-family:Arial,sans-serif;">
            <div style="text-align:center;margin-bottom:20px;">
                <h2 style="margin-bottom:4px;">m1r4ge7</h2>
                <p>Relatório de simulação financeira</p>
            </div>

            <hr>

            <p><strong>Cliente:</strong> <span id="clientePdf"></span></p>
            <p><strong>Data da simulação:</strong> <span id="dataPdf"></span></p>

            <h3>Resumo financeiro</h3>
            <div id="resumoPdf"></div>

            <h3>Análise de viabilidade</h3>
            <div id="analisePdf"></div>

            <h3>Cenários de atraso</h3>
            <table style="width:100%;border-collapse:collapse;" border="1" cellpadding="6">
                <thead>
                    <tr>
                        <th>Atraso</th>
                        <th>Custo adicional</th>
                        <th>Lucro líquido</th>
                        <th>Margem</th>
                    </tr>
                </thead>
                <tbody id="tabelaCenariosPdf"></tbody>
            </table>

            <h3 style="margin-top:20px;">Cronograma de parcelas</h3>
            <table style="width:100%;border-collapse:collapse;" border="1" cellpadding="6">
                <thead>
                    <tr>
                        <th>Parcela</th>
                        <th>Vencimento</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody id="tabelaParcelasPdf"></tbody>
            </table>

            <p style="font-size:10px;margin-top:20px;">
                Documento de simulação. Os valores de risco, custos e inadimplência são estimativas configuráveis.
            </p>
        </div>

        <footer class="m-footer-note">
            m1r4ge7 · Gestão financeira<br>
            Simulações são estimativas e não garantem recebimento ou resultado.
        </footer>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    function iniciarPainel() {
        const $ = id => document.getElementById(id);

        const ids = [
            'clienteNome',
            'valor',
            'modoCalculo',
            'juros',
            'parcelaDesejada',
            'parcelas',
            'intervalo',
            'custosOperacionais',
            'custoCapital',
            'probInadimplencia',
            'recuperacaoInadimplencia',
            'margemMinima',
            'riscoMaximo',
            'prazoMaximoDias',
            'btnRestaurarParametros',
            'btnExportarPdf'
        ];

        const campos = {};

        ids.forEach(id => {
            campos[id] = $(id);
        });

        if (ids.some(id => !$(id))) {
            console.error('Painel financeiro: campos não encontrados.', ids.filter(id => !$(id)));
            return;
        }

        const moeda = new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });

        const percentual = new Intl.NumberFormat('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 4
        });

        let calculoAtual = null;

        function reais(valor) {
            return moeda.format(valor / 100);
        }

        function fmtPct(valor) {
            return percentual.format(valor) + '%';
        }

        function centavos(valor) {
            return Math.round(valor * 100);
        }

        function ler(id) {
            const campo = $(id);
            const valor = campo.value.trim();

            return valor === '' ? NaN : Number(valor);
        }

        function dataBR(data) {
            return data.toLocaleDateString('pt-BR');
        }

        function preencherTabela(id, linhas, colunas, mensagem) {
            const tbody = $(id);
            tbody.replaceChildren();

            if (!linhas.length) {
                const tr = document.createElement('tr');
                const td = document.createElement('td');

                td.colSpan = colunas;
                td.className = 'text-center text-muted';
                td.textContent = mensagem;

                tr.appendChild(td);
                tbody.appendChild(tr);
                return;
            }

            linhas.forEach(linha => {
                const tr = document.createElement('tr');

                linha.forEach(valor => {
                    const td = document.createElement('td');
                    td.textContent = String(valor);
                    tr.appendChild(td);
                });

                tbody.appendChild(tr);
            });
        }

        function mostrarErro(mensagem) {
            $('erroCalculo').textContent = mensagem;
            $('erroCalculo').classList.remove('d-none');

            calculoAtual = null;

            [
                'totalGeral',
                'lucro',
                'valorParcela',
                'prazoTotal',
                'lucroLiquido',
                'margemLiquida',
                'perdaEsperada',
                'custoCapitalTotal'
            ].forEach(id => {
                $(id).textContent = '—';
            });

            $('painelViabilidade').className =
                'm-status alert-danger mt-4';

            $('painelViabilidade').textContent =
                'Não foi possível avaliar a operação.';

            $('observacaoParcelas').textContent = mensagem;

            preencherTabela('tabelaParcelas', [], 3, mensagem);
            preencherTabela('tabelaCenarios', [], 4, mensagem);
        }

        function obterDados() {
            return {
                valor: ler('valor'),
                modoCalculo: $('modoCalculo').value,
                juros: ler('juros'),
                parcelaDesejada: ler('parcelaDesejada'),
                parcelas: ler('parcelas'),
                intervalo: ler('intervalo'),
                custosOperacionais: ler('custosOperacionais'),
                custoCapital: ler('custoCapital'),
                probInadimplencia: ler('probInadimplencia'),
                recuperacaoInadimplencia: ler('recuperacaoInadimplencia'),
                margemMinima: ler('margemMinima'),
                riscoMaximo: ler('riscoMaximo'),
                prazoMaximoDias: ler('prazoMaximoDias')
            };
        }

        function validar(d) {
            if (!Number.isFinite(d.valor) || d.valor <= 0) {
                return 'Informe um valor emprestado maior que zero.';
            }

            if (d.modoCalculo === 'taxa') {
                if (!Number.isFinite(d.juros) || d.juros < 0 || d.juros > 1000) {
                    return 'A taxa mensal deve ficar entre 0% e 1.000%.';
                }
            }

            if (!Number.isInteger(d.parcelas) || d.parcelas < 1 || d.parcelas > 120) {
                return 'Informe de 1 a 120 parcelas.';
            }

            if (!Number.isInteger(d.intervalo) || d.intervalo < 1 || d.intervalo > 365) {
                return 'O intervalo deve ficar entre 1 e 365 dias.';
            }

            if (!['taxa', 'parcela'].includes(d.modoCalculo)) {
                return 'Selecione um modo de cálculo válido.';
            }

            if (d.modoCalculo === 'parcela') {
                if (!Number.isFinite(d.parcelaDesejada) || d.parcelaDesejada <= 0) {
                    return 'Informe o valor desejado de cada parcela.';
                }
            }

            const limites = [
                ['custosOperacionais', 0, 100000000],
                ['custoCapital', 0, 100],
                ['probInadimplencia', 0, 100],
                ['recuperacaoInadimplencia', 0, 100],
                ['margemMinima', 0, 1000],
                ['riscoMaximo', 0, 100],
                ['prazoMaximoDias', 1, 3650]
            ];

            for (const [id, min, max] of limites) {
                if (!Number.isFinite(d[id]) || d[id] < min || d[id] > max) {
                    return 'Verifique os parâmetros financeiros da operação.';
                }
            }

            if (!Number.isInteger(d.prazoMaximoDias)) {
                return 'O prazo máximo deve ser um número inteiro de dias.';
            }

            return null;
        }

        /*
         * Descobre a taxa mensal pela parcela desejada.
         * Utiliza a mesma fórmula Price dos contratos.
         */
        function encontrarTaxaMensal(principal, parcela, quantidade) {
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

            for (let i = 0; i < 100; i++) {
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

        function calcular() {
            $('erroCalculo').classList.add('d-none');

            const d = obterDados();
            const problema = validar(d);

            if (problema) {
                mostrarErro(problema);
                return;
            }

            const principal = centavos(d.valor);
            const quantidade = d.parcelas;

            let taxa;
            let parcelaCentavos;

            if (d.modoCalculo === 'parcela') {
                parcelaCentavos = centavos(d.parcelaDesejada);

                taxa = encontrarTaxaMensal(
                    principal,
                    parcelaCentavos,
                    quantidade
                );

                if (taxa === null) {
                    mostrarErro(
                        'O valor da parcela é insuficiente para quitar o capital emprestado nesse prazo.'
                    );
                    return;
                }

                d.juros = taxa * 100;

                $('juros').value = d.juros.toFixed(4);

            } else {
                taxa = d.juros / 100;

                if (taxa === 0) {
                    parcelaCentavos = Math.round(principal / quantidade);
                } else {
                    const fator = Math.pow(1 + taxa, quantidade);

                    const parcelaCalculada =
                        principal * (
                            (taxa * fator) /
                            (fator - 1)
                        );

                    parcelaCentavos = Math.round(parcelaCalculada);
                }
            }

            const totalCentavos = parcelaCentavos * quantidade;
            const jurosCentavos = totalCentavos - principal;

            const prazoDias = d.intervalo * quantidade;
            const tempoMeses = prazoDias / 30;

            const despesas = centavos(d.custosOperacionais);

            const perdaEsperada = Math.round(
                principal *
                (d.probInadimplencia / 100) *
                (1 - d.recuperacaoInadimplencia / 100)
            );

            const custoCapital = Math.round(
                principal *
                (d.custoCapital / 100) *
                tempoMeses
            );

            const lucroLiquido =
                jurosCentavos -
                despesas -
                perdaEsperada -
                custoCapital;

            const margem = principal > 0
                ? (lucroLiquido / principal) * 100
                : 0;

            const riscoExcedido =
                d.probInadimplencia > d.riscoMaximo;

            const prazoExcedido =
                prazoDias > d.prazoMaximoDias;

            const margemAbaixo =
                margem < d.margemMinima;

            let status;
            let classe;

            const motivos = [];

            if (lucroLiquido <= 0) {
                status = 'REAVALIAR OPERAÇÃO';
                classe = 'alert-danger';
                motivos.push('O lucro líquido estimado é nulo ou negativo');
            } else if (riscoExcedido || prazoExcedido || margemAbaixo) {
                status = 'ATENÇÃO';
                classe = 'alert-warning';

                if (riscoExcedido) {
                    motivos.push('O risco supera o limite configurado');
                }

                if (prazoExcedido) {
                    motivos.push('O prazo supera o máximo desejado');
                }

                if (margemAbaixo) {
                    motivos.push('A margem líquida está abaixo da meta');
                }
            } else {
                status = 'FAVORÁVEL';
                classe = 'alert-success';
                motivos.push('Os indicadores estão dentro dos limites configurados');
            }

            const cenarios = [0, 7, 15, 30].map(dias => {
                const custoExtra = Math.round(
                    principal *
                    (d.custoCapital / 100) *
                    (dias / 30)
                );

                const lucroCenario = lucroLiquido - custoExtra;

                return {
                    dias,
                    custoExtra,
                    lucroCenario,
                    margem: (lucroCenario / principal) * 100
                };
            });

            const hoje = new Date();

            const agenda = Array.from(
                { length: quantidade },
                (_, i) => {
                    const vencimento = new Date(hoje);

                    vencimento.setDate(
                        vencimento.getDate() + d.intervalo * (i + 1)
                    );

                    return {
                        numero: i + 1,
                        vencimento,
                        valor: parcelaCentavos
                    };
                }
            );

            calculoAtual = {
                d,
                principal,
                prazoDias,
                taxa,
                jurosCentavos,
                totalCentavos,
                parcelaCentavos,
                despesas,
                perdaEsperada,
                custoCapital,
                lucroLiquido,
                margem,
                status,
                classe,
                motivos,
                cenarios,
                agenda
            };

            atualizarTela(calculoAtual);
        }

        function atualizarTela(c) {
            $('totalGeral').textContent = reais(c.totalCentavos);
            $('lucro').textContent = reais(c.jurosCentavos);
            $('valorParcela').textContent = reais(c.parcelaCentavos);
            $('prazoTotal').textContent = c.prazoDias + ' dias';
            $('lucroLiquido').textContent = reais(c.lucroLiquido);
            $('margemLiquida').textContent = fmtPct(c.margem);
            $('perdaEsperada').textContent = reais(c.perdaEsperada);
            $('custoCapitalTotal').textContent = reais(c.custoCapital);

            $('lucroLiquido').className =
                'm-metric-value ' +
                (c.lucroLiquido >= 0 ? 'green' : 'red');

            $('margemLiquida').className =
                'm-metric-value ' +
                (c.margem >= c.d.margemMinima ? 'green' : 'red');

            const painel = $('painelViabilidade');

            painel.className = 'm-status ' + c.classe + ' mt-4';
            painel.replaceChildren();

            const titulo = document.createElement('div');
            titulo.className = 'fw-bold fs-5';
            titulo.textContent = c.status;

            const explicacao = document.createElement('div');
            explicacao.className = 'mt-2';
            explicacao.textContent = c.motivos.join('; ') + '.';

            const detalhe = document.createElement('div');
            detalhe.className = 'small mt-2';
            detalhe.textContent =
                'Avaliação baseada nos parâmetros informados. Não garante o pagamento do cliente.';

            painel.append(titulo, explicacao, detalhe);

            $('observacaoParcelas').textContent =
                'Sistema Price · Taxa mensal: ' + fmtPct(c.taxa * 100) +
                ' · Juros totais: ' + reais(c.jurosCentavos) +
                ' · Despesas: ' + reais(c.despesas) +
                ' · Perda esperada: ' + reais(c.perdaEsperada) +
                ' · Valores estimados para análise interna.';

            preencherTabela(
                'tabelaParcelas',
                c.agenda.map(p => [
                    p.numero + 'ª',
                    dataBR(p.vencimento),
                    reais(p.valor)
                ]),
                3,
                'Nenhuma parcela para exibir.'
            );

            preencherTabela(
                'tabelaCenarios',
                c.cenarios.map(s => [
                    s.dias === 0 ? 'Sem atraso' : s.dias + ' dias',
                    reais(s.custoExtra),
                    reais(s.lucroCenario),
                    fmtPct(s.margem)
                ]),
                4,
                'Nenhum cenário para exibir.'
            );
        }

        function prepararPDF() {
            const c = calculoAtual;

            $('clientePdf').textContent =
                campos.clienteNome.value.trim() || 'Não informado';

            $('dataPdf').textContent = dataBR(new Date());

            $('resumoPdf').textContent =
                'Capital emprestado: ' + reais(c.principal) +
                ' | Taxa mensal: ' + fmtPct(c.taxa * 100) +
                ' | Juros: ' + reais(c.jurosCentavos) +
                ' | Total contratado: ' + reais(c.totalCentavos) +
                ' | Parcela: ' + reais(c.parcelaCentavos) +
                ' | Prazo: ' + c.prazoDias + ' dias' +
                ' | Despesas: ' + reais(c.despesas) +
                ' | Custo de capital: ' + reais(c.custoCapital) +
                ' | Perda esperada: ' + reais(c.perdaEsperada) +
                ' | Lucro líquido: ' + reais(c.lucroLiquido) +
                ' | Margem: ' + fmtPct(c.margem);

            $('analisePdf').textContent =
                c.status + ': ' + c.motivos.join('; ') +
                '. Probabilidade estimada de inadimplência: ' +
                fmtPct(c.d.probInadimplencia) + '.';

            preencherTabela(
                'tabelaCenariosPdf',
                c.cenarios.map(s => [
                    s.dias === 0 ? 'Sem atraso' : s.dias + ' dias',
                    reais(s.custoExtra),
                    reais(s.lucroCenario),
                    fmtPct(s.margem)
                ]),
                4,
                'Sem cenários.'
            );

            preencherTabela(
                'tabelaParcelasPdf',
                c.agenda.map(p => [
                    p.numero + 'ª',
                    dataBR(p.vencimento),
                    reais(p.valor)
                ]),
                3,
                'Sem parcelas.'
            );
        }

        function carregarScript(src) {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');

                script.src = src;
                script.async = true;

                script.onload = () => {
                    if (typeof window.html2pdf === 'function') {
                        resolve();
                    } else {
                        reject(new Error('Biblioteca PDF não inicializada.'));
                    }
                };

                script.onerror = () => {
                    reject(new Error('Não foi possível carregar: ' + src));
                };

                document.head.appendChild(script);
            });
        }

        async function carregarBibliotecaPDF() {
            if (typeof window.html2pdf === 'function') {
                return;
            }

            if (window.carregandoHtml2pdf) {
                return window.carregandoHtml2pdf;
            }

            window.carregandoHtml2pdf = (async function () {
                try {
                    await carregarScript(
                        'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js'
                    );
                } catch (erro) {
                    console.warn('Primeiro endereço da biblioteca PDF falhou. Tentando alternativa.', erro);

                    await carregarScript(
                        'https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js'
                    );
                }
            })();

            try {
                await window.carregandoHtml2pdf;
            } catch (erro) {
                window.carregandoHtml2pdf = null;
                throw erro;
            }
        }

        async function exportarPDF() {
            if (!calculoAtual) {
                calcular();
            }

            if (!calculoAtual) {
                alert('Corrija os dados da simulação antes de gerar o PDF.');
                return;
            }

            const botao = $('btnExportarPdf');
            const original = botao.textContent;
            let copia = null;

            botao.disabled = true;
            botao.textContent = 'Gerando PDF...';

            try {
                await carregarBibliotecaPDF();

                prepararPDF();

                copia = $('pdfParcelas').cloneNode(true);
                copia.id = 'relatorioPDFTemporario';

                copia.style.display = 'block';
                copia.style.position = 'fixed';
                copia.style.top = '0';
                copia.style.left = '-10000px';
                copia.style.width = '794px';
                copia.style.padding = '30px';
                copia.style.background = '#ffffff';
                copia.style.color = '#222222';
                copia.style.zIndex = '999999';
                copia.style.overflow = 'visible';

                document.body.appendChild(copia);

                await new Promise(resolve => {
                    requestAnimationFrame(() => {
                        requestAnimationFrame(resolve);
                    });
                });

                await window.html2pdf()
                    .set({
                        margin: 8,
                        filename: 'simulacao-m1r4ge7.pdf',
                        image: {
                            type: 'jpeg',
                            quality: 0.98
                        },
                        html2canvas: {
                            scale: 2,
                            useCORS: true,
                            backgroundColor: '#ffffff',
                            scrollY: 0,
                            logging: false
                        },
                        jsPDF: {
                            unit: 'mm',
                            format: 'a4',
                            orientation: 'portrait'
                        },
                        pagebreak: {
                            mode: ['css', 'legacy']
                        }
                    })
                    .from(copia)
                    .save();

            } catch (erro) {
                console.error('Erro ao gerar PDF:', erro);

                alert(
                    'Não foi possível gerar o PDF. Verifique sua conexão com a internet e tente novamente. Detalhes no console do navegador (F12).'
                );
            } finally {
                if (copia) {
                    copia.remove();
                }

                botao.disabled = false;
                botao.textContent = original;
            }
        }

        function atualizarModoCalculo() {
            const modo = $('modoCalculo').value;
            const modoParcela = modo === 'parcela';

            $('juros').readOnly = modoParcela;
            $('parcelaDesejada').disabled = !modoParcela;

            $('ajudaJuros').textContent = modoParcela
                ? 'Taxa mensal calculada automaticamente pela parcela informada.'
                : 'Informe a taxa para calcular o valor da parcela.';

            $('parcelaDesejada').placeholder = modoParcela
                ? 'Ex.: 420,00'
                : 'Disponível no modo de cálculo inverso';
        }

        $('btnRestaurarParametros').addEventListener('click', function () {
            const padroes = {
                custosOperacionais: '0',
                custoCapital: '2',
                probInadimplencia: '5',
                recuperacaoInadimplencia: '30',
                margemMinima: '5',
                riscoMaximo: '10',
                prazoMaximoDias: '180'
            };

            Object.entries(padroes).forEach(([id, valor]) => {
                $(id).value = valor;
            });

            calcular();
        });

        $('modoCalculo').addEventListener('change', function () {
            atualizarModoCalculo();
            calcular();
        });

        [
            'valor',
            'juros',
            'parcelaDesejada',
            'parcelas',
            'intervalo',
            'custosOperacionais',
            'custoCapital',
            'probInadimplencia',
            'recuperacaoInadimplencia',
            'margemMinima',
            'riscoMaximo',
            'prazoMaximoDias'
        ].forEach(id => {
            $(id).addEventListener('input', calcular);
            $(id).addEventListener('change', calcular);
        });

        $('btnExportarPdf').addEventListener('click', exportarPDF);

        atualizarModoCalculo();
        calcular();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciarPainel);
    } else {
        iniciarPainel();
    }
})();
</script>
@endpush
