<?php
$file = 'd:/KodingProject/Project-KP-Beasiswa-Kab-Blitar/resources/views/pendaftaran/create.blade.php';
$content = file_get_contents($file);

// Add PHP block at the very top, just after <x-layouts.public...>
$phpBlock = <<<HTML
@php
    \$hasTambahan = \$customFields->where('penempatan', 'tambahan')->count() > 0;
    \$stepTambahan = \$hasTambahan ? 4 : null;
    \$stepDokumen = \$hasTambahan ? 5 : 4;
    \$stepReview = \$hasTambahan ? 6 : 5;
    \$totalSteps = \$hasTambahan ? 6 : 5;
@endphp
HTML;

if (strpos($content, '$hasTambahan = $customFields') === false) {
    $content = preg_replace('/(<x-layouts\.public.*?>)/s', "$1\n" . $phpBlock, $content);
}

// 1. Progress Bar width
$content = preg_replace(
    "/:style=\"'width: ' \+ \(\(step - 1\) \/ \d+ \* 100\) \+ '%'\"/",
    ":style=\"'width: ' + ((step - 1) / {{ \$totalSteps - 1 }} * 100) + '%'\"",
    $content
);

// 2. x-for="i in 6" -> x-for="i in {{ $totalSteps }}"
$content = preg_replace('/x-for="i in \d+"/', 'x-for="i in {{ $totalSteps }}"', $content);

// 3. dari 6</span> -> dari {{ $totalSteps }}</span>
$content = preg_replace('/dari \d+<\/span>/', 'dari {{ $totalSteps }}</span>', $content);

// 4. stepNames in x-data
$stepNames6 = "['Identitas Diri', 'Identitas Keluarga', 'Kriteria Penilaian', 'Informasi Tambahan', 'Upload Dokumen', 'Review & Submit']";
$stepNames5 = "['Identitas Diri', 'Identitas & Keluarga', 'Kriteria Penilaian', 'Upload Dokumen', 'Review & Submit']";
$content = preg_replace(
    "/stepNames:\s*\[.*?\]/s",
    "stepNames: {!! \$hasTambahan ? \"$stepNames6\" : \"$stepNames5\" !!}",
    $content
);

// 5. Replace x-show for Step 4 (Informasi Tambahan) and wrap with @if($hasTambahan)
// We need to be careful here. I will just search and replace the blocks manually since regex is risky.

// For Informasi Tambahan block
$infoTambahanSearch = <<<HTML
                {{-- Step 4: Informasi Tambahan --}}
                <div x-show="step === 4" x-transition.opacity.duration.300ms style="display: none;" class="card">
HTML;
$infoTambahanReplace = <<<HTML
                {{-- Step Dinamis: Informasi Tambahan --}}
                @if(\$hasTambahan)
                <div x-show="step === {{ \$stepTambahan }}" x-transition.opacity.duration.300ms style="display: none;" class="card">
HTML;
$content = str_replace($infoTambahanSearch, $infoTambahanReplace, $content);

// And its buttons
$infoTambahanBtnSearch = <<<HTML
                <div x-show="step === 4" class="mt-4 flex justify-between gap-3">
                    <button type="button" @click="history.back()" class="btn bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 px-6 py-2.5 rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Sebelumnya
                    </button>
                    <button type="button" @click="nextStep()" class="btn btn-primary flex items-center gap-2 px-6 py-2.5 shadow-sm">
                        <span class="flex items-center gap-1">Selanjutnya <i data-lucide="arrow-right" class="w-4 h-4"></i></span>
                    </button>
                </div>
HTML;
$infoTambahanBtnReplace = <<<HTML
                <div x-show="step === {{ \$stepTambahan }}" class="mt-4 flex justify-between gap-3">
                    <button type="button" @click="history.back()" class="btn bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 px-6 py-2.5 rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Sebelumnya
                    </button>
                    <button type="button" @click="nextStep()" class="btn btn-primary flex items-center gap-2 px-6 py-2.5 shadow-sm">
                        <span class="flex items-center gap-1">Selanjutnya <i data-lucide="arrow-right" class="w-4 h-4"></i></span>
                    </button>
                </div>
                @endif
HTML;
$content = str_replace($infoTambahanBtnSearch, $infoTambahanBtnReplace, $content);

// For Dokumen block
$dokumenSearch = <<<HTML
                {{-- Step 5: Dokumen --}}
                <div x-show="step === 5" x-transition.opacity.duration.300ms style="display: none;" class="card">
