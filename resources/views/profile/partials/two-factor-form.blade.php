<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Autenticación de dos factores (2FA)
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Agrega una capa adicional de seguridad a tu cuenta.
            Cuando esté habilitado, necesitarás un código de tu app de autenticación cada vez que inicies sesión.
        </p>
    </header>

    @if(session('success') && str_contains(session('success'), '2FA') || str_contains(session('success') ?? '', 'dos factores'))
    <div class="mt-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
        {{ session('success') }}
    </div>
    @endif

    @php $usuario = Auth::user(); @endphp

    @if($usuario->tieneDosFactores())
    {{-- 2FA activo --}}
    <div class="mt-5 flex items-center gap-3 p-4 rounded-xl bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="#16a34a">
            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
        </svg>
        <span class="text-sm font-semibold text-green-800 dark:text-green-300">
            2FA habilitado — tu cuenta está protegida
        </span>
    </div>

    <form method="POST" action="{{ route('two-factor.disable') }}" class="mt-5">
        @csrf @method('DELETE')

        <div>
            <x-input-label for="password_2fa" value="Confirma tu contraseña para desactivar" />
            <x-text-input id="password_2fa" name="password" type="password"
                          class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 mt-4">
            <x-danger-button>Desactivar 2FA</x-danger-button>
        </div>
    </form>

    @else
    {{-- 2FA inactivo --}}
    <div class="mt-5 flex items-center gap-3 p-4 rounded-xl bg-amber-50 border border-amber-200 dark:bg-amber-900/20 dark:border-amber-800">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="#d97706">
            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
        </svg>
        <span class="text-sm font-medium text-amber-800 dark:text-amber-300">
            2FA no habilitado — se recomienda activarlo para mayor seguridad
        </span>
    </div>

    <div class="mt-4">
        <a href="{{ route('two-factor.setup') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
            </svg>
            Configurar 2FA
        </a>
    </div>
    @endif
</section>
