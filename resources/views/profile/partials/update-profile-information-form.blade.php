<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Información del perfil
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Actualiza tu información personal y correo electrónico.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Nombre --}}
        <div>
            <x-input-label for="nombre" value="Nombre(s)" />
            <x-text-input id="nombre" name="nombre" type="text"
                class="mt-1 block w-full"
                :value="old('nombre', $user->nombre)"
                required autofocus autocomplete="given-name" />
            <x-input-error class="mt-2" :messages="$errors->get('nombre')" />
        </div>

        {{-- Apellido Paterno --}}
        <div>
            <x-input-label for="paterno" value="Apellido Paterno" />
            <x-text-input id="paterno" name="paterno" type="text"
                class="mt-1 block w-full"
                :value="old('paterno', $user->paterno)"
                required autocomplete="family-name" />
            <x-input-error class="mt-2" :messages="$errors->get('paterno')" />
        </div>

        {{-- Apellido Materno --}}
        <div>
            <x-input-label for="materno" value="Apellido Materno" />
            <x-text-input id="materno" name="materno" type="text"
                class="mt-1 block w-full"
                :value="old('materno', $user->materno)" />
            <x-input-error class="mt-2" :messages="$errors->get('materno')" />
        </div>

        {{-- Departamento --}}
        <div>
            <x-input-label for="departamento" value="Departamento" />
            <x-text-input id="departamento" name="departamento" type="text"
                class="mt-1 block w-full"
                :value="old('departamento', $user->departamento)" />
            <x-input-error class="mt-2" :messages="$errors->get('departamento')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1 block w-full"
                :value="old('email', $user->email)"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        Tu correo no está verificado.
                        <button form="send-verification"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Reenviar enlace de verificación.
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            Se envió un nuevo enlace a tu correo.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Guardar</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">
                    Guardado.
                </p>
            @endif
        </div>
    </form>
</section>