
<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Recuperar senha
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Esqueceu sua senha? Informe seu e-mail abaixo. Enviaremos um link para você cadastrar uma nova senha.
        </p>
    </div>

    <x-auth-session-status
        class="mb-4"
        :status="session('status')"
    />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="E-mail" />

            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="Digite seu e-mail"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>

        <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
            <a
                href="{{ route('login') }}"
                class="inline-flex items-center justify-center px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
            >
                Voltar ao login
            </a>

            <x-primary-button class="w-full sm:w-auto justify-center">
                Enviar link de recuperação
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
