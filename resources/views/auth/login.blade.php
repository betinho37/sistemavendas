
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#070b14">
    <title>Login | m1r4ge7</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            color-scheme: dark;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: #070b14;
            color: #f8fafc;
            font-family: Inter, "Segoe UI", Arial, sans-serif;
            isolation: isolate;
            overflow-x: hidden;
        }

        /* IMAGEM DE FUNDO */
        body::before {
            content: "";
            position: fixed;
            inset: -15px;
            z-index: -3;
            background:
                url("{{ asset('images/logo-acervotech.jpg') }}")
                center center / cover no-repeat;
            opacity: .34;
            filter: blur(3px) saturate(.75);
            transform: scale(1.04);
        }

        /* CAMADA DE CONTRASTE */
        body::after {
            content: "";
            position: fixed;
            inset: 0;
            z-index: -2;
            background:
                radial-gradient(
                    ellipse at 50% 35%,
                    rgba(0, 145, 255, .18),
                    transparent 48%
                ),
                linear-gradient(
                    115deg,
                    rgba(3, 8, 18, .91),
                    rgba(5, 15, 30, .76),
                    rgba(2, 7, 17, .92)
                );
        }

        /* ILUMINAÇÃO AMBIENTE */
        .ambient-light {
            position: fixed;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: rgba(0, 145, 255, .10);
            filter: blur(100px);
            pointer-events: none;
            z-index: -1;
        }

        .ambient-one {
            top: -180px;
            left: -150px;
        }

        .ambient-two {
            bottom: -220px;
            right: -150px;
            background: rgba(0, 220, 190, .07);
        }

        /* LAYOUT */
        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 35px 18px;
        }

        .login-wrapper {
            width: 100%;
            max-width: 430px;
            animation: appear .7s ease both;
        }

        /* CARD */
        .login-card {
            position: relative;
            overflow: hidden;
            padding: 38px;
            border-radius: 24px;
            background: linear-gradient(
                145deg,
                rgba(15, 27, 46, .88),
                rgba(7, 16, 30, .84)
            );
            border: 1px solid rgba(125, 190, 255, .18);
            box-shadow:
                0 35px 100px rgba(0, 0, 0, .58),
                0 0 60px rgba(0, 145, 255, .07),
                inset 0 1px rgba(255, 255, 255, .06);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
        }

        .login-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 12%;
            width: 76%;
            height: 1px;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(56, 189, 248, .8),
                transparent
            );
        }

        /* LOGO - RESPONSIVA E AMPLIADA */
        .brand-logo {
            position: relative;
            isolation: isolate;
            width: 100%;
            height: clamp(130px, 24vw, 185px);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: -12px auto 15px;
            overflow: visible;
        }

        .brand-logo::before {
            content: "";
            position: absolute;
            width: min(90%, 330px);
            height: 90%;
            border-radius: 48% 52% 35% 65% / 55% 38% 62% 45%;
            background: linear-gradient(
                135deg,
                rgba(0, 178, 255, .28),
                rgba(0, 220, 190, .13)
            );
            filter: blur(32px);
            z-index: -1;
        }

        .brand-logo::after {
            content: "";
            position: absolute;
            width: min(85%, 300px);
            height: 82%;
            border: 1px solid rgba(56, 189, 248, .22);
            border-radius: 45% 55% 60% 40% / 50% 40% 60% 50%;
            transform: rotate(-7deg);
            z-index: -1;
        }

        .brand-logo img {
            display: block;
            width: min(310px, 94%);
            height: 100%;
            max-width: 100%;
            max-height: none;
            object-fit: contain;
            border-radius: 28px 65px 28px 65px;
            filter:
                drop-shadow(0 12px 25px rgba(0, 0, 0, .40))
                drop-shadow(0 0 22px rgba(0, 178, 255, .20));
        }

        /* TABLETS */
        @media (max-width: 768px) {
            .brand-logo {
                height: clamp(130px, 25vw, 165px);
            }

            .brand-logo img {
                width: min(285px, 94%);
            }
        }

        /* CELULARES */
        @media (max-width: 480px) {
            .page {
                padding: 18px 13px;
            }

            .login-card {
                padding: 29px 22px;
                border-radius: 20px;
            }

            .brand-logo {
                height: 145px;
                margin: -5px auto 12px;
            }

            .brand-logo img {
                width: 94%;
                max-width: 270px;
            }

            .brand-name {
                font-size: 27px;
            }

            .section-heading {
                margin-top: 27px;
            }

            .form-options {
                align-items: flex-start;
            }
        }

        /* CELULARES PEQUENOS */
        @media (max-width: 360px) {
            .page {
                padding: 14px 10px;
            }

            .login-card {
                padding: 25px 17px;
            }

            .brand-logo {
                height: 125px;
            }

            .brand-logo img {
                width: 94%;
                max-width: 235px;
            }

            .form-options {
                flex-direction: column;
            }
        }

        .brand-name {
            font-size: 30px;
            font-weight: 800;
            letter-spacing: 3px;
            text-align: center;
            line-height: 1.2;
        }

        .brand-subtitle {
            margin-top: 9px;
            color: #94a3b8;
            font-size: 13px;
            text-align: center;
            letter-spacing: .4px;
        }

        .section-heading {
            margin-top: 32px;
            margin-bottom: 25px;
        }

        .section-heading h2 {
            font-size: 19px;
            font-weight: 700;
            color: #f1f5f9;
        }

        .section-heading p {
            margin-top: 6px;
            color: #94a3b8;
            font-size: 13px;
        }

        /* CAMPOS */
        .field {
            margin-bottom: 21px;
        }

        .field label {
            display: block;
            margin-bottom: 9px;
            color: #dbeafe;
            font-size: 13px;
            font-weight: 600;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
        }

        .input-login {
            width: 100%;
            min-height: 54px;
            padding: 14px 15px 14px 45px;
            border-radius: 12px;
            border: 1px solid rgba(148, 163, 184, .19);
            outline: none;
            color: #f8fafc;
            background: rgba(3, 10, 21, .58);
            font-size: 14px;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }

        .input-login::placeholder {
            color: #64748b;
        }

        .input-login:focus {
            border-color: rgba(14, 165, 233, .85);
            background: rgba(3, 10, 21, .85);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, .10);
        }

        .password-input {
            padding-right: 50px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 13px;
            transform: translateY(-50%);
            padding: 8px;
            color: #94a3b8;
            background: transparent;
            border: 0;
            cursor: pointer;
            transition: color .2s;
        }

        .password-toggle:hover {
            color: #38bdf8;
        }

        /* OPÇÕES */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 4px 0 27px;
            flex-wrap: wrap;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #cbd5e1;
            font-size: 13px;
            cursor: pointer;
        }

        .remember-label input {
            width: 16px;
            height: 16px;
            accent-color: #0ea5e9;
            cursor: pointer;
        }

        .forgot-link {
            color: #38bdf8;
            font-size: 13px;
            text-decoration: none;
            transition: color .2s;
        }

        .forgot-link:hover {
            color: #7dd3fc;
            text-decoration: underline;
        }

        /* BOTÃO */
        .btn-login {
            position: relative;
            width: 100%;
            min-height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: 0;
            border-radius: 12px;
            color: #03111e;
            background: linear-gradient(
                105deg,
                #38bdf8,
                #0ea5e9 55%,
                #0284c7
            );
            box-shadow: 0 9px 28px rgba(14, 165, 233, .22);
            font-size: 14px;
            font-weight: 800;
            letter-spacing: .3px;
            cursor: pointer;
            transition: transform .2s, filter .2s, box-shadow .2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            filter: brightness(1.08);
            box-shadow: 0 13px 34px rgba(14, 165, 233, .32);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* ALERTAS */
        .alert {
            padding: 13px 14px;
            margin-bottom: 20px;
            border-radius: 11px;
            font-size: 13px;
            line-height: 1.5;
        }

        .alert-success {
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, .25);
            background: rgba(34, 197, 94, .09);
        }

        .alert-error {
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, .25);
            background: rgba(239, 68, 68, .09);
        }

        /* RODAPÉ */
        .security-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 27px;
            padding-top: 21px;
            border-top: 1px solid rgba(148, 163, 184, .12);
            color: #94a3b8;
            font-size: 11px;
            text-align: center;
        }

        .security-dot {
            flex: 0 0 7px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 10px rgba(34, 197, 94, .6);
        }

        .copyright {
            margin-top: 13px;
            color: #64748b;
            font-size: 11px;
            text-align: center;
        }

        /* ANIMAÇÃO */
        @keyframes appear {
            from {
                opacity: 0;
                transform: translateY(18px) scale(.985);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: .01ms !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>

    <div class="ambient-light ambient-one"></div>
    <div class="ambient-light ambient-two"></div>

    <main class="page">
        <div class="login-wrapper">

            <section class="login-card">

                <header>
                    <div class="brand-logo">
                        <img
                            src="{{ asset('images/logo-acervotech.jpg') }}"
                            alt="AcervoTech"
                        >
                    </div>

                    <h1 class="brand-name">m1r4ge7</h1>

                    <p class="brand-subtitle">
                        Gestão inteligente do seu capital
                    </p>
                </header>

                <div class="section-heading">
                    <h2>Bem-vindo de volta</h2>
                    <p>Entre com suas credenciais para acessar sua conta.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="field">
                        <label for="email">E-mail</label>

                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg width="19" height="19" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor"
                                     stroke-width="1.8" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                                    <path d="m3 7 9 6 9-6"/>
                                </svg>
                            </span>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                value="{{ old('email') }}"
                                class="input-login"
                                placeholder="seu@email.com"
                                autocomplete="username"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <div class="field">
                        <label for="password">Senha</label>

                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg width="19" height="19" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor"
                                     stroke-width="1.8" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </span>

                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="input-login password-input"
                                placeholder="Digite sua senha"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                onclick="togglePassword()"
                                class="password-toggle"
                                aria-label="Mostrar ou ocultar senha"
                                aria-pressed="false"
                                id="passwordToggle"
                            >
                                <svg id="eyeIcon" width="20" height="20"
                                     viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label for="remember" class="remember-label">
                            <input
                                type="checkbox"
                                name="remember"
                                id="remember"
                                value="1"
                            >
                            Lembrar de mim
                        </label>

                        <a
                            href="{{ route('password.request') }}"
                            class="forgot-link"
                        >
                            Esqueceu sua senha?
                        </a>
                    </div>

                    <button type="submit" class="btn-login">
                        <svg width="19" height="19" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>

                        Acessar minha conta
                    </button>

                </form>

                <footer>
                    <div class="security-info">
                        <span class="security-dot"></span>
                        Acesso seguro e restrito a usuários autorizados
                    </div>

                    <p class="copyright">
                        m1r4ge7 &copy; {{ date('Y') }}. Todos os direitos reservados.
                    </p>
                </footer>

            </section>

        </div>
    </main>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            const button = document.getElementById('passwordToggle');

            const showing = password.type === 'password';

            password.type = showing ? 'text' : 'password';
            button.setAttribute('aria-pressed', showing ? 'true' : 'false');

            if (showing) {
                icon.innerHTML = `
                    <path d="M3 3l18 18"/>
                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                    <path d="M9.9 5.2A10.5 10.5 0 0 1 12 5c6.4 0 10 7 10 7a15.8 15.8 0 0 1-3 3.8"/>
                    <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 4-.8"/>
                `;
            } else {
                icon.innerHTML = `
                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/>
                    <circle cx="12" cy="12" r="3"/>
                `;
            }
        }
    </script>

</body>
</html>
