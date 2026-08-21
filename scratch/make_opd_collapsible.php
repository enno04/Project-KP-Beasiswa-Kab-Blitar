<?php
$content = file_get_contents("resources/views/opd/verifikasi/show.blade.php");

// 1. Informasi Pendaftar
$content = str_replace(
    '<div class="card">
                <div class="card-header font-bold">Informasi Pendaftar</div>
                <div class="card-body space-y-3 text-sm">',
    '<div class="card" x-data="{ open: true }">
                <div @click="open = !open" class="card-header font-bold cursor-pointer hover:bg-slate-50 transition-colors flex justify-between items-center">
                    <span>Informasi Pendaftar</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="open ? \'rotate-180\' : \'\'"></i>
                </div>
                <div x-show="open" x-transition.duration.300ms>
                <div class="card-body space-y-3 text-sm">',
    $content
);
$content = str_replace(
    '                    </div>
                </div>
            </div>

            {{-- Data Akademik --}}',
    '                    </div>
                </div>
                </div>
            </div>

            {{-- Data Akademik --}}',
    $content
);

// 2. Data Akademik
$content = str_replace(
    '<div class="card">
                <div class="card-header font-bold">Data Akademik</div>
                <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">',
    '<div class="card" x-data="{ open: false }">
                <div @click="open = !open" class="card-header font-bold cursor-pointer hover:bg-slate-50 transition-colors flex justify-between items-center">
                    <span>Data Akademik</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="open ? \'rotate-180\' : \'\'"></i>
                </div>
                <div x-show="open" x-transition.duration.300ms style="display: none;">
                <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">',
    $content
);
$content = str_replace(
    '                    </div>
                </div>
            </div>

            {{-- Data Orang Tua / Wali --}}',
    '                    </div>
                </div>
                </div>
            </div>

            {{-- Data Orang Tua / Wali --}}',
    $content
);

// 3. Data Orang Tua / Wali
$content = str_replace(
    '<div class="card">
                <div class="card-header font-bold">Data Orang Tua / Wali</div>
                <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">',
    '<div class="card" x-data="{ open: false }">
                <div @click="open = !open" class="card-header font-bold cursor-pointer hover:bg-slate-50 transition-colors flex justify-between items-center">
                    <span>Data Orang Tua / Wali</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="open ? \'rotate-180\' : \'\'"></i>
                </div>
                <div x-show="open" x-transition.duration.300ms style="display: none;">
                <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">',
    $content
);
$content = str_replace(
    '                    @endif
                </div>
            </div>
            @endif

            {{-- Kriteria Penilaian & Ranking --}}',
    '                    @endif
                </div>
                </div>
            </div>
            @endif

            {{-- Kriteria Penilaian & Ranking --}}',
    $content
);

// 4. Kriteria Penilaian (Ranking)
$content = str_replace(
    '<div class="card overflow-hidden">
                <div class="card-header font-bold text-slate-700 bg-slate-50 flex items-center gap-2">
                    <i data-lucide="calculator" class="w-4 h-4 text-slate-400"></i>
                    Penilaian & Ranking
                </div>
                <div class="card-body">',
    '<div class="card overflow-hidden" x-data="{ open: false }">
                <div @click="open = !open" class="card-header font-bold text-slate-700 bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors flex justify-between items-center">
                    <span class="flex items-center gap-2"><i data-lucide="calculator" class="w-4 h-4 text-slate-400"></i> Penilaian & Ranking</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="open ? \'rotate-180\' : \'\'"></i>
                </div>
                <div x-show="open" x-transition.duration.300ms style="display: none;">
                <div class="card-body">',
    $content
);
$content = str_replace(
    '                    </table>
                </div>
            </div>
            @else',
    '                    </table>
                </div>
                </div>
            </div>
            @else',
    $content
);

// 5. Kriteria Penilaian (Jawaban)
$content = str_replace(
    '<div class="card overflow-hidden">
                <div class="card-header font-bold text-slate-700 bg-slate-50 flex items-center gap-2">
                    <i data-lucide="list-checks" class="w-4 h-4 text-slate-400"></i>
                    Input Jawaban Kriteria
                </div>
                <table class="w-full text-left text-sm border-collapse">',
    '<div class="card overflow-hidden" x-data="{ open: false }">
                <div @click="open = !open" class="card-header font-bold text-slate-700 bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors flex justify-between items-center">
                    <span class="flex items-center gap-2"><i data-lucide="list-checks" class="w-4 h-4 text-slate-400"></i> Input Jawaban Kriteria</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="open ? \'rotate-180\' : \'\'"></i>
                </div>
                <div x-show="open" x-transition.duration.300ms style="display: none;">
                <table class="w-full text-left text-sm border-collapse">',
    $content
);
$content = str_replace(
    '                    </tbody>
                </table>
            </div>
            @endif

            {{-- Informasi Tambahan (Custom Fields) --}}',
    '                    </tbody>
                </table>
                </div>
            </div>
            @endif

            {{-- Informasi Tambahan (Custom Fields) --}}',
    $content
);

// 6. Informasi Tambahan
$content = str_replace(
    '<div class="card">
                <div class="card-header font-bold text-slate-700 bg-slate-50">Informasi Tambahan</div>
                <div class="card-body space-y-3 text-sm">',
    '<div class="card" x-data="{ open: false }">
                <div @click="open = !open" class="card-header font-bold text-slate-700 bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors flex justify-between items-center">
                    <span>Informasi Tambahan</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="open ? \'rotate-180\' : \'\'"></i>
                </div>
                <div x-show="open" x-transition.duration.300ms style="display: none;">
                <div class="card-body space-y-3 text-sm">',
    $content
);
$content = str_replace(
    '                    @endforeach
                </div>
            </div>
            @endif

            {{-- Riwayat --}}',
    '                    @endforeach
                </div>
                </div>
            </div>
            @endif

            {{-- Riwayat --}}',
    $content
);

// 7. Riwayat Pemeriksaan
$content = str_replace(
    '<div class="card">
                <div class="card-header font-bold">Riwayat Pemeriksaan</div>
                <div class="card-body space-y-4">',
    '<div class="card" x-data="{ open: false }">
                <div @click="open = !open" class="card-header font-bold cursor-pointer hover:bg-slate-50 transition-colors flex justify-between items-center">
                    <span>Riwayat Pemeriksaan</span>
                    <i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-300" :class="open ? \'rotate-180\' : \'\'"></i>
                </div>
                <div x-show="open" x-transition.duration.300ms style="display: none;">
                <div class="card-body space-y-4">',
    $content
);
$content = str_replace(
    '                    @endforeach
                </div>
            </div>
            @endif

            {{-- Form Verifikasi --}}',
    '                    @endforeach
                </div>
                </div>
            </div>
            @endif

            {{-- Form Verifikasi --}}',
    $content
);

file_put_contents("resources/views/opd/verifikasi/show.blade.php", $content);
echo "Done";
?>
