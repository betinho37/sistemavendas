
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid py-4 dashboard">

    {{-- CABEÇALHO --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h3 class="text-white fw-bold mb-1">Painel financeiro</h3>
            <p class="text-secondary mb-0">Visão geral dos contratos e recebimentos</p>
        </div>

        <div class="text-secondary small">
            <i class="bi bi-calendar3 me-1"></i>
            {{ now()->format('d/m/Y') }}
        </div>
    </div>

    {{-- INDICADORES --}}
    <div class="row g-3 mb-4">
<div class="col-6 col-md-4 col-xl-2">
    <div class="dash-card">
        <div class="dash-icon green">
            <i class="bi bi-cash-stack"></i>
        </div>
        <small>Total recebido</small>
        <h5>R$ {{ number_format($totalRecebido, 2, ',', '.') }}</h5>
        <span>Pagamentos registrados</span>
    </div>
</div>

<div class="col-6 col-md-4 col-xl-2">
    <div class="dash-card">
        <div class="dash-icon cyan">
            <i class="bi bi-piggy-bank"></i>
        </div>
        <small>Capital em aberto</small>
        <h5>R$ {{ number_format($capitalEmAberto, 2, ',', '.') }}</h5>
        <span>Capital ainda não recuperado</span>
    </div>
</div>

<div class="col-6 col-md-4 col-xl-2">
    <div class="dash-card">
        <div class="dash-icon purple">
            <i class="bi bi-graph-up"></i>
        </div>
        <small>Lucro realizado</small>
        <h5 class="text-success">R$ {{ number_format($lucroObtido, 2, ',', '.') }}</h5>
        <span>Juros efetivamente recebidos</span>
    </div>
</div>
        <div class="col-6 col-md-4 col-xl-2">
            <div class="dash-card">
                <div class="dash-icon blue"><i class="bi bi-people"></i></div>
                <small>Clientes</small>
                <h3>{{ $clientes }}</h3>
                <span>Clientes cadastrados</span>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="dash-card">
                <div class="dash-icon purple"><i class="bi bi-file-earmark-text"></i></div>
                <small>Contratos</small>
                <h3>{{ $contratos }}</h3>
                <span>Contratos ativos e registrados</span>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="dash-card">
                <div class="dash-icon red"><i class="bi bi-exclamation-circle"></i></div>
                <small>Parcelas vencidas</small>
                <h3 class="text-danger">{{ $parcelasVencidas }}</h3>
                <span>Precisam de atenção</span>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="dash-card">
                <div class="dash-icon yellow"><i class="bi bi-clock-history"></i></div>
                <small>Parcelas a vencer</small>
                <h3 class="text-warning">{{ $parcelasAVencer }}</h3>
                <span>Recebimentos pendentes</span>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="dash-card">
                <div class="dash-icon cyan"><i class="bi bi-wallet2"></i></div>
                <small>Capital investido</small>
                <h5>R$ {{ number_format($capitalInvestido, 2, ',', '.') }}</h5>
                <span>Valor emprestado</span>
            </div>
        </div>

        <div class="col-6 col-md-4 col-xl-2">
            <div class="dash-card">
                <div class="dash-icon green"><i class="bi bi-graph-up-arrow"></i></div>
                <small>Lucro previsto</small>
                <h5 class="text-success">R$ {{ number_format($previsaoLucro, 2, ',', '.') }}</h5>
                <span>Parcelas pendentes</span>
            </div>
        </div>

    </div>

    {{-- GRÁFICOS --}}
    <div class="row g-3 mb-4">

        <div class="col-lg-6">
            <div class="dash-panel h-100">
                <div class="panel-heading">
                    <div>
                        <h5>Parcelas</h5>
                        <small>Distribuição dos recebimentos</small>
                    </div>
                    <i class="bi bi-bar-chart-line"></i>
                </div>
                <div class="chart-area">
                    <canvas id="graficoParcelas"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dash-panel h-100">
                <div class="panel-heading">
                    <div>
                        <h5>Resultado financeiro</h5>
                        <small>Lucro obtido e lucro previsto</small>
                    </div>
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="chart-area">
                    <canvas id="graficoLucro"></canvas>
                </div>
            </div>
        </div>

    </div>

    {{-- CALENDÁRIO E PARCELAS --}}
    <div class="row g-3 mb-4">

        <div class="col-xl-6">
            <div class="dash-panel h-100">
                <div class="panel-heading">
                    <div>
                        <h5>Calendário financeiro</h5>
                        <small>Consulte os vencimentos por dia</small>
                    </div>
                    <i class="bi bi-calendar-month"></i>
                </div>

                <div class="calendar-legend mb-3">
                    <span><i class="legend-dot bg-danger"></i> Vencidas</span>
                    <span><i class="legend-dot bg-warning"></i> Pendentes</span>
                    <span><i class="legend-dot bg-success"></i> Pagas</span>
                </div>

                <div id="calendario"></div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="dash-panel h-100">
                <div class="panel-heading">
                    <div>
                        <h5>Parcelas do dia</h5>
                        <small>Selecione uma data no calendário</small>
                    </div>
                    <span class="selected-date" id="dataSelecionada">
                        {{ now()->format('d/m/Y') }}
                    </span>
                </div>

                <div class="daily-summary">
                    <div>
                        <small>Quantidade</small>
                        <strong id="quantidadeParcelas">0</strong>
                    </div>
                    <div>
                        <small>Valor previsto</small>
                        <strong id="valorParcelas">R$ 0,00</strong>
                    </div>
                </div>

                <div id="listaParcelas" class="daily-list">
                    <div class="empty-state">
                        <i class="bi bi-calendar-check"></i>
                        <p>Selecione uma data para consultar as parcelas.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- RANKING --}}
    <div class="row g-3">
        <div class="col-lg-6">
            <div class="dash-panel">
                <div class="panel-heading">
                    <div>
                        <h5>Ranking de clientes</h5>
                        <small>Clientes com maior lucro realizado</small>
                    </div>
                    <i class="bi bi-trophy"></i>
                </div>

                <div class="ranking-list">
                    @forelse($rankingClientes as $i => $cliente)
                        <div class="ranking-item">
                            <div class="ranking-position">
                                {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                            </div>

                            <div class="ranking-name">
                                <strong>{{ $cliente->nome }}</strong>
                                <small>{{ $i === 0 ? 'Maior resultado' : 'Cliente' }}</small>
                            </div>

                            <div class="ranking-value">
                                R$ {{ number_format($cliente->lucro, 2, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="bi bi-people"></i>
                            <p>Nenhum cliente com parcelas pagas.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
.dashboard {
    color: #e8edf5;
}

.dash-card,
.dash-panel {
    background: #171d29;
    border: 1px solid #293244;
    border-radius: 13px;
}

.dash-card {
    min-height: 155px;
    padding: 17px;
    transition: .2s;
}

.dash-card:hover {
    border-color: #3986c9;
    transform: translateY(-2px);
}

.dash-card small {
    display: block;
    color: #9ba8bb;
    font-size: 12px;
    margin-bottom: 8px;
}

.dash-card h3,
.dash-card h5 {
    color: #f4f7fb;
    font-weight: 700;
    margin: 0 0 7px;
    overflow-wrap: anywhere;
}

.dash-card h3 {
    font-size: 27px;
}

.dash-card h5 {
    font-size: 17px;
    margin-top: 12px;
}

.dash-card span {
    font-size: 11px;
    color: #77869b;
}

.dash-icon {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    margin-bottom: 13px;
    font-size: 17px;
}

.dash-icon.blue { background: #173c65; color: #60a5fa; }
.dash-icon.purple { background: #39295e; color: #b69aff; }
.dash-icon.red { background: #57272f; color: #ff8795; }
.dash-icon.yellow { background: #55451f; color: #ffd36a; }
.dash-icon.cyan { background: #164653; color: #5bd5ec; }
.dash-icon.green { background: #174b39; color: #5ee0a5; }

.dash-panel {
    padding: 20px;
}

.panel-heading {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}

.panel-heading h5 {
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 5px;
    color: #f4f7fb;
}

.panel-heading small {
    color: #8290a5;
    font-size: 12px;
}

.panel-heading > i {
    font-size: 21px;
    color: #4da3e6;
}

.chart-area {
    height: 235px;
    position: relative;
}

.calendar-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    font-size: 11px;
    color: #aab5c5;
}

.legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 4px;
}

.calendar-weekdays,
.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
    gap: 7px;
}

.calendar-weekdays {
    margin-bottom: 8px;
    color: #8492a7;
    text-align: center;
    font-size: 11px;
}

.calendar-day {
    min-height: 39px;
    border: 1px solid #303b4e;
    background: #202939;
    color: #e4eaf2;
    border-radius: 8px;
    font-size: 12px;
    transition: .15s;
}

.calendar-day:hover {
    border-color: #65b6f1;
    filter: brightness(1.2);
}

.calendar-day.has-data {
    font-weight: 700;
    color: #fff;
}

.calendar-day.selected {
    outline: 2px solid #60b5f3;
    outline-offset: 1px;
}

.calendar-day.today {
    border-color: #60b5f3;
}

.calendar-day.empty {
    visibility: hidden;
}

.calendar-nav {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}

.calendar-nav strong {
    text-transform: capitalize;
    font-size: 14px;
}

.calendar-nav button {
    border: 1px solid #3a4659;
    color: #dce5f0;
    background: #222d3e;
    border-radius: 7px;
    width: 32px;
    height: 30px;
}

.selected-date {
    font-size: 12px;
    color: #70baf2;
    background: #173650;
    padding: 7px 10px;
    border-radius: 7px;
}

.daily-summary {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 16px;
}

.daily-summary > div {
    background: #202939;
    border: 1px solid #303b4e;
    border-radius: 9px;
    padding: 12px;
}

.daily-summary small,
.daily-summary strong {
    display: block;
}

.daily-summary small {
    color: #8e9db1;
    font-size: 11px;
    margin-bottom: 5px;
}

.daily-summary strong {
    color: #f4f7fb;
    font-size: 16px;
}

.daily-list {
    max-height: 330px;
    overflow-y: auto;
}

.daily-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 13px;
    margin-bottom: 8px;
    border-radius: 9px;
    background: #202939;
    border: 1px solid #303b4e;
    cursor: pointer;
    transition: .15s;
}

.daily-item:hover {
    border-color: #3986c9;
    background: #253348;
}

.daily-item strong {
    color: #f1f5f9;
    font-size: 13px;
}

.daily-item small {
    display: block;
    color: #8d9cb0;
    font-size: 11px;
    margin-top: 4px;
}

.daily-value {
    text-align: right;
    white-space: nowrap;
}

.daily-value strong {
    color: #70c5f5;
}

.status-label {
    font-size: 10px;
    font-weight: 700;
    display: inline-block;
    margin-top: 5px;
}

.status-paid { color: #5ee0a5; }
.status-pending { color: #ffd36a; }
.status-overdue { color: #ff8795; }

.empty-state {
    text-align: center;
    padding: 35px 10px;
    color: #7f8da1;
}

.empty-state i {
    font-size: 28px;
    display: block;
    margin-bottom: 10px;
}

.empty-state p {
    font-size: 12px;
    margin: 0;
}

.ranking-item {
    display: flex;
    align-items: center;
    gap: 13px;
    padding: 12px 5px;
    border-bottom: 1px solid #2b3546;
}

.ranking-item:last-child {
    border-bottom: 0;
}

.ranking-position {
    width: 35px;
    height: 35px;
    border-radius: 9px;
    background: #233b56;
    color: #72bafa;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 12px;
}

.ranking-name {
    flex: 1;
    min-width: 0;
}

.ranking-name strong,
.ranking-name small {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.ranking-name strong {
    font-size: 13px;
    color: #edf2f8;
}

.ranking-name small {
    color: #8290a5;
    font-size: 10px;
    margin-top: 3px;
}

.ranking-value {
    color: #5ee0a5;
    font-size: 13px;
    font-weight: 700;
    white-space: nowrap;
}

@media (max-width: 576px) {
    .dash-card {
        min-height: 140px;
        padding: 12px;
    }

    .dash-card h3 {
        font-size: 23px;
    }

    .dash-card h5 {
        font-size: 14px;
    }

    .dash-panel {
        padding: 15px;
    }

    .calendar-day {
        min-height: 34px;
        padding: 5px 0;
    }

    .daily-item {
        padding: 10px;
    }

    .ranking-value {
        font-size: 11px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const parcelasVencidas = @json($parcelasVencidas);
const parcelasAVencer = @json($parcelasAVencer);
const parcelasPagas = @json($parcelasPagas);
const lucroObtido = Number(@json($lucroObtido));
const lucroPrevisto = Number(@json($previsaoLucro));

const dinheiro = valor => Number(valor).toLocaleString('pt-BR', {
    style: 'currency',
    currency: 'BRL'
});

new Chart(document.getElementById('graficoParcelas'), {
    type: 'bar',
    data: {
        labels: ['Parcelas'],
        datasets: [
            { label: 'Vencidas', data: [parcelasVencidas], backgroundColor: '#dc5365', borderRadius: 6 },
            { label: 'A vencer', data: [parcelasAVencer], backgroundColor: '#e8b94e', borderRadius: 6 },
            { label: 'Pagas', data: [parcelasPagas], backgroundColor: '#35b982', borderRadius: 6 }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: { color: '#dce5f0', usePointStyle: true, boxWidth: 8 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { color: '#9ba9bb', precision: 0 },
                grid: { color: 'rgba(255,255,255,.07)' }
            },
            x: {
                ticks: { color: '#9ba9bb' },
                grid: { display: false }
            }
        }
    }
});

new Chart(document.getElementById('graficoLucro'), {
    type: 'bar',
    data: {
        labels: ['Resultado'],
        datasets: [
            { label: 'Lucro obtido', data: [lucroObtido], backgroundColor: '#35b982', borderRadius: 6 },
            { label: 'Lucro previsto', data: [lucroPrevisto], backgroundColor: '#36a9d6', borderRadius: 6 }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: { color: '#dce5f0', usePointStyle: true, boxWidth: 8 }
            },
            tooltip: {
                callbacks: {
                    label: context => context.dataset.label + ': ' + dinheiro(context.parsed.y)
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#9ba9bb',
                    callback: valor => 'R$ ' + Number(valor).toLocaleString('pt-BR')
                },
                grid: { color: 'rgba(255,255,255,.07)' }
            },
            x: {
                ticks: { color: '#9ba9bb' },
                grid: { display: false }
            }
        }
    }
});

// CALENDÁRIO
const parcelas = @json($parcelasCalendario);
const dados = {};

Object.keys(parcelas).forEach(data => {
    dados[data] = parcelas[data];
});

let dataSelecionada = null;

function chaveData(ano, mes, dia) {
    return `${ano}-${String(mes + 1).padStart(2, '0')}-${String(dia).padStart(2, '0')}`;
}

function hojeLocal() {
    const hoje = new Date();
    return chaveData(hoje.getFullYear(), hoje.getMonth(), hoje.getDate());
}

function renderCalendario(data = new Date()) {
    const cal = document.getElementById('calendario');
    const ano = data.getFullYear();
    const mes = data.getMonth();
    const primeiro = new Date(ano, mes, 1).getDay();
    const ultimo = new Date(ano, mes + 1, 0).getDate();
    const hoje = hojeLocal();

    let html = `
        <div class="calendar-nav">
            <button onclick="renderCalendario(new Date(${ano},${mes - 1},1))">‹</button>
            <strong>${data.toLocaleDateString('pt-BR', {month: 'long', year: 'numeric'})}</strong>
            <button onclick="renderCalendario(new Date(${ano},${mes + 1},1))">›</button>
        </div>
        <div class="calendar-weekdays">
            ${['D','S','T','Q','Q','S','S'].map(d => `<div>${d}</div>`).join('')}
        </div>
        <div class="calendar-days">
    `;

    for (let i = 0; i < primeiro; i++) {
        html += '<div class="calendar-day empty"></div>';
    }

    for (let dia = 1; dia <= ultimo; dia++) {
        const chave = chaveData(ano, mes, dia);
        const lista = dados[chave] || [];
        let classe = '';

        if (lista.length) {
            const todasPagas = lista.every(p => Number(p.status) === 1 || p.status === true);
            const vencida = chave < hoje && !todasPagas;
            classe = todasPagas ? 'bg-success' : (vencida ? 'bg-danger' : 'bg-warning');
        }

        const selecionada = dataSelecionada === chave ? 'selected' : '';
        const hojeClasse = chave === hoje ? 'today' : '';

        html += `
            <button type="button"
                onclick="selecionarData('${chave}')"
                class="calendar-day ${lista.length ? 'has-data ' + classe : ''} ${selecionada} ${hojeClasse}">
                ${dia}
            </button>
        `;
    }

    html += '</div>';
    cal.innerHTML = html;
}

function selecionarData(data) {
    dataSelecionada = data;
    const partes = data.split('-');
    const dataFormatada = `${partes[2]}/${partes[1]}/${partes[0]}`;

    document.getElementById('dataSelecionada').innerText = dataFormatada;

    const lista = document.getElementById('listaParcelas');
    const parcelasDia = dados[data] || [];

    lista.innerHTML = '';

    let total = 0;

    parcelasDia.forEach(p => {
        total += Number(p.valor);

const totalPago = Number(p.total_pago || 0);
const valorParcela = Number(p.valor || 0);

const paga = p.status === true ||
    Number(p.status) === 1 ||
    totalPago >= valorParcela;        const vencida = data < hojeLocal() && !paga;

        const status = paga
            ? '<span class="status-label status-paid">PAGA</span>'
            : (vencida
                ? '<span class="status-label status-overdue">VENCIDA</span>'
                : '<span class="status-label status-pending">PENDENTE</span>');

        const item = document.createElement('div');
        item.className = 'daily-item';

        const informacoes = document.createElement('div');
        const cliente = document.createElement('strong');
        cliente.textContent = p.cliente || 'Cliente';
        const detalhe = document.createElement('small');
        detalhe.textContent = `Contrato #${p.contrato_id} | Parcela ${p.numero_parcela ?? '-'}`;
        informacoes.append(cliente, detalhe);

        const valor = document.createElement('div');
        valor.className = 'daily-value';

        const valorTexto = document.createElement('strong');
        valorTexto.textContent = dinheiro(p.valor);

        const statusTexto = document.createElement('div');
        statusTexto.innerHTML = status;

        valor.append(valorTexto, statusTexto);
        item.append(informacoes, valor);

        item.addEventListener('click', () => {
            window.location.href = `/contratos/${p.contrato_id}`;
        });

        lista.appendChild(item);
    });

    document.getElementById('quantidadeParcelas').textContent = parcelasDia.length;
    document.getElementById('valorParcelas').textContent = dinheiro(total);

    if (!parcelasDia.length) {
        lista.innerHTML = `
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <p>Nenhuma parcela nesta data.</p>
            </div>
        `;
    }

    renderCalendario(new Date(
        Number(partes[0]),
        Number(partes[1]) - 1,
        1
    ));
}

renderCalendario();

const hoje = hojeLocal();
selecionarData(hoje);
</script>
@endpush
