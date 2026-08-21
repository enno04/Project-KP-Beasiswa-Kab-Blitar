<?php
$content = file_get_contents("resources/views/opd/verifikasi/show.blade.php");

// 1. Rename "Informasi Tambahan (Formulir Dinamis)"
$content = str_replace(
    "<div class=\"card-header font-bold text-slate-700 bg-slate-50\">Informasi Tambahan (Formulir Dinamis)</div>",
    "<div class=\"card-header font-bold text-slate-700 bg-slate-50\">Informasi Tambahan</div>",
    $content
);

// 2. Add Akademik and Orang Tua sections after Informasi Pendaftar (and before Informasi Tambahan)
// Wait, the user wants "detail yang lengkap". Let's add Biodata (full), Akademik, and Orang Tua.
// Currently it has a simple "Informasi Pendaftar" card. Let's enhance it.
$new_sections = <<<EOT
            {{-- Data Akademik --}}
            <div class="card">
                <div class="card-header font-bold">Data Akademik</div>
                <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="md:col-span-2">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Perguruan Tinggi</p>
                        <p class="font-medium">{{ \$upload->pendaftaran->identitas->asal_perguruan_tinggi ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Program Studi</p>
                        <p class="font-medium">{{ \$upload->pendaftaran->identitas->program_studi ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Semester</p>
                        <p class="font-medium">{{ \$upload->pendaftaran->identitas->semester ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Data Orang Tua / Wali --}}
            @if(\$upload->pendaftaran->orangtua)
            <div class="card">
                <div class="card-header font-bold">Data Orang Tua / Wali</div>
                <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    @foreach([
                        ['Ayah', \$upload->pendaftaran->orangtua->nama_ayah, \$upload->pendaftaran->orangtua->nik_ayah, \$upload->pendaftaran->orangtua->no_hp_ayah, \$upload->pendaftaran->orangtua->alamat_ayah],
                        ['Ibu', \$upload->pendaftaran->orangtua->nama_ibu, \$upload->pendaftaran->orangtua->nik_ibu, \$upload->pendaftaran->orangtua->no_hp_ibu, \$upload->pendaftaran->orangtua->alamat_ibu],
                    ] as [\$role, \$nama, \$nik, \$hp, \$alamat])
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">{{ \$role }}</span>
                        <div class="font-medium">{{ \$nama ?? '-' }}</div>
                        <div class="text-xs text-slate-500 mt-1">NIK: {{ \$nik ?? '-' }} &middot; HP: {{ \$hp ?? '-' }}</div>
                        <div class="text-xs text-slate-500 mt-1">{{ \$alamat ?? '-' }}</div>
                    </div>
                    @endforeach
                    @if(\$upload->pendaftaran->orangtua->nama_wali)
                    <div class="md:col-span-2 pt-4 border-t border-dashed border-slate-200">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Wali</span>
                        <div class="font-medium">{{ \$upload->pendaftaran->orangtua->nama_wali }}</div>
                        <div class="text-xs text-slate-500 mt-1">NIK: {{ \$upload->pendaftaran->orangtua->nik_wali ?? '-' }} &middot; HP: {{ \$upload->pendaftaran->orangtua->no_hp_wali ?? '-' }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
EOT;

$content = str_replace(
    "{{-- Informasi Tambahan (Custom Fields) --}}",
    $new_sections . "\n\n            {{-- Informasi Tambahan (Custom Fields) --}}",
    $content
);

file_put_contents("resources/views/opd/verifikasi/show.blade.php", $content);
echo "Done";
?>