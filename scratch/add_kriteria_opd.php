<?php
$content = file_get_contents("resources/views/opd/verifikasi/show.blade.php");

$kriteria_section = <<<EOT
            {{-- Kriteria Penilaian & Ranking --}}
            @if(\$upload->pendaftaran->penilaians->isNotEmpty())
            <div class="card overflow-hidden">
                <div class="card-header font-bold text-slate-700 bg-slate-50 flex items-center gap-2">
                    <i data-lucide="calculator" class="w-4 h-4 text-slate-400"></i>
                    Penilaian & Ranking
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="text-center p-4 bg-primary-light rounded-xl">
                            <div class="text-xs font-bold text-slate-500 uppercase">Total Nilai</div>
                            <div class="text-xl font-extrabold text-primary-dark">{{ number_format(\$upload->pendaftaran->total_nilai, 4) }}</div>
                        </div>
                        <div class="text-center p-4 bg-amber-50 rounded-xl">
                            <div class="text-xs font-bold text-slate-500 uppercase">Ranking Desa</div>
                            <div class="text-xl font-extrabold text-amber-700">#{{ \$upload->pendaftaran->ranking ?? '-' }}</div>
                        </div>
                    </div>
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="py-2 text-slate-500 font-bold">Kriteria</th>
                                <th class="py-2 text-right text-slate-500 font-bold">Skor</th>
                                <th class="py-2 text-right text-slate-500 font-bold">Nilai Terbobot</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach(\$upload->pendaftaran->penilaians as \$penilaian)
                            <tr>
                                <td class="py-2 font-medium">{{ \$penilaian->kriteria->nama ?? '-' }}</td>
                                <td class="py-2 text-right">{{ number_format(\$penilaian->skor, 2) }}</td>
                                <td class="py-2 text-right font-semibold text-primary-dark">{{ number_format(\$penilaian->nilai_terbobot, 4) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="card overflow-hidden">
                <div class="card-header font-bold text-slate-700 bg-slate-50 flex items-center gap-2">
                    <i data-lucide="list-checks" class="w-4 h-4 text-slate-400"></i>
                    Input Jawaban Kriteria
                </div>
                <table class="w-full text-left text-sm border-collapse">
                    <tbody class="divide-y divide-slate-100">
                        @forelse(\$upload->pendaftaran->jawabanKriterias as \$jawaban)
                        <tr>
                            <td class="py-3 px-4 w-1/2">
                                <div class="font-semibold text-slate-700">{{ \$jawaban->kriteria->nama }}</div>
                                <div class="text-xs text-slate-400">{{ \$jawaban->kriteria->kelompokKriteria->nama ?? '' }}</div>
                            </td>
                            <td class="py-3 px-4">
                                @if(\$jawaban->kriteria->tipe_input === 'pilihan')
                                    <span class="font-medium">{{ \$jawaban->pilihanKriteria->label ?? '-' }}</span>
                                @else
                                    <span class="font-medium font-mono text-primary">{{ \$jawaban->nilai_input ?? '-' }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-slate-400 py-4 text-sm italic">Belum ada data nilai kriteria.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Informasi Tambahan (Custom Fields) --}}
EOT;

$content = str_replace(
    "{{-- Informasi Tambahan (Custom Fields) --}}",
    $kriteria_section,
    $content
);

file_put_contents("resources/views/opd/verifikasi/show.blade.php", $content);
echo "Done";
?>