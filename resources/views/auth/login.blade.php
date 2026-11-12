
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | m1r4ge7</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-black text-white min-h-screen flex flex-col items-center justify-center">

    <div class="w-full max-w-md px-6 py-8 bg-[#111827] rounded-xl shadow-xl">

        <!-- Logo -->
        <div class="text-center mb-6">
            <img
                src="{{ asset('images/logo-acervotech.jpg') }}"
                alt="Logo"
                class="mx-auto w-40 h-auto"
            >

            <p class="text-sm text-gray-400 mt-2">
                Gestão avançada do seu capital
            </p>
        </div>

        <!-- Identificação -->
        <div class="flex justify-center border-b border-[#00B2FF] mb-6">
            <span class="text-[#00B2FF] border-b-2 border-[#00B2FF] px-4 pb-2 font-semibold">
                <i class="fas fa-lock"></i>
                Acesso ao sistema
            </span>
        </div>

        <!-- Formulário -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm mb-1">
                    E-mail
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    class="w-full px-4 py-2 rounded bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-[#00B2FF]"
                    required
                    autofocus
                >
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm mb-1">
                    Senha
                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    class="w-full px-4 py-2 rounded bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-[#00B2FF]"
                    required
                >
            </div>

            @if ($errors->has('email'))
                <div class="text-red-500 text-sm mb-4">
                    {{ $errors->first('email') }}
                </div>
            @endif

            <button
                type="submit"
                class="w-full bg-gradient-to-r from-[#00B2FF] to-[#0073E6] text-black font-bold py-2 rounded hover:opacity-90 transition"
            >
                <i class="fas fa-sign-in-alt mr-2"></i>
                Entrar
            </button>
        </form>

        <p class="text-center text-xs text-gray-500 mt-6">
            Acesso restrito a usuários autorizados.
        </p>

    </div>

</body>
</html>
