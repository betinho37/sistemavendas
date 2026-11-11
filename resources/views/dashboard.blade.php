@extends('layouts.app')

@section('title','Dashboard')

@section('content')
<div class="container-fluid mt-4">

    {{-- ===================== --}}
    {{-- MÉTRICAS --}}
    {{-- ===================== --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card bg-dark text-light text-center p-3">
                <small>Clientes</small>
                <h4>{{ $clientes }}</h4>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-dark text-light text-center p-3">
                <small>Contratos</small>
                <h4>{{ $contratos }}</h4>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-dark text-light text-center p-3">
                <small>Parcelas vencidas</small>
                <h4 class="text-danger">{{ $parcelasVencidas }}</h4>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-dark text-light text-center p-3">
                <small>Parcelas a vencer</small>
                <h4 class="text-warning">{{ $parcelasAVencer }}</h4>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-dark text-light text-center p-3">
                <small>Capital investido</small>
                <h6>R$ {{ number_format($capitalInvestido,2,',','.') }}</h6>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card bg-dark text-light text-center p-3">
                <small>Lucro previsto</small>
                <h6 class="text-success">
                    R$ {{ number_format($previsaoLucro,2,',','.') }}
                </h6>
            </div>
        </div>
    </div>

    {{-- ===================== --}}
    {{-- GRÁFICOS --}}
    {{-- ===================== --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card bg-dark text-light h-100">
                <div class="card-header text-center">Parcelas</div>
                <div class="card-body" style="height:220px">
                    <canvas id="graficoParcelas"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-dark text-light h-100">
                <div class="card-header text-center">Lucro</div>
                <div class="card-body" style="height:220px">
                    <canvas id="graficoLucro"></canvas>
                </div>
            </div>
        </div>
    </div>


    {{-- ===================== --}}
    {{-- CALENDÁRIO + LISTA --}}
    {{-- ===================== --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card bg-dark text-light h-100">
                <div class="card-header text-center fw-bold">
                    Calendário de parcelas
                </div>
                <div class="card-body">
                    <div id="calendario"></div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-dark text-light h-100">
                <div class="card-header text-center">
                    Exibindo:
                    <strong id="dataSelecionada">
                        {{ now()->format('d/m/Y') }}
                    </strong>
                </div>

                <div class="card-body">
                    <h5 class="text-center" id="infoParcelas">
                        Parcelas: 0 | Valor previsto: R$ 0,00
                    </h5>

                    <hr>

                    <div id="listaParcelas" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== --}}
    {{-- Ranking de Clientes --}}
    {{-- ===================== --}}

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card bg-dark text-light h-100">
                <div class="card-header text-center fw-bold">
                    🏆 Ranking de Clientes
                </div>

                <div class="card-body">
                    @forelse($rankingClientes as $i => $cliente)
                        <div class="d-flex justify-content-between align-items-center
                                    bg-secondary bg-opacity-25 p-2 rounded mb-2">

                            <div>
                                <strong>
                                    {{ $i+1 }}º - {{ $cliente->nome }}
                                </strong>
                            </div>

                            <div class="text-success fw-bold">
                                R$ {{ number_format($cliente->lucro, 2, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <span class="text-muted">
                            Nenhum cliente com parcelas pagas
                        </span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>



</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/* ===============================
   GRÁFICOS
================================ */

new Chart(document.getElementById('graficoParcelas'), {
    type: 'bar',
    data: {
        labels: ['Parcelas'],
        datasets: [
            {
                label: 'Vencidas',
                data: [{{ $parcelasVencidas }}],
                backgroundColor: '#dc3545', // 🔴 vermelho
                borderRadius: 6
            },
            {
                label: 'A vencer',
                data: [{{ $parcelasAVencer }}],
                backgroundColor: '#ffc107', // 🟡 amarelo
                borderRadius: 6
            },
            {
                label: 'Pagas',
                data: [{{ $parcelasPagas }}],
                backgroundColor: '#198754', // 🟢 verde
                borderRadius: 6
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: '#ffffff',
                    font: { weight: 'bold' },
                    usePointStyle: true,
                    pointStyle: 'rectRounded'
                }
            },
            tooltip: {
                callbacks: {
                    label: ctx =>
                        ctx.dataset.label + ': ' + ctx.parsed.y
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#ffffff',
                    precision: 0
                },
                grid: {
                    color: 'rgba(255,255,255,0.1)'
                }
            },
            x: {
                ticks: { color: '#ffffff' },
                grid: { display: false }
            }
        }
    }
});





const lucroObtido  = Number({{ $lucroObtido }});
const lucroPrevisto = Number({{ $previsaoLucro }});
new Chart(document.getElementById('graficoLucro'), {
    type: 'bar',
    data: {
        labels: ['Lucro'],
        datasets: [
            {
                label: 'Lucro obtido',
                data: [lucroObtido],
                backgroundColor: '#198754',
                borderRadius: 6
            },
            {
                label: 'Lucro previsto',
                data: [lucroPrevisto],
                backgroundColor: '#0dcaf0',
                borderRadius: 6
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: '#ffffff',
                    font: { weight: 'bold' }
                }
            },
            tooltip: {
                callbacks: {
                    label: ctx =>
                        ctx.dataset.label + ': R$ ' +
                        ctx.parsed.y.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2
                        })
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#ffffff',
                    callback: v => 'R$ ' + v.toLocaleString('pt-BR')
                },
                grid: {
                    color: 'rgba(255,255,255,0.1)'
                }
            },
            x: {
                ticks: { color: '#ffffff' },
                grid: { display: false }
            }
        }
    }
});


/* ===============================
   CALENDÁRIO
================================ */
const parcelas = @json($parcelasCalendario);
const dados = {};

Object.keys(parcelas).forEach(data => {
    dados[data] = parcelas[data];
});

function renderCalendario(data = new Date()){
    const cal = document.getElementById('calendario');
    cal.innerHTML = '';

    const ano = data.getFullYear();
    const mes = data.getMonth();
    const primeiro = new Date(ano, mes, 1).getDay();
    const ultimo = new Date(ano, mes + 1, 0).getDate();
    const hoje = new Date().toISOString().split('T')[0];

    let html = `
    <div class="d-flex justify-content-between mb-3">
        <button class="btn btn-sm btn-outline-light"
            onclick="renderCalendario(new Date(${ano},${mes}-1,1))">‹</button>

        <strong>${data.toLocaleDateString('pt-BR',{month:'long',year:'numeric'})}</strong>

        <button class="btn btn-sm btn-outline-light"
            onclick="renderCalendario(new Date(${ano},${mes}+1,1))">›</button>
    </div>

    <div class="d-grid" style="grid-template-columns:repeat(7,1fr);gap:6px;">`;

    ['D','S','T','Q','Q','S','S'].forEach(d =>
        html += `<small class="text-center">${d}</small>`
    );

    for(let i=0;i<primeiro;i++) html += `<div></div>`;

    for(let d=1; d<=ultimo; d++){
        const key = `${ano}-${String(mes+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        let classe = 'bg-secondary';

        if (dados[key]) {
            if (dados[key].some(p => p.status)) {
                classe = 'bg-success'; // 🟢 tem parcela paga
            } else {
                classe = key < hoje ? 'bg-danger' : 'bg-warning';
            }
        }


        html += `
        <div onclick="selecionarData('${key}')"
             class="p-2 rounded text-center ${classe}"
             style="cursor:pointer">
            ${d}
        </div>`;
    }

    html += '</div>';
    cal.innerHTML = html;
}

function selecionarData(data){
    document.getElementById('dataSelecionada').innerText =
        new Date(data).toLocaleDateString('pt-BR');

    const lista = document.getElementById('listaParcelas');
    lista.innerHTML = '';
    let total = 0;

    if(dados[data]){
        dados[data].forEach(p => {
            total += Number(p.valor);

            lista.innerHTML += `
            <div class="d-flex justify-content-between align-items-center
                        bg-secondary bg-opacity-25 p-3 rounded mb-2"
                 style="cursor:pointer"
                 onclick="window.location='/contratos/${p.contrato_id}'">

                <div>
                    <strong>${p.cliente}</strong><br>
                    <small>
                        Contrato #${p.contrato_id} |
                        Parcela ${p.numero_parcela}
                    </small>
                </div>

                <div class="text-end">
                    <strong>R$ ${Number(p.valor).toLocaleString('pt-BR')}</strong><br>
                    <button class="btn btn-sm btn-success"
                        onclick="event.stopPropagation(); pagarParcela(${p.id})">
                        Pagar
                    </button>
                </div>
            </div>`;
        });

        document.getElementById('infoParcelas').innerText =
            `Parcelas: ${dados[data].length} | Valor previsto: R$ ${total.toLocaleString('pt-BR')}`;
    } else {
        document.getElementById('infoParcelas').innerText =
            'Parcelas: 0 | Valor previsto: R$ 0,00';

        lista.innerHTML =
            '<span class="text-muted">Nenhuma parcela nesta data</span>';
    }
}
    function pagarParcela(id) {
        if (!confirm('Confirmar pagamento desta parcela?')) return;

        fetch(`/dashboard/pagar-parcela/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // ✅ ATUALIZA A PÁGINA
                location.reload();
            } else {
                alert('Erro ao pagar parcela');
            }
        })
        .catch(() => alert('Erro de comunicação'));
    }


renderCalendario();
</script>
@endpush
