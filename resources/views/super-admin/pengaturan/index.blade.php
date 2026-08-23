<x-layouts.admin :title="'Pengaturan Web'">
    <x-page-header title="Pengaturan Pusat Informasi & Layanan Bantuan" subtitle="Atur informasi narahubung yang akan ditampilkan pada halaman publik." />

    <div class="card max-w-4xl">
        <div class="card-body p-6">
            @if(session('success'))
                <x-alert type="success" class="mb-6">
                    {{ session('success') }}
                </x-alert>
            @endif

            @php
                $contactPersons = json_decode($settings['contact_persons']->value ?? '[]', true);
                if (empty($contactPersons)) {
                    $contactPersons = [['name' => '', 'phone' => '']];
                }
            @endphp

            <form action="{{ route('super-admin.pengaturan.update') }}" method="POST" class="space-y-6" x-data="{
                contacts: {{ json_encode($contactPersons) }},
                addContact() {
                    this.contacts.push({name: '', phone: ''});
                },
                removeContact(index) {
                    if (this.contacts.length > 1) {
                        this.contacts.splice(index, 1);
                    }
                }
            }">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-4">Daftar Contact Person</label>
                    
                    <div class="space-y-4">
                        <template x-for="(contact, index) in contacts" :key="index">
                            <div class="flex items-start gap-3">
                                <div class="flex-1">
                                    <input type="text" x-model="contact.name" :name="'contact_persons['+index+'][name]'" class="form-input w-full" placeholder="Contoh: Bapak Akhyat" required>
                                    <p class="text-xs text-slate-500 mt-1">Nama ini akan tampil di tombol publik.</p>
                                </div>
                                <div class="flex-1">
                                    <input type="text" x-model="contact.phone" :name="'contact_persons['+index+'][phone]'" class="form-input w-full" placeholder="Contoh: 0813... atau +62 813..." required>
                                    <p class="text-xs text-slate-500 mt-1">Gunakan awalan 08x atau +62 8x (sistem otomatis menyesuaikan link WA).</p>
                                </div>
                                <div>
                                    <button type="button" @click="removeContact(index)" class="btn btn-danger p-2 h-[42px]" x-show="contacts.length > 1" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="button" @click="addContact()" class="btn btn-outline-primary mt-4 text-sm">
                        <i data-lucide="plus" class="w-4 h-4 mr-1"></i> Tambah Kontak
                    </button>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100">
                    <div class="mb-5">
                        <label class="block text-base font-bold text-slate-800">Informasi Operasional & Sekretariat</label>
                        <p class="text-xs text-slate-500 mt-1">Pengaturan di bawah ini akan memengaruhi alamat dan jam operasional yang tampil pada <b>Footer Website Publik</b>.</p>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Alamat Sekretariat</label>
                            <input type="text" name="settings[address]" class="form-input w-full" value="{{ old('settings.address', $settings['address']->value ?? '') }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Link Google Maps</label>
                            <input type="url" name="settings[maps_link]" class="form-input w-full" value="{{ old('settings.maps_link', $settings['maps_link']->value ?? '') }}" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jam Operasional</label>
                            <input type="text" name="settings[operational_hours]" class="form-input w-full" value="{{ old('settings.operational_hours', $settings['operational_hours']->value ?? '') }}" placeholder="Contoh: Sen - Jum, 08:00 - 16:00 WIB" required>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100">
                    <div class="mb-5">
                        <label class="block text-base font-bold text-slate-800">Sosial Media & Tautan Web</label>
                        <p class="text-xs text-slate-500 mt-1">Tautan ini akan dimunculkan dalam bentuk <b>Ikon Interaktif</b> di bagian Kontak pada Footer halaman Publik.</p>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Link Instagram Resmi (Opsional)</label>
                            <input type="url" name="settings[instagram_link]" class="form-input w-full" value="{{ old('settings.instagram_link', $settings['instagram_link']->value ?? '') }}" placeholder="https://instagram.com/...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Link Website Resmi (Opsional)</label>
                            <input type="url" name="settings[website_link]" class="form-input w-full" value="{{ old('settings.website_link', $settings['website_link']->value ?? '') }}" placeholder="https://...">
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100">
                    <div class="mb-5">
                        <label class="block text-base font-bold text-slate-800">Pengumuman Global (Beranda Utama)</label>
                        <p class="text-xs text-slate-500 mt-1">Pengumuman akan muncul berupa <b>Teks Berjalan Warna Biru</b> tepat di bawah halaman utama Beranda.</p>
                    </div>
                    <div class="space-y-4 bg-slate-50 p-5 rounded-xl border border-slate-100">
                        <div class="flex items-center gap-3 border-b border-slate-200 pb-4">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[announcement_active]" value="1" class="sr-only peer" {{ (old('settings.announcement_active', $settings['announcement_active']->value ?? '0') == '1') ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                            <div>
                                <span class="text-sm font-bold text-slate-800 block">Tampilkan Pengumuman di Halaman Publik</span>
                            </div>
                        </div>
                        <div class="pt-2">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Teks Pengumuman</label>
                            <textarea name="settings[announcement_text]" rows="3" class="form-input w-full p-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-inner bg-white resize-none transition-all" placeholder="Ketik teks peringatan atau info penting di sini...">{{ old('settings.announcement_text', $settings['announcement_text']->value ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6 mt-6 border-t border-slate-100">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
