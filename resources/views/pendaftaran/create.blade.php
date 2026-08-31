<x-layouts.public :title="'Pendaftaran ' . $program->nama">
    @php
        $hasTambahan = $customFields->where('penempatan', 'tambahan')->count() > 0;
        $stepDokumen = $hasTambahan ? 5 : 4;
        $stepReview = $hasTambahan ? 6 : 5;
        $totalSteps = $stepReview;
    @endphp
    {{-- Emergency Banner (Dari Pengaturan Superadmin) --}}
    @if(isset($webSettings['emergency_banner_active']) && $webSettings['emergency_banner_active'] == '1' && !empty($webSettings['emergency_banner_text']))
        @php
            $bgColorClass = 'bg-amber-500';
            $textColorClass = 'text-amber-900';
            if (isset($webSettings['emergency_banner_color'])) {
                if ($webSettings['emergency_banner_color'] === 'red') {
                    $bgColorClass = 'bg-red-500';
                    $textColorClass = 'text-red-50';
                } elseif ($webSettings['emergency_banner_color'] === 'blue') {
                    $bgColorClass = 'bg-blue-600';
                    $textColorClass = 'text-blue-50';
                }
            }
        @endphp
        <div class="{{ $bgColorClass }} {{ $textColorClass }} font-semibold text-sm py-3 px-4 shadow-sm border-b border-black/10 relative z-40 w-full animate-fade-in-down">
            <div class="max-w-7xl mx-auto flex items-center justify-center gap-2 text-center">
                <i data-lucide="alert-triangle" class="w-5 h-5 shrink-0 {{ $bgColorClass === 'bg-amber-500' ? 'text-amber-800' : 'text-white' }}"></i>
                <span>{{ $webSettings['emergency_banner_text'] }}</span>
            </div>
        </div>
    @endif

    {{-- Hero --}}
    <section class="bg-cover bg-center bg-no-repeat border-b border-slate-200"
        style="background-image: url('{{ asset('images/header_pendaftaran.png') }}');">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
            <p class="text-xs sm:text-sm font-extrabold text-amber-400 uppercase tracking-widest mb-3">Formulir</p>
            <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight drop-shadow-md">Formulir Pendaftaran Beasiswa
            </h1>
            <p class="text-xl font-bold text-amber-300 mt-2">{{ $program->nama }}</p>
            <p class="text-slate-200 mt-1">Jalur: <span class="font-semibold text-white">{{ $jalur->nama }}</span> · Tahun
                {{ $periode->tahun }}
            </p>
        </div>
    </section>

    <div class="py-12 min-h-screen" x-data="pendaftaranForm()">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Progress Steps --}}
            <div class="mb-4 sm:mb-8">
                <div class="flex items-center justify-between relative">
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 rounded-full z-0">
                    </div>
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 rounded-full z-0 transition-all duration-500"
                        style="background: linear-gradient(135deg, #2B5C92, #0C1446);"
                        :style="'width: ' + ((step - 1) / ({{ $totalSteps }} - 1) * 100) + '%'"></div>

                    <template x-for="i in {{ $totalSteps }}" :key="i">
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-sm transition-all duration-300 border-4 border-white shadow-sm"
                                :class="step >= i ? 'text-white shadow-lg' : 'bg-slate-200 text-slate-400'"
                                :style="step >= i ? 'background: linear-gradient(135deg, #2B5C92, #0C1446);' : ''"
                                x-text="i"></div>
                            <span class="text-[11px] font-bold mt-2 hidden sm:block transition-colors tracking-wide"
                                :class="step >= i ? 'text-primary-dark' : 'text-slate-400'" x-text="stepNames[i-1]"></span>
                        </div>
                    </template>
                </div>
                
                {{-- Mobile Step Indicator --}}
                <div class="mt-4 text-center sm:hidden">
                    <span class="text-xs font-extrabold text-primary uppercase tracking-wider block mb-0.5">Langkah <span x-text="step"></span> dari {{ $totalSteps }}</span>
                    <h2 class="text-base font-bold text-gray-800" x-text="stepNames[step-1]"></h2>
                </div>
            </div>

            {{-- Form Error Alert --}}
            @if ($errors->any())
                <div class="mb-6 alert alert-danger">
                    <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-semibold text-sm mb-1">Terdapat kesalahan pada isian form:</p>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Form Wrapper --}}
            <form action="{{ route('pendaftaran.store') }}" method="POST" enctype="multipart/form-data"
                id="pendaftaran-form"
                @submit.prevent="submitForm($event)"
                @keydown.enter="$event.target.tagName.toLowerCase() === 'textarea' ? null : $event.preventDefault()">
                @csrf
                <input type="hidden" name="program_slug" value="{{ $program->slug }}">
                <input type="hidden" name="jalur_slug" value="{{ $jalur->slug }}">

                {{-- Step 1: Identitas Diri --}}
                <div x-show="step === 1" x-transition.opacity.duration.300ms class="card">
                    <div class="card-header flex items-center gap-2">
                        <i data-lucide="user" class="w-5 h-5 text-slate-400"></i>
                        <span class="font-bold text-lg">Identitas Diri Pribadi</span>
                    </div>
                    <div class="card-body">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Nomor Induk Kependudukan
                                    (NIK) <span class="text-red-500">*</span></label>
                                <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16"
                                    inputmode="numeric" placeholder="16 digit NIK"
                                    oninput="this.value = this.value.replace(/\D/g, '').slice(0,16)"
                                    @input="nikError = ''"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                                    :class="{ 'border-red-500 bg-red-50 ring-2 ring-red-200': nikError || @json($errors->has('nik')) }"
                                    required>
                                <template x-if="nikError">
                                    <p class="text-red-600 text-xs mt-1.5 font-bold flex items-center gap-1 bg-red-50 p-2 rounded-lg border border-red-200">
                                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                                        <span x-text="nikError"></span>
                                    </p>
                                </template>
                                @error('nik')
                                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Nama Lengkap (Sesuai KTP)
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" maxlength="255"
                                    placeholder="Nama lengkap Anda"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Tempat Lahir <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" maxlength="50"
                                    placeholder="Kota / Kabupaten tempat lahir"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Tanggal Lahir <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Jenis Kelamin <span
                                        class="text-red-500">*</span></label>
                                <select name="jenis_kelamin"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none bg-white"
                                    required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki
                                    </option>
                                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Nomor HP/WhatsApp <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="no_hp" value="{{ old('no_hp') }}" maxlength="20"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Kecamatan <span
                                        class="text-red-500">*</span></label>
                                <select name="kecamatan_id" id="kecamatan" x-model="kecamatan_id" @change="fetchDesa()"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none bg-white"
                                    required>
                                    <option value="">Pilih Kecamatan</option>
                                    @foreach($kecamatanList as $kec)
                                        <option value="{{ $kec->id }}" {{ old('kecamatan_id') == $kec->id ? 'selected' : '' }}>{{ $kec->nama_kecamatan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Desa/Kelurahan <span
                                        class="text-red-500">*</span></label>
                                <select name="desa_id" id="desa" x-model="desa_id" @focus="if(desaList.length === 0 && document.getElementById('kecamatan').value) { kecamatan_id = document.getElementById('kecamatan').value; fetchDesa(); }"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none bg-white" required>
                                    <option value="">Pilih Desa</option>
                                    <template x-for="d in desaList" :key="d.id">
                                        <option :value="d.id" x-text="d.nama_desa" :selected="oldDesaId == d.id">
                                        </option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Alamat Email <span
                                        class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="contoh: email@domain.com"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none @error('email') border-red-500 @enderror" required>
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Alamat Lengkap (Sesuai
                                    KTP) <span class="text-red-500">*</span></label>
                                <textarea name="alamat_ktp" rows="2" placeholder="Nama Jalan, RT/RW, Dusun"
                                    maxlength="100"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                                    required>{{ old('alamat_ktp') }}</textarea>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold mb-1 text-gray-700">Titik Koordinat (Latitude, Longitude) <span class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Salin dan tempel (paste) titik koordinat lokasi rumah Anda dari Google Maps. Contoh: -8.043310, 112.277617</p>
                                <input type="text" name="google_maps_url" value="{{ old('google_maps_url') }}"
                                    placeholder="Contoh: -8.043310, 112.277617"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none @error('google_maps_url') border-red-500 @enderror"
                                    required>
                            </div>

                            {{-- Field Dinamis: Identitas Diri --}}
                            @if($customFields->where('penempatan', 'identitas_diri')->count() > 0)
                                <div class="sm:col-span-2 border-t pt-4 mt-2">
                                    <h3 class="font-bold text-gray-800 mb-4">Informasi Tambahan (Identitas)</h3>
                                </div>
                                @include('pendaftaran.partials.custom_fields', ['penempatan' => 'identitas_diri'])
                            @endif

                            {{-- Akademik pindah ke Step 1 --}}
                            <div class="sm:col-span-2 border-t pt-4 mt-2">
                                <h3 class="font-bold text-gray-800 mb-4">Informasi Akademik</h3>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Asal Perguruan Tinggi
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="asal_perguruan_tinggi"
                                    value="{{ old('asal_perguruan_tinggi') }}" maxlength="255"
                                    placeholder="Contoh: Universitas Brawijaya"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                                    required>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Fakultas / Jurusan <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="program_studi" value="{{ old('program_studi') }}"
                                    maxlength="255"
                                    placeholder="Contoh: Teknik Informatika"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Semester Saat Ini <span
                                        class="text-red-500">*</span></label>
                                <input type="text" inputmode="numeric" pattern="[0-9]*" name="semester" value="{{ old('semester') }}"
                                    placeholder="Contoh: 1"
                                    oninput="this.value = this.value.replace(/\D/g, ''); if(this.value !== '') { let val = parseInt(this.value, 10); if(val > 14) this.value = 14; if(val < 1) this.value = 1; }"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                                    required>
                            </div>
                            
                            {{-- Field Dinamis: Akademik --}}
                            @if($customFields->where('penempatan', 'akademik')->count() > 0)
                                <div class="sm:col-span-2 border-t pt-4 mt-2">
                                    <h3 class="font-bold text-gray-800 mb-4">Formulir Tambahan (Akademik)</h3>
                                </div>
                                @include('pendaftaran.partials.custom_fields', ['penempatan' => 'akademik'])
                            @endif
                        </div>

                    </div>
                </div>

                <div x-show="step === 1" class="mt-4 flex justify-between gap-3">
                    <a href="{{ route('pendaftaran.index') }}" class="btn bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 px-6 py-2.5 rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                    </a>
                    <button type="button" @click="nextStep()" :disabled="isCheckingNik" class="btn btn-primary flex items-center gap-2 px-6 py-2.5 shadow-sm">
                        <span x-show="!isCheckingNik" class="flex items-center gap-1">Selanjutnya <i data-lucide="arrow-right" class="w-4 h-4"></i></span>
                        <span x-show="isCheckingNik" class="flex items-center gap-2" style="display: none;">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Memeriksa...
                        </span>
                    </button>
                </div>

                {{-- Step 2: Data Keluarga & Akademik --}}
                <div x-show="step === 2" x-transition.opacity.duration.300ms style="display: none;" class="card">
                    <div class="card-header flex items-center gap-2">
                        <i data-lucide="users" class="w-5 h-5 text-slate-400"></i>
                        <span class="font-bold text-lg">Identitas Orang Tua / Wali</span>
                    </div>
                    <div class="card-body">

                        <div class="grid grid-cols-1 gap-6">

                            {{-- Data Ayah --}}
                            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 uppercase tracking-wide text-sm">
                                    Ayah</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">Nama Lengkap <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}"
                                            maxlength="50"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            required>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">NIK <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="nik_ayah" value="{{ old('nik_ayah') }}" maxlength="16"
                                            inputmode="numeric" placeholder="16 digit NIK"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0,16)"
                                            @input="nikParentErrors.nik_ayah = ''"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            :class="{ 'border-red-500 bg-red-50 ring-1 ring-red-200': nikParentErrors.nik_ayah }"
                                            required>
                                        <template x-if="nikParentErrors.nik_ayah">
                                            <p class="text-red-600 text-xs mt-1 font-bold flex items-center gap-1">
                                                <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                                                <span x-text="nikParentErrors.nik_ayah"></span>
                                            </p>
                                        </template>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">Tempat Lahir <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="tempat_lahir_ayah"
                                            value="{{ old('tempat_lahir_ayah') }}" maxlength="20"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">Tanggal Lahir
                                            <span class="text-red-500">*</span></label>
                                        <input type="date" name="tanggal_lahir_ayah"
                                            value="{{ old('tanggal_lahir_ayah') }}"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            required>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">Alamat <span
                                                class="text-red-500">*</span></label>
                                        <textarea name="alamat_ayah" rows="2" maxlength="100" autocomplete="off"
                                            oninput="document.querySelector('[name=alamat_ibu]').value = this.value; document.querySelector('[name=alamat_ibu]').dispatchEvent(new Event('input'))"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            required>{{ old('alamat_ayah') }}</textarea>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">No. Telp / HP / WA
                                            <span class="text-red-500">*</span></label>
                                        <input type="text" name="no_hp_ayah" value="{{ old('no_hp_ayah') }}"
                                            maxlength="14" placeholder="08xxxxxxxxxx" autocomplete="off"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0,14)"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            required>
                                    </div>
                                </div>
                            </div>

                            {{-- Data Ibu --}}
                            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 uppercase tracking-wide text-sm">
                                    Ibu</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">Nama Lengkap <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}" maxlength="50"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            required>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">NIK <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="nik_ibu" value="{{ old('nik_ibu') }}" maxlength="16"
                                            inputmode="numeric" placeholder="16 digit NIK"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0,16)"
                                            @input="nikParentErrors.nik_ibu = ''"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            :class="{ 'border-red-500 bg-red-50 ring-1 ring-red-200': nikParentErrors.nik_ibu }"
                                            required>
                                        <template x-if="nikParentErrors.nik_ibu">
                                            <p class="text-red-600 text-xs mt-1 font-bold flex items-center gap-1">
                                                <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                                                <span x-text="nikParentErrors.nik_ibu"></span>
                                            </p>
                                        </template>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">Tempat Lahir <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="tempat_lahir_ibu" value="{{ old('tempat_lahir_ibu') }}"
                                            maxlength="20"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">Tanggal Lahir
                                            <span class="text-red-500">*</span></label>
                                        <input type="date" name="tanggal_lahir_ibu"
                                            value="{{ old('tanggal_lahir_ibu') }}"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            required>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">Alamat <span
                                                class="text-red-500">*</span></label>
                                        <textarea name="alamat_ibu" rows="2" maxlength="100" autocomplete="off"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            required>{{ old('alamat_ibu') }}</textarea>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">No. Telp / HP / WA
                                            <span class="text-red-500">*</span></label>
                                        <input type="text" name="no_hp_ibu" value="{{ old('no_hp_ibu') }}"
                                            maxlength="14" placeholder="08xxxxxxxxxx" autocomplete="off"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0,14)"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            required>
                                    </div>
                                </div>
                            </div>

                            {{-- Data Wali --}}
                            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                                <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 uppercase tracking-wide text-sm">
                                    Wali <span class="text-xs text-gray-500 font-normal lowercase">(Kosongkan jika tidak
                                        ada)</span></h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">Nama
                                            Lengkap</label>
                                        <input type="text" name="nama_wali" value="{{ old('nama_wali') }}"
                                            maxlength="50"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">NIK</label>
                                        <input type="text" name="nik_wali" value="{{ old('nik_wali') }}" maxlength="16"
                                            inputmode="numeric" placeholder="16 digit NIK"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0,16)"
                                            @input="nikParentErrors.nik_wali = ''"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            :class="{ 'border-red-500 bg-red-50 ring-1 ring-red-200': nikParentErrors.nik_wali }">
                                        <template x-if="nikParentErrors.nik_wali">
                                            <p class="text-red-600 text-xs mt-1 font-bold flex items-center gap-1">
                                                <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i>
                                                <span x-text="nikParentErrors.nik_wali"></span>
                                            </p>
                                        </template>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">Tempat
                                            Lahir</label>
                                        <input type="text" name="tempat_lahir_wali"
                                            value="{{ old('tempat_lahir_wali') }}" maxlength="20"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">Tanggal
                                            Lahir</label>
                                        <input type="date" name="tanggal_lahir_wali"
                                            value="{{ old('tanggal_lahir_wali') }}"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">Alamat</label>
                                        <textarea name="alamat_wali" rows="2" maxlength="100" autocomplete="off"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary">{{ old('alamat_wali') }}</textarea>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">No. Telp / HP /
                                            WA</label>
                                        <input type="text" name="no_hp_wali" value="{{ old('no_hp_wali') }}"
                                            maxlength="14" placeholder="08xxxxxxxxxx" autocomplete="off"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0,14)"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary">
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Field Dinamis: Orang Tua --}}
                            @if($customFields->where('penempatan', 'orang_tua')->count() > 0)
                                <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 mt-6">
                                    <h3 class="font-bold text-gray-800 mb-4 border-b pb-2 uppercase tracking-wide text-sm">
                                        Data Tambahan Orang Tua
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @include('pendaftaran.partials.custom_fields', ['penempatan' => 'orang_tua'])
                                    </div>
                                </div>
                            @endif

                        </div>

                    </div>
                </div>

                <div x-show="step === 2" class="mt-4 flex justify-between gap-3">
                    <button type="button" @click="prevStep()" class="btn bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 px-6 py-2.5 rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Sebelumnya
                    </button>
                    <button type="button" @click="nextStep()" class="btn btn-primary flex items-center gap-2 px-6 py-2.5 shadow-sm">
                        <span class="flex items-center gap-1">Selanjutnya <i data-lucide="arrow-right" class="w-4 h-4"></i></span>
                    </button>
                </div>

                {{-- Step 3: Kriteria Penilaian --}}
                <div x-show="step === 3" x-transition.opacity.duration.300ms style="display: none;" class="card">
                    <div class="card-header flex items-center gap-2">
                        <i data-lucide="target" class="w-5 h-5 text-slate-400"></i>
                        <span class="font-bold text-lg">Kriteria Penilaian (SPK)</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-6 alert alert-info">
                            <i data-lucide="info" class="w-4 h-4 shrink-0"></i>
                            <span>Data ini digunakan sebagai dasar perhitungan peringkat. Harap diisi dengan jujur
                                sesuai dokumen pendukung.</span>
                        </div>

                        @foreach($kelompokKriterias as $kelompok)
                            <div class="mb-8">
                                <h3 class="font-bold text-lg text-gray-900 mb-4">{{ $kelompok->nama }}</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    @foreach($kelompok->kriterias as $kriteria)
                                        <div class="sm:col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-200 overflow-hidden">
                                            <label class="block text-sm font-bold mb-1 text-gray-800 break-words">
                                                {{ $kriteria->nama }} <span class="text-red-500">*</span>
                                            </label>
                                            @if($kriteria->keterangan)
                                                <p class="text-xs text-gray-500 mb-3 break-words">{{ $kriteria->keterangan }}</p>
                                            @endif

                                            @if($kriteria->isPilihan())
                                                {{-- Dropdown Pilihan --}}
                                                <select name="kriteria_{{ $kriteria->id }}"
                                                    class="w-full max-w-full px-4 py-2.5 rounded-lg border text-sm focus:ring-2 focus:ring-primary outline-none bg-white"
                                                    required>
                                                    <option value="">-- Pilih Jawaban --</option>
                                                    @foreach($kriteria->pilihans as $pil)
                                                        <option value="{{ $pil->id }}" {{ old('kriteria_' . $kriteria->id) == $pil->id ? 'selected' : '' }} class="truncate whitespace-normal">
                                                            {{ $pil->label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                {{-- Input Numerik Langsung dengan Auto-Clamp JS --}}
                                                <input type="number" step="0.01" name="kriteria_{{ $kriteria->id }}"
                                                    value="{{ old('kriteria_' . $kriteria->id) }}"
                                                    placeholder="Isi angka (Min: {{ $kriteria->nilai_min }}, Max: {{ $kriteria->nilai_max }})"
                                                    min="{{ $kriteria->nilai_min }}" max="{{ $kriteria->nilai_max }}"
                                                    oninput="if(this.value.length > 6) this.value = this.value.slice(0,6); if(this.value !== '') { let m = parseFloat(this.max); let v = parseFloat(this.value); if(!isNaN(m) && v > m) this.value = m; }"
                                                    onblur="if(this.value !== '') { let m = parseFloat(this.min); let v = parseFloat(this.value); if(!isNaN(m) && v < m) this.value = m; }"
                                                    class="peer w-full max-w-full px-4 py-2.5 rounded-lg border text-sm focus:ring-2 focus:ring-primary outline-none transition-colors"
                                                    required>
                                                <p class="mt-2 text-xs text-red-600 font-bold hidden peer-invalid:flex items-center gap-1 bg-red-50 p-2 rounded-lg border border-red-200">
                                                    <i data-lucide="alert-circle" class="w-3.5 h-3.5 shrink-0"></i> 
                                                    <span>Nilai wajib diisi sesuai batas ({{ $kriteria->nilai_min }} - {{ $kriteria->nilai_max }}).</span>
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

                <div x-show="step === 3" class="mt-4 flex justify-between gap-3">
                    <button type="button" @click="prevStep()" class="btn bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 px-6 py-2.5 rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Sebelumnya
                    </button>
                    <button type="button" @click="nextStep()" class="btn btn-primary flex items-center gap-2 px-6 py-2.5 shadow-sm">
                        <span class="flex items-center gap-1">Selanjutnya <i data-lucide="arrow-right" class="w-4 h-4"></i></span>
                    </button>
                </div>

                                @if($hasTambahan)
                {{-- Step 4: Informasi Tambahan (Jika Ada) --}}
                <div x-show="step === 4" x-transition.opacity.duration.300ms style="display: none;" class="card">
                    <div class="card-header flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5 text-amber-500"></i>
                        <span class="font-bold text-lg">Informasi Tambahan</span>
                    </div>
                    <div class="card-body">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-amber-50/30 p-5 rounded-xl border border-amber-100">
                            @include('pendaftaran.partials.custom_fields', ['penempatan' => 'tambahan'])
                        </div>
                    </div>
                </div>
                
                <div x-show="step === 4" class="mt-4 flex justify-between gap-3">
                    <button type="button" @click="prevStep()" class="btn bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 px-6 py-2.5 rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Sebelumnya
                    </button>
                    <button type="button" @click="nextStep()" class="btn btn-primary flex items-center gap-2 px-6 py-2.5 shadow-sm">
                        Selanjutnya <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </div>
                @endif

                {{-- Step Dokumen --}}
                <div x-show="step === {{ $stepDokumen }}" x-transition.opacity.duration.300ms style="display: none;" class="card">
                    <div class="card-header flex items-center gap-2">
                        <i data-lucide="files" class="w-5 h-5 text-slate-400"></i>
                        <span class="font-bold text-lg">Berkas Persyaratan</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-6 alert alert-warning">
                            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
                            <span>Pastikan format dokumen dan ukuran sesuai ketentuan di masing-masing kolom.</span>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            @forelse($dokumens as $dok)
                                <div
                                    class="border rounded-xl p-5 bg-gray-50 flex flex-col md:flex-row md:items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <label class="block text-sm font-bold text-gray-800 mb-1">
                                            {{ $dok->nama }}
                                            @if($dok->wajib)
                                                <span class="text-red-500" title="Wajib Diunggah">*</span>
                                            @else
                                                <span class="text-gray-400 font-normal text-xs ml-1">(Opsional)</span>
                                            @endif
                                        </label>
                                        <p class="text-xs text-gray-600 mb-2">
                                            {{ $dok->deskripsi ?? 'Silakan unggah dokumen pendukung.' }}
                                        </p>
                                        <div class="flex flex-wrap items-center gap-2 mb-3">
                                            <span
                                                class="px-2 py-0.5 bg-primary-light text-primary-dark rounded text-[10px] font-semibold tracking-wider">Format:
                                                {{ str_replace(',', ', ', strtoupper($dok->format_file)) }}</span>
                                            <span
                                                class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-[10px] font-semibold tracking-wider">Max:
                                                {{ $dok->max_size_kb / 1024 }} MB</span>
                                        </div>
                                    </div>
                                    <div class="md:w-1/2 w-full shrink-0">
                                        <input type="file" name="dokumen_{{ $dok->id }}" id="dokumen_{{ $dok->id }}"
                                            data-nama="{{ $dok->nama }}"
                                            @change="handleFileUpload($event, {{ $dok->id }})"
                                            accept=".{{ str_replace(',', ',.', $dok->format_file) }}"
                                            class="block w-full text-sm text-gray-500
                                                                                        file:mr-4 file:py-2 file:px-4
                                                                                        file:rounded-full file:border-0
                                                                                        file:text-sm file:font-semibold
                                                                                        file:bg-primary file:text-white
                                                                                        hover:file:bg-primary-dark transition-colors" {{ $dok->wajib ? 'required' : '' }}>
                                        
                                        {{-- File Preview Area --}}
                                        <template x-if="filePreviews[{{ $dok->id }}]">
                                            <div class="mt-3" x-data="{ showPreview: false }">
                                                <button type="button" @click="showPreview = !showPreview" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-lg shadow-sm transition-colors">
                                                    <i data-lucide="eye" class="w-3.5 h-3.5" :class="showPreview ? 'text-primary' : ''"></i> 
                                                    <span x-text="showPreview ? 'Tutup Pratinjau' : 'Lihat Pratinjau'"></span>
                                                </button>
                                                <button type="button" @click="removeFile({{ $dok->id }})" class="inline-flex items-center gap-1.5 px-3 py-1.5 ml-2 text-xs font-semibold text-red-600 bg-white hover:bg-red-50 border border-red-200 rounded-lg shadow-sm transition-colors">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                                </button>

                                                <div x-show="showPreview" x-transition class="mt-3 p-3 bg-white border border-slate-200 rounded-xl shadow-sm">
                                                    <template x-if="filePreviews[{{ $dok->id }}].type === 'image'">
                                                        <div class="relative w-full h-40 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 group">
                                                            <img :src="filePreviews[{{ $dok->id }}].url" class="object-contain w-full h-full bg-white" alt="Preview Gambar">
                                                            <a :href="filePreviews[{{ $dok->id }}].url" :download="filePreviews[{{ $dok->id }}].name" class="absolute inset-0 bg-black/20 flex items-center justify-center sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                                                <span class="bg-white text-gray-800 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1 shadow"><i data-lucide="download" class="w-3.5 h-3.5"></i> Buka / Unduh</span>
                                                            </a>
                                                        </div>
                                                    </template>
                                                    <template x-if="filePreviews[{{ $dok->id }}].type === 'pdf'">
                                                        <div class="flex items-center gap-3 p-1">
                                                            <div class="w-10 h-10 rounded bg-red-50 flex items-center justify-center shrink-0 border border-red-100">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-xs font-bold text-gray-800 truncate" x-text="filePreviews[{{ $dok->id }}].name"></p>
                                                                <a :href="filePreviews[{{ $dok->id }}].url" :download="filePreviews[{{ $dok->id }}].name" class="text-[10px] text-primary hover:text-primary-dark hover:underline font-semibold flex items-center gap-1 mt-0.5"><i data-lucide="download" class="w-3 h-3"></i> Buka / Unduh PDF</a>
                                                            </div>
                                                        </div>
                                                    </template>
                                                    <template x-if="filePreviews[{{ $dok->id }}].type === 'other'">
                                                        <div class="flex items-center gap-3 p-1">
                                                            <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200">
                                                                <i data-lucide="file" class="w-5 h-5 text-slate-500"></i>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-xs font-bold text-gray-800 truncate" x-text="filePreviews[{{ $dok->id }}].name"></p>
                                                                <p class="text-[10px] text-slate-500 mt-0.5">Dokumen siap diunggah</p>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 italic py-4">Belum ada dokumen yang dikonfigurasi untuk
                                    jalur ini.</p>
                            @endforelse
                        </div>

                    </div>
                </div>

                <div x-show="step === {{ $stepDokumen }}" class="mt-4 flex justify-between gap-3">
                    <button type="button" @click="prevStep()" class="btn bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 px-6 py-2.5 rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Sebelumnya
                    </button>
                    <button type="button" @click="nextStep()" class="btn btn-primary flex items-center gap-2 px-6 py-2.5 shadow-sm">
                        <span class="flex items-center gap-1">Selanjutnya <i data-lucide="arrow-right" class="w-4 h-4"></i></span>
                    </button>
                </div>
        </div>

        {{-- Step Review & Submit --}}
        <div x-show="step === {{ $stepReview }}" x-transition.opacity.duration.300ms style="display: none;" class="card max-w-3xl mx-auto">
            <div class="card-header flex items-center gap-2">
                <i data-lucide="check-square" class="w-5 h-5 text-primary"></i>
                <span class="font-bold text-lg text-primary-dark">Review Pendaftaran</span>
            </div>
            <div class="card-body">

                <div class="mb-6 p-4 rounded-xl border border-red-200 bg-red-50 shadow-sm">
                    <div class="flex gap-3 items-start">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 shrink-0 mt-0.5"></i>
                        <div class="text-sm text-red-800">
                            <strong class="block mb-1 font-extrabold text-red-900 text-base">PERHATIAN: TAHAP AKHIR PENDAFTARAN!</strong>
                            Silakan periksa kembali seluruh data dan dokumen yang telah Anda isikan. Jika terdapat kesalahan, Anda masih dapat kembali ke tahap sebelumnya menggunakan tombol "Ubah Data". 
                            <br><br>
                            <span class="font-semibold text-red-900">PENTING: Setelah Anda mencentang kotak persetujuan dan mengklik "Kirim Pendaftaran", seluruh data akan dikunci secara permanen dan TIDAK DAPAT diubah kembali dengan alasan apapun.</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    {{-- A. Review Identitas --}}
                    <div class="border rounded-xl p-5 bg-gray-50">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="font-bold text-lg text-gray-800">A. Identitas Diri</h3>
                            <button type="button" @click="goToStep(1)"
                                class="text-xs font-semibold text-primary hover:text-primary-dark flex items-center gap-1">
                                <i data-lucide="edit" class="w-3 h-3"></i> Ubah Data
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm">
                            <div><span class="text-gray-500 block text-xs">Nama Lengkap</span><span class="font-medium"
                                    x-text="getFormVal('nama_lengkap')"></span></div>
                            <div><span class="text-gray-500 block text-xs">NIK</span><span class="font-medium"
                                    x-text="getFormVal('nik')"></span></div>
                            <div><span class="text-gray-500 block text-xs">Tempat, Tgl Lahir</span><span
                                    class="font-medium"
                                    x-text="getFormVal('tempat_lahir') + ', ' + getFormVal('tanggal_lahir')"></span>
                            </div>
                            <div><span class="text-gray-500 block text-xs">No. HP/WA</span><span class="font-medium"
                                    x-text="getFormVal('no_hp')"></span></div>
                            <div><span class="text-gray-500 block text-xs">Jenis Kelamin</span><span class="font-medium"
                                    x-text="getFormVal('jenis_kelamin')"></span>
                            </div>
                            <div><span class="text-gray-500 block text-xs">Email</span><span class="font-medium"
                                    x-text="getFormVal('email')"></span></div>
                            <div><span class="text-gray-500 block text-xs">Kecamatan</span><span class="font-medium"
                                    x-text="getFormVal('kecamatan_id')"></span></div>
                            <div><span class="text-gray-500 block text-xs">Desa</span><span class="font-medium"
                                    x-text="getFormVal('desa_id')"></span></div>
                            <div class="md:col-span-2"><span class="text-gray-500 block text-xs">Alamat KTP</span><span
                                    class="font-medium" x-text="getFormVal('alamat_ktp')"></span></div>
                            <div class="md:col-span-2"><span class="text-gray-500 block text-xs">Titik Koordinat (Latitude, Longitude)</span><span
                                    class="font-medium" x-text="getFormVal('google_maps_url')"></span></div>
                            @if($customFields->where('penempatan', 'identitas_diri')->count() > 0)
                                <div class="md:col-span-2 mt-2"><h4 class="font-bold text-gray-700 text-sm border-b pb-1">Tambahan Identitas</h4></div>
                                @include('pendaftaran.partials.custom_fields_review', ['penempatan' => 'identitas_diri'])
                            @endif
                        </div>
                    </div>

                    {{-- B. Data Orang Tua / Wali --}}
                    <div class="border rounded-xl p-5 bg-gray-50">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="font-bold text-lg text-gray-800">B. Data Orang Tua / Wali</h3>
                            <button type="button" @click="goToStep(2)"
                                class="text-xs font-semibold text-primary hover:text-primary-dark flex items-center gap-1">
                                <i data-lucide="edit" class="w-3 h-3"></i> Ubah Data
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h4 class="font-bold text-gray-700 text-sm mb-2 uppercase border-b pb-1">Ayah</h4>
                                <div class="grid grid-cols-1 gap-y-2 text-sm">
                                    <div><span class="text-gray-500 block text-xs">Nama Lengkap</span><span
                                            class="font-medium" x-text="getFormVal('nama_ayah')"></span></div>
                                    <div><span class="text-gray-500 block text-xs">NIK</span><span class="font-medium"
                                            x-text="getFormVal('nik_ayah')"></span></div>
                                    <div><span class="text-gray-500 block text-xs">No. HP/WA</span><span
                                            class="font-medium" x-text="getFormVal('no_hp_ayah')"></span></div>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-700 text-sm mb-2 uppercase border-b pb-1">Ibu</h4>
                                <div class="grid grid-cols-1 gap-y-2 text-sm">
                                    <div><span class="text-gray-500 block text-xs">Nama Lengkap</span><span
                                            class="font-medium" x-text="getFormVal('nama_ibu')"></span></div>
                                    <div><span class="text-gray-500 block text-xs">NIK</span><span class="font-medium"
                                            x-text="getFormVal('nik_ibu')"></span></div>
                                    <div><span class="text-gray-500 block text-xs">No. HP/WA</span><span
                                            class="font-medium" x-text="getFormVal('no_hp_ibu')"></span></div>
                                </div>
                            </div>
                            @if($customFields->where('penempatan', 'orang_tua')->count() > 0)
                                <div class="md:col-span-2 mt-2"><h4 class="font-bold text-gray-700 text-sm border-b pb-1">Tambahan Data Orang Tua</h4></div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:col-span-2">
                                    @include('pendaftaran.partials.custom_fields_review', ['penempatan' => 'orang_tua'])
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- C. Status Ekonomi / Kriteria D --}}
                    <div class="border rounded-xl p-5 bg-gray-50">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="font-bold text-lg text-gray-800">C. Kriteria Penilaian (SPK)</h3>
                            <button type="button" @click="goToStep(3)"
                                class="text-xs font-semibold text-primary hover:text-primary-dark flex items-center gap-1">
                                <i data-lucide="edit" class="w-3 h-3"></i> Ubah Data
                            </button>
                        </div>
                        <div class="grid grid-cols-1 gap-y-3 text-sm">
                            @foreach($kelompokKriterias as $kelompok)
                                @foreach($kelompok->kriterias as $kriteria)
                                    <div>
                                        <span class="text-gray-500 block text-xs">{{ $kriteria->nama }}</span>
                                        <span class="font-medium" x-text="getFormVal('kriteria_{{ $kriteria->id }}')"></span>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                        

                    </div>

                    {{-- D. Data Akademik --}}
                    <div class="border rounded-xl p-5 bg-gray-50">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="font-bold text-lg text-gray-800">D. Data Akademik</h3>
                            <button type="button" @click="goToStep(1)"
                                class="text-xs font-semibold text-primary hover:text-primary-dark flex items-center gap-1">
                                <i data-lucide="edit" class="w-3 h-3"></i> Ubah Data
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-4 gap-y-3 text-sm">
                            <div><span class="text-gray-500 block text-xs">Asal Perguruan Tinggi</span><span
                                    class="font-medium" x-text="getFormVal('asal_perguruan_tinggi')"></span></div>
                            <div><span class="text-gray-500 block text-xs">Prodi/Fakultas</span><span
                                    class="font-medium" x-text="getFormVal('program_studi')"></span></div>
                            <div><span class="text-gray-500 block text-xs">Semester</span><span class="font-medium"
                                    x-text="getFormVal('semester')"></span></div>
                            @if($customFields->where('penempatan', 'akademik')->count() > 0)
                                <div class="md:col-span-3 mt-2"><h4 class="font-bold text-gray-700 text-sm border-b pb-1">Tambahan Data Akademik</h4></div>
                                @include('pendaftaran.partials.custom_fields_review', ['penempatan' => 'akademik'])
                            @endif
                        </div>
                    </div>

                    @if($customFields->where('penempatan', 'tambahan')->count() > 0)
                        <div class="border rounded-xl p-5 bg-amber-50">
                            <div class="flex justify-between items-center mb-4 border-b pb-2">
                                <h3 class="font-bold text-lg text-amber-800">E. Informasi Tambahan</h3>
                                <button type="button" @click="goToStep({{ $stepDokumen }})"
                                    class="text-xs font-semibold text-amber-600 hover:text-amber-800 flex items-center gap-1">
                                    <i data-lucide="edit" class="w-3 h-3"></i> Ubah Data
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @include('pendaftaran.partials.custom_fields_review', ['penempatan' => 'tambahan'])
                            </div>
                        </div>
                    @endif

                    {{-- Berkas Persyaratan --}}
                    <div class="border rounded-xl p-5 bg-gray-50">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="font-bold text-lg text-gray-800">E. Dokumen</h3>
                            <button type="button" @click="goToStep({{ $stepDokumen }})"
                                class="text-xs font-semibold text-primary hover:text-primary-dark flex items-center gap-1">
                                <i data-lucide="edit" class="w-3 h-3"></i> Ubah Berkas
                            </button>
                        </div>
                        <div class="grid grid-cols-1 gap-y-3 text-sm">
                            @forelse($dokumens as $dok)
                                <div x-data="{ showPreview: false }" class="bg-white border rounded-lg overflow-hidden">
                                    <div class="flex flex-col sm:flex-row sm:justify-between items-start sm:items-center gap-3 sm:gap-2 p-3">
                                        <div>
                                            <div class="font-medium text-gray-800">{{ $dok->nama }}</div>
                                            <div class="text-xs text-primary truncate max-w-[200px] md:max-w-md"
                                                x-text="uploadedFiles[{{ $dok->id }}] || 'Belum dipilih'"></div>
                                        </div>
                                        <template x-if="uploadedFiles[{{ $dok->id }}]">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-1 bg-green-100 text-green-700 text-[11px] font-bold rounded flex items-center gap-1">
                                                    <i data-lucide="check" class="w-3 h-3"></i> Diunggah
                                                </span>
                                                <template x-if="filePreviews[{{ $dok->id }}]">
                                                    <button type="button" @click="showPreview = !showPreview" class="px-2 py-1 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-[11px] font-bold rounded flex items-center gap-1 transition-colors shadow-sm">
                                                        <i data-lucide="eye" class="w-3 h-3" :class="showPreview ? 'text-primary' : ''"></i> 
                                                        <span x-text="showPreview ? 'Tutup' : 'Lihat'"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!uploadedFiles[{{ $dok->id }}]">
                                            <span class="px-2 py-1 bg-gray-100 text-gray-500 text-xs font-bold rounded">Kosong</span>
                                        </template>
                                    </div>
                                    <div x-show="showPreview" x-transition class="p-3 border-t bg-slate-50">
                                        <template x-if="filePreviews[{{ $dok->id }}]">
                                            <div>
                                                <template x-if="filePreviews[{{ $dok->id }}].type === 'image'">
                                                    <div class="relative w-full h-40 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 group">
                                                        <img :src="filePreviews[{{ $dok->id }}].url" class="object-contain w-full h-full bg-white" alt="Preview Gambar">
                                                        <a :href="filePreviews[{{ $dok->id }}].url" :download="filePreviews[{{ $dok->id }}].name" class="absolute inset-0 bg-black/20 flex items-center justify-center sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                                            <span class="bg-white text-gray-800 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1 shadow"><i data-lucide="download" class="w-3.5 h-3.5"></i> Buka / Unduh</span>
                                                        </a>
                                                    </div>
                                                </template>
                                                <template x-if="filePreviews[{{ $dok->id }}].type === 'pdf'">
                                                    <div class="flex items-center gap-3 p-1">
                                                        <div class="w-10 h-10 rounded bg-red-50 flex items-center justify-center shrink-0 border border-red-100">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-xs font-bold text-gray-800 truncate" x-text="filePreviews[{{ $dok->id }}].name"></p>
                                                            <a :href="filePreviews[{{ $dok->id }}].url" :download="filePreviews[{{ $dok->id }}].name" class="text-[10px] text-primary hover:text-primary-dark hover:underline font-semibold flex items-center gap-1 mt-0.5"><i data-lucide="download" class="w-3 h-3"></i> Buka / Unduh PDF</a>
                                                        </div>
                                                    </div>
                                                </template>
                                                <template x-if="filePreviews[{{ $dok->id }}].type === 'other'">
                                                    <div class="flex items-center gap-3 p-1">
                                                        <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200">
                                                            <i data-lucide="file" class="w-5 h-5 text-slate-500"></i>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-xs font-bold text-gray-800 truncate" x-text="filePreviews[{{ $dok->id }}].name"></p>
                                                            <p class="text-[10px] text-slate-500 mt-0.5">Dokumen siap diunggah</p>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 italic text-xs">Belum ada dokumen.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- T&C Checkbox --}}
                    <div class="p-5 rounded-xl border border-yellow-300 bg-yellow-50 shadow-sm">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" x-model="persetujuan"
                                class="mt-1 w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="text-sm text-yellow-900">Saya telah membaca, memahami, dan menyetujui Kebijakan Privasi dan Pelindungan Data Pribadi program <strong>Beasiswa Blitar Mengabdi</strong>. Saya memberikan persetujuan kepada pihak <strong>Beasiswa Blitar Mengabdi</strong> untuk mengumpulkan, menggunakan, dan memproses data pribadi saya sesuai dengan ketentuan tersebut.</span>
                        </label>
                    </div>
                </div>

            </div>
        </div>

        <div x-show="step === {{ $stepReview }}" class="mt-4 flex justify-between gap-3 max-w-3xl mx-auto">
            <button type="button" @click="prevStep()" class="btn bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 px-6 py-2.5 rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Sebelumnya
            </button>
            <button type="button" @click="submitForm($event)" :disabled="!persetujuan" class="btn btn-primary flex items-center gap-2 px-6 py-2.5 shadow-sm" :class="!persetujuan ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-md'">
                <i data-lucide="send" class="w-4 h-4"></i> Kirim Pendaftaran
            </button>
        </div>


        </form>
    </div>
    </div>

    @push('scripts')
        <script>
            function pendaftaranForm() {
                return {
                    step: {{ $errors->any() ? 1 : 1 }},
                    stepNames: {!! $hasTambahan ? "['Identitas Diri', 'Identitas Keluarga', 'Kriteria Penilaian', 'Informasi Tambahan', 'Upload Dokumen', 'Review & Submit']" : "['Identitas Diri', 'Identitas Keluarga', 'Kriteria Penilaian', 'Upload Dokumen', 'Review & Submit']" !!},
                    persetujuan: false,
                    isCheckingNik: false,
                    nikError: '',
                    nikParentErrors: { nik_ayah: '', nik_ibu: '', nik_wali: '' },
                    nik: '{{ old('nik') }}',
                    kecamatan_id: '{{ old('kecamatan_id') }}',
                    desa_id: '{{ old('desa_id') }}',
                    oldDesaId: '{{ old('desa_id') }}',
                    desaList: [],
                    uploadedFiles: {},
                    filePreviews: {},
                    reviewTick: 0,
                    fileStorage: null,

                    initIndexedDB() {
                        return new Promise((resolve, reject) => {
                            try {
                                const request = indexedDB.open('PendaftaranFilesDB', 1);
                                request.onupgradeneeded = e => {
                                    if (!e.target.result.objectStoreNames.contains('files')) {
                                        e.target.result.createObjectStore('files');
                                    }
                                };
                                request.onsuccess = e => {
                                    this.fileStorage = e.target.result;
                                    resolve();
                                };
                                request.onerror = e => reject(e);
                            } catch (e) {
                                reject(e);
                            }
                        });
                    },

                    restoreFiles() {
                        if (!this.fileStorage) return;
                        try {
                            const tx = this.fileStorage.transaction('files', 'readonly');
                            const store = tx.objectStore('files');
                            const req = store.getAllKeys();
                            
                            req.onsuccess = () => {
                                req.result.forEach(key => {
                                    const getReq = store.get(key);
                                    getReq.onsuccess = () => {
                                        const file = getReq.result;
                                        if (file) {
                                            const id = key.replace('doc_', '');
                                            
                                            // Validasi ukuran maksimal 2MB saat restore
                                            if (file.size > 2 * 1024 * 1024) {
                                                console.warn(`File ${file.name} melebihi 2MB, menghapus dari IndexedDB.`);
                                                try {
                                                    const delTx = this.fileStorage.transaction('files', 'readwrite');
                                                    delTx.objectStore('files').delete(key);
                                                } catch(e) {}
                                                return; // Jangan restore file ini
                                            }
                                            
                                            const input = document.getElementById('dokumen_' + id);
                                            if (input) {
                                                const dt = new DataTransfer();
                                                dt.items.add(file);
                                                input.files = dt.files;
                                                this.uploadedFiles[id] = file.name;
                                                this.generatePreview(file, id);
                                            }
                                        }
                                    }
                                });
                            };
                        } catch (e) {
                            console.error("Gagal merestore file dari IndexedDB", e);
                        }
                    },

                    generatePreview(file, id) {
                        // Bersihkan memori URL yang lama jika ada
                        if (this.filePreviews[id] && this.filePreviews[id].url) {
                            URL.revokeObjectURL(this.filePreviews[id].url);
                        }
                        
                        if (file.type.startsWith('image/')) {
                            this.filePreviews[id] = { type: 'image', url: URL.createObjectURL(file), name: file.name };
                        } else if (file.type === 'application/pdf') {
                            this.filePreviews[id] = { type: 'pdf', url: URL.createObjectURL(file), name: file.name };
                        } else {
                            this.filePreviews[id] = { type: 'other', url: null, name: file.name };
                        }

                        // Render ulang icon Lucide setelah Alpine.js menampilkan DOM baru
                        setTimeout(() => {
                            if (typeof lucide !== 'undefined') {
                                lucide.createIcons();
                            }
                        }, 50);
                    },

                    removeFile(id) {
                        this.uploadedFiles[id] = '';
                        if (this.filePreviews[id] && this.filePreviews[id].url) {
                            URL.revokeObjectURL(this.filePreviews[id].url);
                        }
                        this.filePreviews[id] = null;
                        
                        // Hapus file dari DOM input file browser
                        const fileInput = document.getElementById('dokumen_' + id);
                        if (fileInput) {
                            fileInput.value = '';
                        }

                        if (this.fileStorage) {
                            try {
                                const tx = this.fileStorage.transaction('files', 'readwrite');
                                tx.objectStore('files').delete('doc_' + id);
                            } catch (e) {
                                // Abaikan error delete
                            }
                        }
                    },

                    handleFileUpload(event, id) {
                        const file = event.target.files[0];
                        if (file) {
                            // Validasi ukuran maksimal 2MB (2048 KB = 2 * 1024 * 1024 bytes)
                            if (file.size > 2 * 1024 * 1024) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Ukuran File Terlalu Besar',
                                    text: `File "${file.name}" melebihi batas maksimal 2 MB.`,
                                    confirmButtonColor: '#3b82f6'
                                });
                                event.target.value = ''; // Reset input browser
                                this.uploadedFiles[id] = '';
                                if (this.filePreviews[id] && this.filePreviews[id].url) {
                                    URL.revokeObjectURL(this.filePreviews[id].url);
                                }
                                this.filePreviews[id] = null;
                                return; // Hentikan proses
                            }

                            this.uploadedFiles[id] = file.name;
                            this.generatePreview(file, id);
                            if (this.fileStorage) {
                                try {
                                    const tx = this.fileStorage.transaction('files', 'readwrite');
                                    tx.objectStore('files').put(file, 'doc_' + id);
                                } catch (e) {
                                    console.error("Gagal menyimpan file ke IndexedDB", e);
                                }
                            }
                        } else {
                            this.uploadedFiles[id] = '';
                            if (this.filePreviews[id] && this.filePreviews[id].url) {
                                URL.revokeObjectURL(this.filePreviews[id].url);
                            }
                            this.filePreviews[id] = null;
                            if (this.fileStorage) {
                                try {
                                    const tx = this.fileStorage.transaction('files', 'readwrite');
                                    tx.objectStore('files').delete('doc_' + id);
                                } catch (e) {
                                    // Abaikan error delete
                                }
                            }
                        }
                    },

                    init() {
                        const form = document.getElementById('pendaftaran-form');

                        // Sinkronisasi tombol back browser dengan form menggunakan Hash (#step-X)
                        if (!window.location.hash) {
                            history.replaceState(null, '', '#step-' + this.step);
                        } else {
                            const match = window.location.hash.match(/#step-(\d+)/);
                            if (match) {
                                this.step = parseInt(match[1]);
                            }
                        }

                        window.addEventListener('hashchange', () => {
                            const match = window.location.hash.match(/#step-(\d+)/);
                            if (match) {
                                this.step = parseInt(match[1]);
                            }
                        });

                        // Inisialisasi IndexedDB untuk file
                        this.initIndexedDB().then(() => {
                            this.restoreFiles();
                        }).catch(e => {
                            console.warn("IndexedDB tidak didukung atau diblokir:", e);
                        });

                        // Load draft dari localStorage
                        const saved = localStorage.getItem('draft_pendaftaran');
                        if (saved && form) {
                            try {
                                const data = JSON.parse(saved);
                                // Hanya load jika program_slug cocok (mencegah salah jalur)
                                if (data.program_slug === '{{ $program->slug }}') {
                                    if (data.alpine) {
                                        this.kecamatan_id = data.alpine.kecamatan_id || '';
                                        this.desa_id = data.alpine.desa_id || '';
                                        this.oldDesaId = data.alpine.desa_id || '';
                                        this.savedDesaId = data.alpine.desa_id || ''; // Perlindungan khusus agar tidak dihapus x-model
                                        this.nik = data.alpine.nik || '';
                                        this.persetujuan = data.alpine.persetujuan || false;
                                    }
                                    
                                    // Kembalikan value DOM
                                    if (data.form) {
                                        // Delay sedikit agar Alpine selesai inisialisasi DOM (terutama x-show dan template)
                                        setTimeout(() => {
                                            Object.keys(data.form).forEach(key => {
                                                const el = form.elements[key];
                                                if (el && el.type !== 'file') {
                                                    if (el instanceof RadioNodeList || (el.length && el[0] && el[0].type === 'radio')) {
                                                        Array.from(el).forEach(r => r.checked = (r.value === data.form[key]));
                                                    } else if (el.type === 'checkbox') {
                                                        el.checked = (data.form[key] === 'on' || data.form[key] === true);
                                                    } else {
                                                        el.value = data.form[key];
                                                    }
                                                }
                                            });
                                            
                                            // Paksa Alpine untuk me-render ulang data review setelah DOM berhasil diisi
                                            this.reviewTick++;
                                        }, 100);
                                    }
                                }
                            } catch(e) {
                                console.error('Gagal meload draft', e);
                            }
                        }

                        // Save draft tiap kali ada perubahan form (DOM event delegation)
                        if (form) {
                            form.addEventListener('input', () => {
                                const formData = new FormData(form);
                                const formObj = {};
                                formData.forEach((value, key) => {
                                    if(value instanceof File) return;
                                    formObj[key] = value;
                                });
                                
                                const toSave = {
                                    program_slug: '{{ $program->slug }}',
                                    alpine: {
                                        kecamatan_id: this.kecamatan_id,
                                        desa_id: this.desa_id,
                                        nik: this.nik,
                                        persetujuan: this.persetujuan
                                    },
                                    form: formObj
                                };
                                localStorage.setItem('draft_pendaftaran', JSON.stringify(toSave));
                            });
                        }

                        // Fallback trigger save saat state Alpine berubah (seperti checkbox persetujuan)
                        this.$watch('$data', (val) => {
                            if (form) form.dispatchEvent(new Event('input'));
                        });

                        this.$watch('kecamatan_id', (value) => {
                            this.fetchDesa();
                        });

                        if (this.kecamatan_id) {
                            this.fetchDesa();
                        }
                    },

                    getFormVal(name) {
                        // Accessing reviewTick ensures re-evaluation ONLY when step changes, avoiding re-render loops!
                        const _tick = this.reviewTick;
                        
                        // Perlakuan khusus untuk dropdown dinamis agar tidak terkena jeda render DOM Alpine
                        if (name === 'desa_id' && this.desa_id && this.desaList) {
                            const desa = this.desaList.find(d => d.id == this.desa_id);
                            if (desa) return desa.nama_desa;
                        }

                        const form = document.getElementById('pendaftaran-form');
                        if (!form) return '-';
                        const el = form.elements[name];
                        if (!el) return '-';

                        if (el.tagName === 'SELECT') {
                            const opt = el.options && el.selectedIndex >= 0 ? el.options[el.selectedIndex] : null;
                            return opt && opt.value && opt.text ? opt.text.trim() : '-';
                        }

                        if (el instanceof RadioNodeList || (el.length && el[0] && el[0].type === 'radio')) {
                            const checked = Array.from(el).find(r => r.checked);
                            if (!checked) return '-';
                            const label = checked.closest('label');
                            return label ? label.textContent.trim() : checked.value;
                        }

                        return el.value && el.value.trim() !== '' ? el.value.trim() : '-';
                    },

                    fetchDesa() {
                        if (!this.kecamatan_id) {
                            this.desaList = [];
                            this.desa_id = '';
                            return;
                        }

                        fetch(`/api/desa/${this.kecamatan_id}`, {
                            headers: {
                                'ngrok-skip-browser-warning': '69420',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(res => {
                                if (!res.ok) throw new Error('API Error');
                                return res.json();
                            })
                            .then(data => {
                                if (Array.isArray(data)) {
                                    this.desaList = data;
                                    
                                    // Gunakan desa_id dari state (localStorage), savedDesaId (perlindungan), atau oldDesaId dari Laravel
                                    let targetDesa = this.desa_id || this.savedDesaId || this.oldDesaId;
                                    
                                    if (targetDesa && data.some(d => d.id == targetDesa)) {
                                        this.desa_id = targetDesa;
                                        this.savedDesaId = null; // Hapus perlindungan setelah berhasil dipakai
                                    } else {
                                        this.desa_id = '';
                                    }
                                } else {
                                    this.desaList = [];
                                    this.desa_id = '';
                                }
                                
                                // Paksa re-render teks review setelah dropdown desa terisi
                                setTimeout(() => {
                                    this.reviewTick++;
                                }, 100); // Beri sedikit waktu untuk Alpine me-render <option>
                            })
                            .catch(err => {
                                console.error('Fetch desa error:', err);
                                this.desaList = [];
                                this.desa_id = '';
                            });
                    },

                                        goToStep(targetStep) {
                        this.step = targetStep;
                        window.location.hash = 'step-' + this.step;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    },
                    prevStep() {
                        if (this.step > 1) {
                            this.step--;
                            window.location.hash = 'step-' + this.step;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    },
                    async nextStep() {
                        const form = document.getElementById('pendaftaran-form');
                        const elements = form.querySelectorAll(`[x-show="step === ${this.step}"] input, [x-show="step === ${this.step}"] select, [x-show="step === ${this.step}"] textarea`);

                        let valid = true;
                        let firstInvalidEl = null;
                        for (let el of elements) {
                            if (!el.checkValidity()) {
                                valid = false;
                                el.classList.add('border-red-500', 'bg-red-50');
                                if (!firstInvalidEl) firstInvalidEl = el;
                            } else {
                                el.classList.remove('border-red-500', 'bg-red-50');
                            }
                        }

                        // Custom validation untuk memastikan Desa terpilih, mencegah lolos saat select disabled / fetch delay
                        if (this.step === 1 && !this.desa_id) {
                            valid = false;
                            const desaEl = document.getElementById('desa');
                            if (desaEl) {
                                desaEl.classList.add('border-red-500', 'bg-red-50');
                                if (!firstInvalidEl) firstInvalidEl = desaEl;
                            }
                        }

                        if (!valid) {
                            if (firstInvalidEl) {
                                firstInvalidEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                setTimeout(() => {
                                    let fieldName = firstInvalidEl.getAttribute('data-nama') || '';
                                    if (!fieldName) {
                                        const container = firstInvalidEl.closest('div');
                                        if (container) {
                                            const labelEl = container.querySelector('label');
                                            if (labelEl) fieldName = labelEl.innerText.replace('*', '').replace('(Latitude, Longitude)', '').trim();
                                        }
                                    }
                                    if (!fieldName) {
                                        fieldName = firstInvalidEl.getAttribute('name') || 'Kolom tersebut';
                                        fieldName = fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                    }

                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Periksa Kembali',
                                        text: `Mohon lengkapi atau perbaiki format pengisian pada kolom: "${fieldName}"`,
                                        confirmButtonColor: '#3b82f6'
                                    });
                                    firstInvalidEl.reportValidity();
                                }, 300);
                            }
                            return;
                        }

                        // Cek penguncian NIK saat di Step 1
                        if (this.step === 1) {
                            const nikEl = form.querySelector('input[name="nik"]');
                            const nikVal = nikEl ? nikEl.value.trim() : '';

                            if (nikVal.length !== 16) {
                                this.nikError = 'NIK harus berjumlah tepat 16 digit.';
                                if (nikEl) {
                                    nikEl.focus();
                                    nikEl.classList.add('border-red-500', 'bg-red-50');
                                }
                                return;
                            }

                            this.isCheckingNik = true;
                            this.nikError = '';

                            try {
                                const response = await fetch('/api/cek-nik', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        nik: nikVal,
                                        program_slug: '{{ $program->slug }}'
                                    })
                                });

                                const resData = await response.json();

                                if (!resData.available) {
                                    this.nikError = resData.message || 'NIK tidak dapat digunakan untuk mendaftar.';
                                    this.isCheckingNik = false;
                                    if (nikEl) {
                                        nikEl.focus();
                                        nikEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    }
                                    return;
                                }
                            } catch (err) {
                                console.error('Gagal verifikasi NIK:', err);
                            } finally {
                                this.isCheckingNik = false;
                            }
                        }

                        // Validasi NIK Orang Tua di Step 2
                        if (this.step === 2) {
                            this.nikParentErrors = { nik_ayah: '', nik_ibu: '', nik_wali: '' };
                            let parentValid = true;

                            const nikPendaftar = form.querySelector('input[name="nik"]')?.value.trim() || '';
                            
                            // Ambil field Ayah
                            const namaAyah = form.querySelector('input[name="nama_ayah"]')?.value.trim() || '';
                            const nikAyah = form.querySelector('input[name="nik_ayah"]')?.value.trim() || '';
                            const tempatLahirAyah = form.querySelector('input[name="tempat_lahir_ayah"]')?.value.trim() || '';
                            const tanggalLahirAyah = form.querySelector('input[name="tanggal_lahir_ayah"]')?.value.trim() || '';
                            const alamatAyah = form.querySelector('textarea[name="alamat_ayah"]')?.value.trim() || '';
                            const noHpAyah = form.querySelector('input[name="no_hp_ayah"]')?.value.trim() || '';

                            // Ambil field Ibu
                            const namaIbu = form.querySelector('input[name="nama_ibu"]')?.value.trim() || '';
                            const nikIbu = form.querySelector('input[name="nik_ibu"]')?.value.trim() || '';
                            const tempatLahirIbu = form.querySelector('input[name="tempat_lahir_ibu"]')?.value.trim() || '';
                            const tanggalLahirIbu = form.querySelector('input[name="tanggal_lahir_ibu"]')?.value.trim() || '';
                            const alamatIbu = form.querySelector('textarea[name="alamat_ibu"]')?.value.trim() || '';
                            const noHpIbu = form.querySelector('input[name="no_hp_ibu"]')?.value.trim() || '';

                            // Ambil field Wali
                            const namaWali = form.querySelector('input[name="nama_wali"]')?.value.trim() || '';
                            const nikWali = form.querySelector('input[name="nik_wali"]')?.value.trim() || '';
                            const tempatLahirWali = form.querySelector('input[name="tempat_lahir_wali"]')?.value.trim() || '';
                            const tanggalLahirWali = form.querySelector('input[name="tanggal_lahir_wali"]')?.value.trim() || '';
                            const pekerjaanWali = form.querySelector('input[name="pekerjaan_wali"]')?.value.trim() || '';
                            const alamatWali = form.querySelector('textarea[name="alamat_wali"]')?.value.trim() || '';
                            const noHpWali = form.querySelector('input[name="no_hp_wali"]')?.value.trim() || '';

                            // Validasi jika user tidak sengaja mengisi kolom wali padahal tidak berniat menggunakan wali
                            if (!namaWali && (nikWali || tempatLahirWali || tanggalLahirWali || pekerjaanWali || alamatWali || noHpWali)) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Perhatian!',
                                    html: 'Anda belum mengisi <b>Nama Wali</b>, namun terdapat isian pada kolom data Wali lainnya.<br><br>Jika Anda tidak menggunakan data Wali, mohon hapus isi teks yang tidak sengaja terketik pada kolom-kolom Wali tersebut.',
                                    confirmButtonColor: '#3b82f6'
                                });
                                return; // Berhenti dan tetap di Step 2
                            }

                            // Validasi kelengkapan data jika Nama diisi
                            if (namaAyah && (!nikAyah || !tempatLahirAyah || !tanggalLahirAyah || !alamatAyah || !noHpAyah)) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Data Ayah Belum Lengkap',
                                    text: 'Anda sudah mengisi Nama Ayah, namun masih ada kolom pendukung yang kosong. Mohon lengkapi seluruh data Ayah sebelum melanjutkan.',
                                    confirmButtonColor: '#3b82f6'
                                });
                                return;
                            }
                            if (namaIbu && (!nikIbu || !tempatLahirIbu || !tanggalLahirIbu || !alamatIbu || !noHpIbu)) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Data Ibu Belum Lengkap',
                                    text: 'Anda sudah mengisi Nama Ibu, namun masih ada kolom pendukung yang kosong. Mohon lengkapi seluruh data Ibu sebelum melanjutkan.',
                                    confirmButtonColor: '#3b82f6'
                                });
                                return;
                            }
                            if (namaWali && (!nikWali || !tempatLahirWali || !tanggalLahirWali || !pekerjaanWali || !alamatWali || !noHpWali)) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Data Wali Belum Lengkap',
                                    text: 'Anda sudah mengisi Nama Wali, namun masih ada kolom pendukung yang kosong. Mohon lengkapi seluruh data Wali sebelum melanjutkan.',
                                    confirmButtonColor: '#3b82f6'
                                });
                                return;
                            }

                            // Validasi format 16 digit
                            if (nikAyah.length !== 16) {
                                this.nikParentErrors.nik_ayah = 'NIK Ayah harus berjumlah tepat 16 digit.';
                                parentValid = false;
                            }
                            if (nikIbu.length !== 16) {
                                this.nikParentErrors.nik_ibu = 'NIK Ibu harus berjumlah tepat 16 digit.';
                                parentValid = false;
                            }
                            if (nikWali && nikWali.length !== 16) {
                                this.nikParentErrors.nik_wali = 'NIK Wali harus berjumlah tepat 16 digit.';
                                parentValid = false;
                            }

                            // Validasi NIK tidak boleh sama satu sama lain
                            if (parentValid) {
                                if (nikAyah === nikIbu) {
                                    this.nikParentErrors.nik_ibu = 'NIK Ibu tidak boleh sama dengan NIK Ayah.';
                                    parentValid = false;
                                }
                                if (nikAyah === nikPendaftar) {
                                    this.nikParentErrors.nik_ayah = 'NIK Ayah tidak boleh sama dengan NIK Pendaftar.';
                                    parentValid = false;
                                }
                                if (nikIbu === nikPendaftar) {
                                    this.nikParentErrors.nik_ibu = 'NIK Ibu tidak boleh sama dengan NIK Pendaftar.';
                                    parentValid = false;
                                }
                                if (nikWali) {
                                    if (nikWali === nikAyah) {
                                        this.nikParentErrors.nik_wali = 'NIK Wali tidak boleh sama dengan NIK Ayah.';
                                        parentValid = false;
                                    } else if (nikWali === nikIbu) {
                                        this.nikParentErrors.nik_wali = 'NIK Wali tidak boleh sama dengan NIK Ibu.';
                                        parentValid = false;
                                    } else if (nikWali === nikPendaftar) {
                                        this.nikParentErrors.nik_wali = 'NIK Wali tidak boleh sama dengan NIK Pendaftar.';
                                        parentValid = false;
                                    }
                                }
                            }

                            // Validasi Nomor HP tidak boleh sama dengan pendaftar
                            if (parentValid) {
                                const noHpPendaftar = form.querySelector('input[name="no_hp"]')?.value.trim() || '';
                                let hpError = '';
                                
                                if (noHpAyah && noHpAyah === noHpPendaftar) {
                                    hpError = 'Nomor HP Ayah';
                                } else if (noHpIbu && noHpIbu === noHpPendaftar) {
                                    hpError = 'Nomor HP Ibu';
                                } else if (noHpWali && noHpWali === noHpPendaftar) {
                                    hpError = 'Nomor HP Wali';
                                }
                                
                                if (hpError) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Nomor HP Kembar',
                                        text: `${hpError} tidak boleh persis sama dengan Nomor HP Pendaftar. Mohon gunakan nomor telepon/WA lain yang valid agar panitia dapat menghubungi pihak orang tua/wali jika diperlukan.`,
                                        confirmButtonColor: '#ef4444'
                                    });
                                    return;
                                }
                            }

                            if (!parentValid) {
                                // Scroll ke error pertama
                                const firstErr = Object.keys(this.nikParentErrors).find(k => this.nikParentErrors[k]);
                                if (firstErr) {
                                    const errEl = form.querySelector(`input[name="${firstErr}"]`);
                                    if (errEl) errEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                                return;
                            }
                        }

                        if (this.step === {{ $stepDokumen }}) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Tahap Review & Kirim',
                                html: 'Anda akan masuk ke halaman terakhir.<br><br>Pastikan seluruh data dan dokumen pendukung Anda sudah benar. <b>Data yang sudah dikirim tidak dapat diubah kembali!</b>',
                                confirmButtonText: 'Ya, Lanjut ke Review',
                                confirmButtonColor: '#3b82f6',
                                showCancelButton: true,
                                cancelButtonText: 'Cek Kembali',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    this.step++;
                                    this.reviewTick++;
                                    window.location.hash = 'step-' + this.step;
                                    setTimeout(() => {
                                        window.scrollTo({ top: 0, behavior: 'smooth' });
                                    }, 300);
                                }
                            });
                            return;
                        }

                        this.step++;
                        this.reviewTick++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        // Tambahkan state baru ke history via URL Hash agar tombol back browser bekerja 100%
                        window.location.hash = 'step-' + this.step;
                    },

                    submitForm(e) {
                        if (e && e.preventDefault) e.preventDefault();
                        const form = document.getElementById('pendaftaran-form');
                        if (!form) return;

                        if (!form.checkValidity()) {
                            let firstInvalidEl = form.querySelector(':invalid');
                            if (firstInvalidEl) {
                                const stepDiv = firstInvalidEl.closest('[x-show]');
                                if (stepDiv) {
                                    const showAttr = stepDiv.getAttribute('x-show');
                                    if (showAttr.includes('1')) this.step = 1;
                                    else if (showAttr.includes('2')) this.step = 2;
                                    else if (showAttr.includes('3')) this.step = 3;
                                    else if (showAttr.includes('4')) this.step = 4;

                                    this.reviewTick++;
                                    setTimeout(() => {
                                        if (firstInvalidEl.tagName !== 'INPUT' || firstInvalidEl.type !== 'file') {
                                            firstInvalidEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                            firstInvalidEl.focus();
                                        }
                                        let fieldName = firstInvalidEl.getAttribute('data-nama') || '';
                                        if (!fieldName) {
                                            const container = firstInvalidEl.closest('div');
                                            if (container) {
                                                const labelEl = container.querySelector('label');
                                                if (labelEl) fieldName = labelEl.innerText.replace('*', '').replace('(Latitude, Longitude)', '').trim();
                                            }
                                        }
                                        if (!fieldName) {
                                            fieldName = firstInvalidEl.getAttribute('name') || 'Kolom tersebut';
                                            fieldName = fieldName.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                                        }

                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Pendaftaran Belum Lengkap',
                                            text: `Ada isian yang masih kosong atau formatnya salah pada: "${fieldName}". Mohon perbaiki sebelum mengirim.`,
                                            confirmButtonColor: '#3b82f6'
                                        });
                                        firstInvalidEl.reportValidity();
                                    }, 400);
                                }
                            }
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Konfirmasi Pengiriman',
                                html: 'Apakah Anda yakin semua data sudah benar?<br><br>Data yang telah dikirim <b>TIDAK DAPAT DIUBAH</b> dan akan langsung diproses oleh sistem.',
                                showCancelButton: true,
                                confirmButtonColor: '#3b82f6',
                                cancelButtonColor: '#64748b',
                                confirmButtonText: 'Ya, Kirim Sekarang!',
                                cancelButtonText: 'Batal',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Munculkan animasi loading global (layar penuh)
                                    document.documentElement.classList.remove('skip-preloader');
                                    const globalPreloader = document.getElementById('global-preloader');
                                    if (globalPreloader) {
                                        globalPreloader.style.setProperty('display', 'flex', 'important');
                                    }

                                    // Hapus draft saat sukses submit agar pendaftaran berikutnya bersih
                                    localStorage.removeItem('draft_pendaftaran');
                                    HTMLFormElement.prototype.submit.call(form);
                                }
                            });
                        }
                    },
                }
            }

            // Mencegah bfcache menampilkan ulang form ini setelah sukses pendaftaran
            window.addEventListener('pageshow', function (event) {
                if (sessionStorage.getItem('pendaftaran_success') === 'true') {
                    sessionStorage.removeItem('pendaftaran_success');
                    window.location.replace("{{ route('home') }}");
                }
            });
        </script>
    @endpush
</x-layouts.public>