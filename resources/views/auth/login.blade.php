<x-guest-layout>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    
    {{-- Header with Logo Pemkab Blitar --}}
    <div class="text-center mb-8">
        <div class="flex items-center justify-center mb-4">
            <img src="{{ asset('images/logo-kab-blitar.png') }}" alt="Logo Kabupaten Blitar" class="h-20 w-auto object-contain">
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Beasiswa Blitar Mengabdi</h1>
        <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1.5 leading-relaxed">
            Sistem Informasi Manajemen Beasiswa Terpadu Kab. Blitar
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ showPassword: false }">
        @csrf

        {{-- Username Field --}}
        <div>
            <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <i data-lucide="user" class="w-4 h-4 text-slate-500"></i> Username
            </label>
            <div class="relative">
                <input id="username" type="text" name="username" :value="old('username')" required autofocus
                    placeholder="Masukkan username"
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm font-medium text-slate-900 focus:outline-none focus:border-[#2B5C92] focus:ring-2 focus:ring-[#2B5C92]/20 transition-all shadow-2xs">
            </div>
            @error('username')
                <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password Field --}}
        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                <i data-lucide="lock" class="w-4 h-4 text-slate-500"></i> Password
            </label>
            <div class="relative">
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                    placeholder="Masukkan password"
                    class="w-full px-4 py-3 pr-11 rounded-xl border border-slate-300 text-sm font-medium text-slate-900 focus:outline-none focus:border-[#2B5C92] focus:ring-2 focus:ring-[#2B5C92]/20 transition-all shadow-2xs">
                
                <button type="button" @click="showPassword = !showPassword"
                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors p-1"
                    tabindex="-1">
                    <template x-if="!showPassword">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </template>
                    <template x-if="showPassword">
                        <i data-lucide="eye-off" class="w-4 h-4"></i>
                    </template>
                </button>
            </div>
            @error('password')
                <p class="text-xs text-red-500 font-semibold mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        {{-- Turnstile --}}
        @if(env('TURNSTILE_SITE_KEY'))
        <div class="flex justify-center pt-1">
            <div class="cf-turnstile" data-sitekey="{{ env('TURNSTILE_SITE_KEY') }}" data-theme="light"></div>
        </div>
        @error('cf-turnstile-response')
            <p class="text-xs text-red-500 text-center font-semibold">{{ $message }}</p>
        @enderror
        @endif

        {{-- Submit Button --}}
        <div class="pt-2">
            <button type="submit"
                class="w-full py-3.5 px-6 rounded-full font-bold text-sm text-white shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 active:scale-[0.99]"
                style="background-color: #2B5C92;">
                <i data-lucide="log-in" class="w-4 h-4"></i> Masuk
            </button>
        </div>
    </form>

    {{-- Footer Info & Back Link --}}
    <div class="mt-8 pt-6 border-t border-slate-100 text-center space-y-4">
        <p class="text-[11px] text-slate-400 leading-relaxed max-w-xs mx-auto">
            Situs ini dilindungi untuk keamanan data administrator Beasiswa Blitar Mengabdi.
        </p>

        <div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-primary transition-colors">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</x-guest-layout>
