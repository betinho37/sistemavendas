
<aside class="m1r7-sidebar">
    <div class="m1r7-sidebar-brand">
        <span class="m1r7-brand-icon">
            <i class="fas fa-bolt"></i>
        </span>
        <div>
            <strong>m1r4ge7</strong>
            <small>GESTÃO FINANCEIRA</small>
        </div>
    </div>

    <div class="m1r7-sidebar-section">MENU PRINCIPAL</div>

    <ul class="nav flex-column m1r7-sidebar-nav">

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('fluxo-caixa.*') ? 'active' : '' }}"
               href="{{ route('fluxo-caixa.index') }}">
                <i class="fas fa-cash-register"></i>
                <span>Fluxo de Caixa</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('calculadora.*') ? 'active' : '' }}"
               href="{{ route('calculadora.index') }}">
                <i class="fas fa-calculator"></i>
                <span>Calculadora</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}"
               href="{{ route('clientes.index') }}">
                <i class="fas fa-users"></i>
                <span>Clientes</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('contratos.*') ? 'active' : '' }}"
               href="{{ route('contratos.index') }}">
                <i class="fas fa-file-contract"></i>
                <span>Contratos</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('parcelas.*') ? 'active' : '' }}"
               href="{{ route('parcelas.vencimentos') }}">
                <i class="fas fa-calendar-days"></i>
                <span>Vencimentos</span>
            </a>
        </li>

        @if(auth()->check() && auth()->user()->isAdmin())

            @php
                $solicitacoesPendentes = \App\Models\SolicitacaoPagamento::where('status', 'pendente')->count();
            @endphp

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.solicitacoes-pagamento.*') ? 'active' : '' }}"
                   href="{{ route('admin.solicitacoes-pagamento.index') }}">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <span>Pagamentos do Portal</span>

                    @if($solicitacoesPendentes > 0)
                        <span class="badge bg-danger ms-auto">
                            {{ $solicitacoesPendentes }}
                        </span>
                    @endif
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}"
                   href="{{ route('admin.usuarios.index') }}">
                    <i class="fas fa-user-gear"></i>
                    <span>Usuários</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}"
                   href="{{ route('auditoria.index') }}">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Auditoria</span>
                </a>
            </li>

        @endif

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('configuracoes.*') ? 'active' : '' }}"
               href="{{ route('configuracoes.index') }}">
                <i class="fas fa-gear"></i>
                <span>Configurações</span>
            </a>
        </li>

    </ul>

    <div class="m1r7-sidebar-footer">
        <div class="m1r7-status-dot"></div>
        <span>Sistema operacional</span>
    </div>
</aside>
