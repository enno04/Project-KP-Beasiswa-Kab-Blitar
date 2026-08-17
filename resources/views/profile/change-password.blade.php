<x-layouts.admin :title="'Ubah Password'">
    <div class="max-w-2xl mx-auto space-y-6">
        {{-- Header Section --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Ubah Password</h1>
                <p class="text-sm text-slate-500 mt-1">Perbarui password akun Anda untuk menjaga keamanan sistem.</p>
            </div>
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-primary" style="background-color: rgba(43, 92, 146, 0.1);">
                <i data-lucide="key-round" class="w-6 h-6"></i>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="card p-6 sm:p-8 bg-white border border-slate-200/80 rounded-2xl shadow-sm">
            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Password Lama --}}
                <x-form-input 
                    label="Password Lama" 
                    name="current_password" 
                    type="password" 
                    required 
                    placeholder="Masukkan password lama Anda" 
                />

                {{-- Password Baru --}}
                <x-form-input 
                    label="Password Baru" 
                    name="password" 
                    type="password" 
                    required 
                    placeholder="Masukkan password baru" 
                    helper="Password minimal 8 karakter."
                />

                {{-- Konfirmasi Password Baru --}}
                <x-form-input 
                    label="Konfirmasi Password Baru" 
                    name="password_confirmation" 
                    type="password" 
                    required 
                    placeholder="Ulangi password baru" 
                />

                {{-- Action Buttons --}}
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                    <button type="submit" class="btn btn-primary flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-sm shadow-sm transition-all">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
