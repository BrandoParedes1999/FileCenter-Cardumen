<x-guest-layout>
    <div class="mb-6">
        <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver al inicio de sesión
        </a>
    </div>

    <div class="mb-10 text-left">
        <h2 class="text-4xl font-black text-gray-900 tracking-tight">Recuperar</h2>
        <p class="text-gray-500 mt-3 text-lg leading-relaxed">
            ¿Olvidaste tu contraseña? No hay problema. Dinos tu correo y te enviaremos un enlace para crear una nueva.
        </p>
    </div>

    <x-auth-session-status class="mb-6 font-medium text-sm text-green-600 bg-green-50 p-4 rounded-2xl border border-green-100 shadow-sm" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Correo electrónico</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-indigo-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <input id="email" 
                       class="block w-full pl-12 pr-4 py-4 bg-gray-50 border-gray-100 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 rounded-2xl transition-all duration-200 outline-none text-gray-900 shadow-sm" 
                       type="email" 
                       name="email" 
                       :value="old('email')" 
                       required 
                       autofocus 
                       placeholder="usuario@empresa.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-4 px-6 rounded-2xl transition duration-300 shadow-xl shadow-indigo-200 active:scale-[0.98]">
                Enviar enlace de recuperación
            </button>
        </div>
    </form>
</x-guest-layout>