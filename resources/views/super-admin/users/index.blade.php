<x-layouts.admin :title="'Kelola Pengguna'">
    <x-page-header title="Kelola Pengguna" subtitle="Manajemen hak akses admin Kabupaten, Kecamatan, Desa, dan OPD.">
        <x-slot:actions>
            <button onclick="document.getElementById('modal-tambah-user').classList.remove('hidden')" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah User
            </button>
        </x-slot:actions>
    </x-page-header>

    {{-- Table --}}
    {{-- Table --}}
    <x-data-table :items="$users" empty-icon="users" empty-title="Belum Ada Pengguna" empty-text="Belum ada user terdaftar dalam sistem.">
        <x-slot:header>
            <x-table-column label="Nama Pengguna" sortable="nama" />
            <x-table-column label="Role" sortable="role_id" />
            <x-table-column label="Wilayah / Instansi" />
            <x-table-column label="Terdaftar" sortable="created_at" />
            <x-table-column label="Aksi" align="right" />
        </x-slot:header>

        @foreach($users as $user)
            <tr class="hover:bg-slate-50 transition-colors group">
                <td class="px-4 py-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0" style="background: linear-gradient(135deg, #2B5C92, #0C1446);">
                            {{ strtoupper(substr($user->nama, 0, 2)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $user->nama }}</p>
                            <p class="text-xs text-slate-400">{{ $user->username }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 border-b border-slate-100">
                    @php
                        $kode = $user->role->kode ?? '';
                        $roleType = match($kode) {
                            'super_admin' => 'primary',
                            'admin_kabupaten' => 'info',
                            'admin_opd' => 'accent',
                            'admin_kecamatan' => 'warning',
                            'admin_desa' => 'success',
                            'admin_dpmd' => 'primary',
                            default => 'muted',
                        };
                    @endphp
                    <x-badge :type="$roleType">{{ $user->role->nama ?? 'Tanpa Role' }}</x-badge>
                </td>
                <td class="px-4 py-3 border-b border-slate-100">
                    @if($kode === 'admin_opd')
                        <span class="text-sm font-medium">{{ $user->opd->nama_opd ?? '-' }}</span>
                    @elseif($kode === 'admin_kecamatan')
                        <span class="text-sm font-medium">Kec. {{ $user->kecamatan->nama_kecamatan ?? '-' }}</span>
                    @elseif($kode === 'admin_desa')
                        <span class="text-sm font-medium">Desa {{ $user->desa->nama_desa ?? '-' }}, Kec. {{ $user->kecamatan->nama_kecamatan ?? '-' }}</span>
                    @else
                        <span class="text-sm italic text-slate-400">Pusat</span>
                    @endif
                </td>
                <td class="px-4 py-3 border-b border-slate-100 text-sm text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                <td class="px-4 py-3 border-b border-slate-100 text-right">
                    @if(auth()->id() !== $user->id)
                        <form action="{{ route('super-admin.master.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-xs btn-ghost text-red-500 hover:bg-red-50" title="Hapus">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </x-data-table>

    {{-- Modal Tambah User --}}
    <div id="modal-tambah-user" class="modal-overlay hidden" x-data="userForm()">
        <div class="modal-panel max-w-lg mx-4">
            <form action="{{ route('super-admin.master.users.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h3 class="text-lg font-bold">Tambah User Baru</h3>
                    <button type="button" onclick="document.getElementById('modal-tambah-user').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <div class="modal-body space-y-4">
                    <x-form-input name="nama" label="Nama Lengkap" :required="true" placeholder="Masukkan nama lengkap" />
                    <x-form-input name="username" label="Username" :required="true" placeholder="Masukkan username" />
                    <x-form-input name="password" label="Password" type="password" :required="true" placeholder="Min. 8 karakter" helper="Minimal 8 karakter" />

                    <div>
                        <label class="form-label">Role / Hak Akses <span class="required">*</span></label>
                        <select name="role_id" x-model="roleId" @change="updateRoleKode" class="form-select" required>
                            <option value="">— Pilih Role —</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->id }}" data-kode="{{ $r->kode }}">{{ $r->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="roleKode === 'admin_opd'" x-transition class="pt-1">
                        <label class="form-label">Instansi OPD <span class="required">*</span></label>
                        <select name="opd_id" class="form-select" :required="roleKode === 'admin_opd'">
                            <option value="">— Pilih OPD —</option>
                            @foreach($opdList as $op)
                                <option value="{{ $op->id }}">{{ $op->nama_opd }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="roleKode === 'admin_kecamatan' || roleKode === 'admin_desa'" x-transition class="pt-1">
                        <label class="form-label">Kecamatan <span class="required">*</span></label>
                        <select name="kecamatan_id" id="kecamatan_select" @change="fetchDesa()" class="form-select" :required="roleKode === 'admin_kecamatan' || roleKode === 'admin_desa'">
                            <option value="">— Pilih Kecamatan —</option>
                            @foreach($kecamatanList as $kec)
                                <option value="{{ $kec->id }}">{{ $kec->nama_kecamatan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div x-show="roleKode === 'admin_desa'" x-transition class="pt-1">
                        <label class="form-label">Desa <span class="required">*</span></label>
                        <select name="desa_id" id="desa_select" class="form-select" :required="roleKode === 'admin_desa'">
                            <option value="">— Pilih Desa —</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="document.getElementById('modal-tambah-user').classList.add('hidden')" class="btn btn-outline">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function userForm() {
            return {
                roleId: '', roleKode: '',
                updateRoleKode(e) {
                    const selected = e.target.options[e.target.selectedIndex];
                    this.roleKode = selected.getAttribute('data-kode') || '';
                },
                fetchDesa() {
                    const kecId = document.getElementById('kecamatan_select').value;
                    const desaSelect = document.getElementById('desa_select');
                    desaSelect.innerHTML = '<option value="">Memuat...</option>';
                    if(!kecId) { desaSelect.innerHTML = '<option value="">— Pilih Desa —</option>'; return; }
                    fetch(`/api/desa-by-kecamatan/${kecId}`)
                        .then(res => res.json())
                        .then(data => {
                            desaSelect.innerHTML = '<option value="">— Pilih Desa —</option>';
                            data.forEach(d => { desaSelect.innerHTML += `<option value="${d.id}">${d.nama_desa}</option>`; });
                        });
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>
