<?php
$content = file_get_contents("resources/views/pendaftaran/create.blade.php");

// 1. Add @php block at top
$content = str_replace(
    "<x-layouts.public :title=\"'Pendaftaran ' . \$program->nama\">\n",
    "<x-layouts.public :title=\"'Pendaftaran ' . \$program->nama\">\n    @php\n        \$hasTambahan = \$customFields->where('penempatan', 'tambahan')->count() > 0;\n        \$stepDokumen = \$hasTambahan ? 5 : 4;\n        \$stepReview = \$hasTambahan ? 6 : 5;\n        \$totalSteps = \$stepReview;\n    @endphp\n",
    $content
);

// 2. Fix progress bar Total steps
$content = str_replace(
    ":style=\"'width: ' + ((step - 1) / 4 * 100) + '%'\"></div>",
    ":style=\"'width: ' + ((step - 1) / ({{ \$totalSteps }} - 1) * 100) + '%'\"></div>",
    $content
);
$content = str_replace(
    "<template x-for=\"i in 5\" :key=\"i\">",
    "<template x-for=\"i in {{ \$totalSteps }}\" :key=\"i\">",
    $content
);
$content = str_replace(
    "Langkah <span x-text=\"step\"></span> dari 5</span>",
    "Langkah <span x-text=\"step\"></span> dari {{ \$totalSteps }}</span>",
    $content
);

// 3. Fix JavaScript stepNames
$content = str_replace(
    "stepNames: ['Identitas Diri', 'Identitas Keluarga', 'Kriteria Penilaian', 'Upload Dokumen', 'Review & Submit'],",
    "stepNames: {!! \$hasTambahan ? \"['Identitas Diri', 'Identitas Keluarga', 'Kriteria Penilaian', 'Informasi Tambahan', 'Upload Dokumen', 'Review & Submit']\" : \"['Identitas Diri', 'Identitas Keluarga', 'Kriteria Penilaian', 'Upload Dokumen', 'Review & Submit']\" !!},",
    $content
);

// 4. Extract "Informasi Tambahan" from Step 3 and turn into Step 4
$step3_tambahan_block = <<<EOT
                        {{-- Field Dinamis: Informasi Tambahan --}}
                        @if(\$customFields->where('penempatan', 'tambahan')->count() > 0)
                            <div class="mt-8 border-t pt-6">
                                <h3 class="font-bold text-lg text-gray-800 mb-4 flex items-center gap-2">
                                    <i data-lucide="info" class="w-5 h-5 text-amber-500"></i>
                                    Informasi Tambahan
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-amber-50/30 p-5 rounded-xl border border-amber-100">
                                    @include('pendaftaran.partials.custom_fields', ['penempatan' => 'tambahan'])
                                </div>
                            </div>
                        @endif
EOT;
$content = str_replace($step3_tambahan_block, "", $content);

$new_step_4 = <<<EOT
                @if(\$hasTambahan)
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
                    <button type="button" @click="step--; window.location.hash = 'step-' + step; window.scrollTo({ top: 0, behavior: 'smooth' })" class="btn bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 px-6 py-2.5 rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Sebelumnya
                    </button>
                    <button type="button" @click="nextStep()" class="btn btn-primary flex items-center gap-2 px-6 py-2.5 shadow-sm">
                        Selanjutnya <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </div>
                @endif

                {{-- Step Dokumen --}}
EOT;
$content = str_replace("{{-- Step 4: Dokumen --}}", $new_step_4, $content);

// 5. Replace step === 4 with step === {{ $stepDokumen }}
$content = str_replace(
    "<div x-show=\"step === 4\" x-transition.opacity.duration.300ms style=\"display: none;\" class=\"card\">",
    "<div x-show=\"step === {{ \$stepDokumen }}\" x-transition.opacity.duration.300ms style=\"display: none;\" class=\"card\">",
    $content
);
$content = str_replace(
    "<div x-show=\"step === 4\" class=\"mt-4 flex justify-between gap-3\">",
    "<div x-show=\"step === {{ \$stepDokumen }}\" class=\"mt-4 flex justify-between gap-3\">",
    $content
);

// 6. Replace step === 5 with step === {{ $stepReview }}
$content = str_replace(
    "{{-- Step 5: Review & Submit --}}",
    "{{-- Step Review & Submit --}}",
    $content
);
$content = str_replace(
    "<div x-show=\"step === 5\" x-transition.opacity.duration.300ms style=\"display: none;\" class=\"card max-w-3xl mx-auto\">",
    "<div x-show=\"step === {{ \$stepReview }}\" x-transition.opacity.duration.300ms style=\"display: none;\" class=\"card max-w-3xl mx-auto\">",
    $content
);
$content = str_replace(
    "<div x-show=\"step === 5\" class=\"mt-4 flex justify-between gap-3 max-w-3xl mx-auto\">",
    "<div x-show=\"step === {{ \$stepReview }}\" class=\"mt-4 flex justify-between gap-3 max-w-3xl mx-auto\">",
    $content
);

// 7. Update Review Summary buttons to use appropriate step values
$content = str_replace(
    "@click=\"step = 4; window.scrollTo({ top: 0, behavior: 'smooth' })\"",
    "@click=\"step = {{ \$stepDokumen }}; window.scrollTo({ top: 0, behavior: 'smooth' })\"",
    $content
);

// 8. JS Swal.fire
$content = str_replace(
    "if (this.step === 4) {",
    "if (this.step === {{ \$stepDokumen }}) {",
    $content
);

file_put_contents("resources/views/pendaftaran/create.blade.php", $content);
echo "Done";
?>