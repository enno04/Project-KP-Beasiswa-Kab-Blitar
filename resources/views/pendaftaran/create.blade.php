<x-layouts.public :title="'Pendaftaran ' . $program->nama">
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
            <div class="mb-8">
                <div class="flex items-center justify-between relative">
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 rounded-full z-0">
                    </div>
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 rounded-full z-0 transition-all duration-500"
                        style="background: linear-gradient(135deg, #2B5C92, #0C1446);"
                        :style="'width: ' + ((step - 1) / 4 * 100) + '%'"></div>

                    <template x-for="i in 5" :key="i">
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
                                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" maxlength="50"
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
                                <select name="kecamatan_id" id="kecamatan" x-model="kecamatan_id"
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
                                <select name="desa_id" id="desa" x-model="desa_id"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none bg-white"
                                    :disabled="desaList.length === 0" required>
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
                                <label class="block text-sm font-semibold mb-1 text-gray-700">Titik Koordinat (Google
                                    Maps) <span class="text-red-500">*</span></label>
                                <p class="text-xs text-gray-500 mb-2">Salin dan tempel (paste) tautan lokasi rumah Anda
                                    dari Google Maps. Contoh: https://maps.google.com/...</p>
                                <input type="url" name="google_maps_url" value="{{ old('google_maps_url') }}"
                                    placeholder="https://maps.google.com/..."
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none @error('google_maps_url') border-red-500 @enderror"
                                    required>
                            </div>

                            {{-- Akademik pindah ke Step 1 --}}
                            <div class="sm:col-span-2 border-t pt-4 mt-2">
                                <h3 class="font-bold text-gray-800 mb-4">Informasi Akademik</h3>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Asal Perguruan Tinggi
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="asal_perguruan_tinggi"
                                    value="{{ old('asal_perguruan_tinggi') }}" maxlength="40"
                                    placeholder="Contoh: Universitas Brawijaya"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                                    required>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Fakultas / Jurusan <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="program_studi" value="{{ old('program_studi') }}"
                                    maxlength="20"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold mb-2 text-gray-700">Semester Saat Ini <span
                                        class="text-red-500">*</span></label>
                                <input type="number" name="semester" value="{{ old('semester') }}" min="1" max="14"
                                    oninput="if (this.value !== '') {this.value = Math.min(Math.max(parseInt(this.value), 1), 14);}"
                                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                                    required>
                            </div>
                        </div>
                    </div>
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
                                        <textarea name="alamat_ayah" rows="2" maxlength="100"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            required>{{ old('alamat_ayah') }}</textarea>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">No. Telp / HP / WA
                                            <span class="text-red-500">*</span></label>
                                        <input type="text" name="no_hp_ayah" value="{{ old('no_hp_ayah') }}"
                                            maxlength="14" placeholder="08xxxxxxxxxx"
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
                                        <textarea name="alamat_ibu" rows="2" maxlength="100"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary"
                                            required>{{ old('alamat_ibu') }}</textarea>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">No. Telp / HP / WA
                                            <span class="text-red-500">*</span></label>
                                        <input type="text" name="no_hp_ibu" value="{{ old('no_hp_ibu') }}"
                                            maxlength="14" placeholder="08xxxxxxxxxx"
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
                                        <textarea name="alamat_wali" rows="2" maxlength="100"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary">{{ old('alamat_wali') }}</textarea>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-xs font-semibold mb-1 text-gray-700">No. Telp / HP /
                                            WA</label>
                                        <input type="text" name="no_hp_wali" value="{{ old('no_hp_wali') }}"
                                            maxlength="14" placeholder="08xxxxxxxxxx"
                                            oninput="this.value = this.value.replace(/\D/g, '').slice(0,14)"
                                            class="w-full px-4 py-2.5 rounded-lg border text-sm outline-none focus:ring-1 focus:ring-primary">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
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
                                        <div class="sm:col-span-2 bg-gray-50 p-4 rounded-xl border border-gray-200">
                                            <label class="block text-sm font-bold mb-1 text-gray-800">
                                                {{ $kriteria->nama }} <span class="text-red-500">*</span>
                                            </label>
                                            @if($kriteria->keterangan)
                                                <p class="text-xs text-gray-500 mb-3">{{ $kriteria->keterangan }}</p>
                                            @endif

                                            @if($kriteria->isPilihan())
                                                {{-- Dropdown Pilihan --}}
                                                <select name="kriteria_{{ $kriteria->id }}"
                                                    class="w-full px-4 py-2.5 rounded-lg border text-sm focus:ring-2 focus:ring-primary outline-none bg-white"
                                                    required>
                                                    <option value="">-- Pilih Jawaban --</option>
                                                    @foreach($kriteria->pilihans as $pil)
                                                        <option value="{{ $pil->id }}" {{ old('kriteria_' . $kriteria->id) == $pil->id ? 'selected' : '' }}>
                                                            {{ $pil->label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                {{-- Input Numerik Langsung --}}
                                                <input type="number" step="0.01" name="kriteria_{{ $kriteria->id }}"
                                                    value="{{ old('kriteria_' . $kriteria->id) }}"
                                                    placeholder="Masukkan angka (Min: {{ $kriteria->nilai_min }}, Max: {{ $kriteria->nilai_max }})"
                                                    min="{{ $kriteria->nilai_min }}" max="{{ $kriteria->nilai_max }}"
                                                    class="w-full px-4 py-2.5 rounded-lg border text-sm focus:ring-2 focus:ring-primary outline-none"
                                                    required>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Step 4: Dokumen --}}
                <div x-show="step === 4" x-transition.opacity.duration.300ms style="display: none;" class="card">
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
                                        <input type="file" name="dokumen_{{ $dok->id }}"
                                            @change="uploadedFiles[{{ $dok->id }}] = $event.target.files.length ? $event.target.files[0].name : ''"
                                            accept=".{{ str_replace(',', ',.', $dok->format_file) }}"
                                            class="block w-full text-sm text-gray-500
                                                                                        file:mr-4 file:py-2 file:px-4
                                                                                        file:rounded-full file:border-0
                                                                                        file:text-sm file:font-semibold
                                                                                        file:bg-primary file:text-white
                                                                                        hover:file:bg-primary-dark transition-colors" {{ $dok->wajib ? 'required' : '' }}>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 italic py-4">Belum ada dokumen yang dikonfigurasi untuk
                                    jalur ini.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
        </div>

        {{-- Step 5: Review & Submit --}}
        <div x-show="step === 5" x-transition.opacity.duration.300ms style="display: none;" class="card">
            <div class="card-header flex items-center gap-2">
                <i data-lucide="check-square" class="w-5 h-5 text-primary"></i>
                <span class="font-bold text-lg text-primary-dark">Review Pendaftaran</span>
            </div>
            <div class="card-body">

                <div class="mb-6 p-4 rounded-xl border bg-primary-light border-primary-light text-primary-dark text-sm">
                    Silakan periksa kembali data yang telah Anda isikan. Jika terdapat kesalahan, Anda dapat kembali ke
                    tahap sebelumnya menggunakan tombol "Ubah Data". Jika sudah benar, centang kotak persetujuan dan
                    klik "Kirim Pendaftaran".
                </div>

                <div class="space-y-6">
                    {{-- A. Review Identitas --}}
                    <div class="border rounded-xl p-5 bg-gray-50">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="font-bold text-lg text-gray-800">A. Identitas Diri</h3>
                            <button type="button" @click="step = 1; window.scrollTo({ top: 0, behavior: 'smooth' })"
                                class="text-xs font-semibold text-primary hover:text-primary-dark flex items-center gap-1">
                                <i data-lucide="edit" class="w-3 h-3"></i> Ubah Data
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-3 text-sm">
                            <div><span class="text-gray-500 block text-xs">NIK</span><span class="font-medium"
                                    x-text="getFormVal('nik')"></span></div>
                            <div><span class="text-gray-500 block text-xs">Nama Lengkap</span><span class="font-medium"
                                    x-text="getFormVal('nama_lengkap')"></span></div>
                            <div><span class="text-gray-500 block text-xs">Tempat, Tgl Lahir</span><span
                                    class="font-medium"
                                    x-text="getFormVal('tempat_lahir') + ', ' + getFormVal('tanggal_lahir')"></span>
                            </div>
                            <div><span class="text-gray-500 block text-xs">Jenis Kelamin</span><span class="font-medium"
                                    x-text="getFormVal('jenis_kelamin')"></span>
                            </div>
                            <div><span class="text-gray-500 block text-xs">No. HP/WA</span><span class="font-medium"
                                    x-text="getFormVal('no_hp')"></span></div>
                            <div><span class="text-gray-500 block text-xs">Email</span><span class="font-medium"
                                    x-text="getFormVal('email')"></span></div>
                            <div><span class="text-gray-500 block text-xs">Kecamatan</span><span class="font-medium"
                                    x-text="getFormVal('kecamatan_id')"></span></div>
                            <div><span class="text-gray-500 block text-xs">Desa</span><span class="font-medium"
                                    x-text="getFormVal('desa_id')"></span></div>
                            <div class="md:col-span-2"><span class="text-gray-500 block text-xs">Alamat KTP</span><span
                                    class="font-medium" x-text="getFormVal('alamat_ktp')"></span></div>
                            <div class="md:col-span-2"><span class="text-gray-500 block text-xs">Titik Koordinat (Google
                                    Maps)</span><a :href="getFormVal('google_maps_url') !== '-' ? getFormVal('google_maps_url') : '#'" target="_blank"
                                    class="font-medium text-primary hover:underline break-all"
                                    x-text="getFormVal('google_maps_url')"></a></div>
                        </div>
                    </div>

                    {{-- B. Data Orang Tua / Wali --}}
                    <div class="border rounded-xl p-5 bg-gray-50">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="font-bold text-lg text-gray-800">B. Data Orang Tua / Wali</h3>
                            <button type="button" @click="step = 2; window.scrollTo({ top: 0, behavior: 'smooth' })"
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
                        </div>
                    </div>

                    {{-- C. Status Ekonomi / Kriteria D --}}
                    <div class="border rounded-xl p-5 bg-gray-50">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="font-bold text-lg text-gray-800">C. Kriteria Penilaian (SPK)</h3>
                            <button type="button" @click="step = 3; window.scrollTo({ top: 0, behavior: 'smooth' })"
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
                            <button type="button" @click="step = 1; window.scrollTo({ top: 0, behavior: 'smooth' })"
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
                        </div>
                    </div>

                    {{-- E. Berkas Persyaratan --}}
                    <div class="border rounded-xl p-5 bg-gray-50">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="font-bold text-lg text-gray-800">E. Dokumen</h3>
                            <button type="button" @click="step = 4; window.scrollTo({ top: 0, behavior: 'smooth' })"
                                class="text-xs font-semibold text-primary hover:text-primary-dark flex items-center gap-1">
                                <i data-lucide="edit" class="w-3 h-3"></i> Ubah Berkas
                            </button>
                        </div>
                        <div class="grid grid-cols-1 gap-y-3 text-sm">
                            @forelse($dokumens as $dok)
                                <div class="flex justify-between items-center bg-white p-3 border rounded-lg">
                                    <div>
                                        <div class="font-medium text-gray-800">{{ $dok->nama }}</div>
                                        <div class="text-xs text-primary truncate max-w-[200px] md:max-w-md"
                                            x-text="uploadedFiles[{{ $dok->id }}] || 'Belum dipilih'"></div>
                                    </div>
                                    <template x-if="uploadedFiles[{{ $dok->id }}]">
                                        <span
                                            class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded flex items-center gap-1"><i
                                                data-lucide="check" class="w-3 h-3"></i> Sudah Diunggah</span>
                                    </template>
                                    <template x-if="!uploadedFiles[{{ $dok->id }}]">
                                        <span
                                            class="px-2 py-1 bg-gray-100 text-gray-500 text-xs font-bold rounded">Kosong</span>
                                    </template>
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
                            <span class="text-sm text-yellow-900">Saya menyatakan bahwa seluruh data dan dokumen yang
                                saya unggah adalah <strong>BENAR</strong> dan dapat dipertanggungjawabkan keasliannya.
                                Apabila di kemudian hari terbukti ada pemalsuan, saya bersedia menerima sanksi yang
                                berlaku dan status pendaftaran saya dibatalkan.</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="mt-8 flex justify-between items-center px-2">
            <button type="button" x-show="step > 1" @click="step--" class="btn btn-outline">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Sebelumnya
            </button>
            <div x-show="step === 1"></div>

            <button type="button" x-show="step < 5" @click="nextStep()" :disabled="isCheckingNik" class="btn btn-primary flex items-center gap-2">
                <template x-if="!isCheckingNik">
                    <span class="flex items-center gap-1">Selanjutnya <i data-lucide="arrow-right" class="w-4 h-4"></i></span>
                </template>
                <template x-if="isCheckingNik">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memeriksa NIK...
                    </span>
                </template>
            </button>
            <button type="button" @click="submitForm($event)" x-show="step === 5" :disabled="!persetujuan"
                class="btn btn-primary flex items-center gap-2"
                :class="!persetujuan ? 'opacity-50 cursor-not-allowed' : 'hover:shadow-md'">
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
                    stepNames: ['Identitas Diri', 'Akademik & Keluarga', 'Kriteria Penilaian', 'Upload Dokumen', 'Review & Submit'],
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
                    reviewTick: 0,

                    init() {
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

                        fetch(`/api/desa/${this.kecamatan_id}`)
                            .then(res => {
                                if (!res.ok) throw new Error('API Error');
                                return res.json();
                            })
                            .then(data => {
                                if (Array.isArray(data)) {
                                    this.desaList = data;
                                    if (this.oldDesaId && data.some(d => d.id == this.oldDesaId)) {
                                        this.desa_id = this.oldDesaId;
                                    } else {
                                        this.desa_id = '';
                                    }
                                } else {
                                    this.desaList = [];
                                    this.desa_id = '';
                                }
                            })
                            .catch(err => {
                                console.error('Fetch desa error:', err);
                                this.desaList = [];
                                this.desa_id = '';
                            });
                    },

                    async nextStep() {
                        const form = document.getElementById('pendaftaran-form');
                        const elements = form.querySelectorAll(`[x-show="step === ${this.step}"] input, [x-show="step === ${this.step}"] select, [x-show="step === ${this.step}"] textarea`);

                        let valid = true;
                        for (let el of elements) {
                            if (!el.checkValidity()) {
                                valid = false;
                                el.classList.add('border-red-500', 'bg-red-50');
                                el.reportValidity();
                                break;
                            } else {
                                el.classList.remove('border-red-500', 'bg-red-50');
                            }
                        }

                        if (!valid) return;

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
                            const nikAyah = form.querySelector('input[name="nik_ayah"]')?.value.trim() || '';
                            const nikIbu = form.querySelector('input[name="nik_ibu"]')?.value.trim() || '';
                            const nikWali = form.querySelector('input[name="nik_wali"]')?.value.trim() || '';

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

                        this.step++;
                        this.reviewTick++;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
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
                                            firstInvalidEl.focus();
                                        }
                                        firstInvalidEl.reportValidity();
                                    }, 200);
                                }
                            }
                        } else {
                            HTMLFormElement.prototype.submit.call(form);
                        }
                    },
                }
            }
        </script>
    @endpush
</x-layouts.public>