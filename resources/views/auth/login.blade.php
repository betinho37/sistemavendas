
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#080d18">
    <title>Login | m1r4ge7</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background:
                radial-gradient(circle at 50% 0%, rgba(0, 178, 255, 0.13), transparent 40%),
                #080d18;
            font-family: Arial, sans-serif;
        }

        .login-card {
            background: linear-gradient(145deg, #151f30, #0e1624);
            border: 1px solid rgba(0, 178, 255, 0.18);
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.45);
        }

        .input-login {
            width: 100%;
            min-height: 52px;
            background: #0b1220;
            border: 1px solid #26364d;
            border-radius: 10px;
            padding: 14px 15px;
            color: white;
            outline: none;
            transition: .2s;
        }

        .input-login:focus {
            border-color: #00b2ff;
            box-shadow: 0 0 0 3px rgba(0, 178, 255, 0.12);
        }

        .btn-login {
            min-height: 52px;
            background: linear-gradient(90deg, #00b2ff, #0073e6);
            transition: .2s;
        }

        .btn-login:hover {
            filter: brightness(1.12);
            transform: translateY(-1px);
        }

        @media (max-width: 480px) {
            .login-card {
                border-radius: 16px;
            }
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center px-4 py-8 text-white">

    <main class="w-full max-w-md">

        <div class="login-card rounded-2xl p-6 sm:p-9">

            <!-- Logo -->
            <div class="text-center mb-8">

                <div class="inline-flex items-center justify-center mb-5">
                    <img
                        src="{{ asset('images/logo-acervotech.jpg') }}"
                        alt="AcervoTech"
                        class="w-36 sm:w-40 h-auto object-contain rounded-lg"
                    >
                </div>

                <h1 class="text-2xl font-bold tracking-wide">
                    m1r4ge7
                </h1>

                <p class="text-gray-400 text-sm mt-2">
                    Gestão avançada do seu capital
                </p>
            </div>

            <!-- Identificação -->
            <div class="mb-7">
                <div class="flex items-center gap-3 mb-2">
                    <div class="h-9 w-9 rounded-lg bg-sky-500/10 flex items-center justify-center text-sky-400">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="20" height="20" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>

                    <h2 class="text-lg font-semibold">
                        Acesso ao sistema
                    </h2>
                </div>

                <p class="text-sm text-gray-400">
                    Informe seus dados para continuar.
                </p>
            </div>

            <!-- Mensagens -->
            @if (session('status'))
                <div class="mb-5 p-3 rounded-lg bg-green-500/10 border border-green-500/30 text-green-300 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 p-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Formulário -->
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                        E-mail
                    </label>

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

                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                        Senha
                    </label>

                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="input-login pr-14"
                            placeholder="Digite sua senha"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-sky-400 p-2"
                            aria-label="Mostrar ou ocultar senha"
                        >
                            <svg id="eyeIcon"
                                 xmlns="http://www.w3.org/2000/svg"
                                 width="21" height="21"
                                 viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor"
                                 stroke-width="2"
                                 stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Lembrar e recuperar senha -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-7">

                    <label for="remember" class="flex items-center gap-2 text-sm text-gray-400 cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            value="1"
                            class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-sky-500 focus:ring-sky-500"
                        >
                        Lembrar de mim
                    </label>

                    <a
                        href="{{ route('password.request') }}"
                        class="text-sm text-sky-400 hover:text-sky-300 transition"
                    >
                        Esqueceu sua senha?
                    </a>

                </div>

                <!-- Entrar -->
                <button
                    type="submit"
                    class="btn-login w-full rounded-lg text-black font-bold flex items-center justify-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg"
                         width="20" height="20"
                         viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="2"
                         stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>

                    Entrar no sistema
                </button>

            </form>

            <!-- Rodapé -->
            <div class="mt-8 pt-5 border-t border-gray-700/60 text-center">

                <p class="text-xs text-gray-500">
                    <span class="text-sky-400">●</span>
                    Acesso restrito a usuários autorizados.
                </p>

                <p class="text-xs text-gray-600 mt-3">
                    m1r4ge7 &copy; {{ date('Y') }}
                </p>

            </div>

        </div>

    </main>

    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');

            if (password.type === 'password') {
                password.type = 'text';
                icon.innerHTML = `
                    <path d="M3 3l18 18"/>
                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                    <path d="M9.9 5.2A10.5 10.5 0 0 1 12 5c6.4 0 10 7 10 7a15.8 15.8 0 0 1-3 3.8"/>
                    <path d="M6.2 6.2C3.5 8 2 12 2 12s3.6 7 10 7a10 10 0 0 0 4-.8"/>
                `;
            } else {
                password.type = 'password';
                icon.innerHTML = `
                    <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/>
                    <circle cx="12" cy="12" r="3"/>
                `;
            }
        }
    </script>

</body>
</html>