HTML;
$dokumenReplace = <<<HTML
                {{-- Step Dinamis: Dokumen --}}
                <div x-show="step === {{ \$stepDokumen }}" x-transition.opacity.duration.300ms style="display: none;" class="card">
HTML;
$content = str_replace($dokumenSearch, $dokumenReplace, $content);

$dokumenBtnSearch = <<<HTML
                <div x-show="step === 5" class="mt-4 flex justify-between gap-3">
HTML;
$dokumenBtnReplace = <<<HTML
                <div x-show="step === {{ \$stepDokumen }}" class="mt-4 flex justify-between gap-3">
HTML;
$content = str_replace($dokumenBtnSearch, $dokumenBtnReplace, $content);

// For Review block
$reviewSearch = <<<HTML
        {{-- Step 6: Review & Submit --}}
        <div x-show="step === 6" x-transition.opacity.duration.300ms style="display: none;" class="card max-w-3xl mx-auto">
HTML;
$reviewReplace = <<<HTML
        {{-- Step Dinamis: Review & Submit --}}
        <div x-show="step === {{ \$stepReview }}" x-transition.opacity.duration.300ms style="display: none;" class="card max-w-3xl mx-auto">
HTML;
$content = str_replace($reviewSearch, $reviewReplace, $content);

$reviewBtnSearch = <<<HTML
        <div x-show="step === 6" class="mt-4 flex justify-between gap-3 max-w-3xl mx-auto">
HTML;
$reviewBtnReplace = <<<HTML
        <div x-show="step === {{ \$stepReview }}" class="mt-4 flex justify-between gap-3 max-w-3xl mx-auto">
HTML;
$content = str_replace($reviewBtnSearch, $reviewBtnReplace, $content);

// 6. Javascript validation logic
$content = preg_replace('/if \(this\.step === 5\) {/', 'if (this.step === {{ $stepDokumen }}) {', $content);

$jsFallbackSearch = <<<HTML
                                    if (showAttr.includes('1')) this.step = 1;
                                    else if (showAttr.includes('2')) this.step = 2;
                                    else if (showAttr.includes('3')) this.step = 3;
                                    else if (showAttr.includes('4')) this.step = 4;
                                    else if (showAttr.includes('5')) this.step = 5;
                                    else if (showAttr.includes('6')) this.step = 6;
HTML;
$jsFallbackReplace = <<<HTML
                                    if (showAttr.includes('1')) this.step = 1;
                                    else if (showAttr.includes('2')) this.step = 2;
                                    else if (showAttr.includes('3')) this.step = 3;
                                    @if(\$hasTambahan)
                                    else if (showAttr.includes('{{ \$stepTambahan }}')) this.step = {{ \$stepTambahan }};
                                    @endif
                                    else if (showAttr.includes('{{ \$stepDokumen }}')) this.step = {{ \$stepDokumen }};
                                    else if (showAttr.includes('{{ \$stepReview }}')) this.step = {{ \$stepReview }};
HTML;
$content = str_replace($jsFallbackSearch, $jsFallbackReplace, $content);

// 7. Buttons in Review Step
// Button for Informasi Tambahan
$btnTambahanSearch = <<<HTML
                                <h3 class="font-bold text-lg text-amber-800">E. Informasi Tambahan</h3>
                                <button type="button" @click="step = 4; window.scrollTo({ top: 0, behavior: 'smooth' })"
HTML;
$btnTambahanReplace = <<<HTML
                                <h3 class="font-bold text-lg text-amber-800">E. Informasi Tambahan</h3>
                                <button type="button" @click="step = {{ \$stepTambahan }}; window.scrollTo({ top: 0, behavior: 'smooth' })"
HTML;
$content = str_replace($btnTambahanSearch, $btnTambahanReplace, $content);

// Button for Dokumen
$btnDokumenSearch = <<<HTML
                              <h3 class="font-bold text-lg text-gray-800">F. Dokumen</h3>
                              <button type="button" @click="step = 5; window.scrollTo({ top: 0, behavior: 'smooth' })"
HTML;
$btnDokumenReplace = <<<HTML
                              <h3 class="font-bold text-lg text-gray-800">{{ \$hasTambahan ? 'F' : 'E' }}. Dokumen</h3>
                              <button type="button" @click="step = {{ \$stepDokumen }}; window.scrollTo({ top: 0, behavior: 'smooth' })"
HTML;
$content = str_replace($btnDokumenSearch, $btnDokumenReplace, $content);

file_put_contents($file, $content);
echo "Modification complete.\n";
