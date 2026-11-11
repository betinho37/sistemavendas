
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Painel') | m1r4ge7</title>

    <meta name="theme-color" content="#101010">

    {{-- Bootstrap --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Font Awesome --}}
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        rel="stylesheet"
    >

    {{-- Tema global m1r4ge7 --}}
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}?v=2">

    @stack('styles')
</head>

<body class="m1r7-body">

    {{-- Fundo de fechamento do menu no celular --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Cabeçalho global --}}
    <header class="m1r7-topbar">
        <div class="topbar-left">
            <button
                type="button"
                class="sidebar-toggle"
                id="sidebarToggle"
                aria-label="Abrir menu"
                aria-controls="m1r7Sidebar"
                aria-expanded="false"
            >
                <i class="fas fa-bars"></i>
            </button>

            <a href="{{ route('dashboard') }}" class="brand-link">
                <span class="brand-symbol">
                    <i class="fas fa-bolt"></i>
                </span>

                <span class="brand-name">
                    <strong>m1r4ge7</strong>
                    <small>GESTÃO FINANCEIRA</small>
                </span>
            </a>
        </div>

        <div class="topbar-right">
            <span class="system-status">
                <span class="status-dot"></span>
                Sistema
            </span>

            @auth
                <div class="user-info">
                    <span class="user-avatar">
                        <i class="fas fa-user"></i>
                    </span>

                    <span class="user-name">
                        {{ Auth::user()->name }}
                    </span>
                </div>
            @endauth
        </div>
    </header>

    {{-- Menu lateral --}}
    @include('layouts.sidebar')

    {{-- Conteúdo de cada página --}}
    <main class="m1r7-main">
        <div class="page-content">

            @if (session('success'))
                <div class="alert alert-success m1r7-alert" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger m1r7-alert" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')

        </div>
    </main>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('sidebarToggle');
            const overlay = document.getElementById('sidebarOverlay');

            function setSidebar(open) {
                document.body.classList.toggle('sidebar-open', open);

                if (toggle) {
                    toggle.setAttribute('aria-expanded', String(open));
                    toggle.setAttribute(
                        'aria-label',
                        open ? 'Fechar menu' : 'Abrir menu'
                    );
                }
            }

            if (toggle) {
                toggle.addEventListener('click', function () {
                    setSidebar(!document.body.classList.contains('sidebar-open'));
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function () {
                    setSidebar(false);
                });
            }

            document.querySelectorAll('#m1r7Sidebar a').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth <= 900) {
                        setSidebar(false);
                    }
                });
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 900) {
                    setSidebar(false);
                }
            });
        });
    </script>

    @stack('scripts')

</body>
</html>
