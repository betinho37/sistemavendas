
<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Criar nova senha
        </h2>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            Cadastre uma nova senha para acessar sua conta.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <input
            type="hidden"
            name="token"
            value="{{ $request->route('token') }}"
        >

        <div>
            <x-input-label for="email" value="E-mail" />

            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email', $request->email)"
                required
                autofocus
                autocomplete="username"
                placeholder="Seu e-mail"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>

        <div>
            <x-input-label for="password" value="Nova senha" />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                placeholder="Digite sua nova senha"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirmar nova senha" />

            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Digite novamente sua nova senha"
            />

            <x-input-error
                :messages="$errors->get('password_confirmation')"
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
                Redefinir senha
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
