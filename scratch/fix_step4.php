<?php
$content = file_get_contents("resources/views/pendaftaran/create.blade.php");
// Fix the Informasi Tambahan step to be step 4 specifically
$tambahan_block = <<<EOT
                @if(\$hasTambahan)
                {{-- Step 4: Informasi Tambahan (Jika Ada) --}}
                <div x-show="step === {{ \$stepDokumen }}" x-transition.opacity.duration.300ms style="display: none;" class="card">
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
                
                <div x-show="step === {{ \$stepDokumen }}" class="mt-4 flex justify-between gap-3">
                    <button type="button" @click="step--; window.location.hash = 'step-' + step; window.scrollTo({ top: 0, behavior: 'smooth' })" class="btn bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 px-6 py-2.5 rounded-lg font-semibold transition-colors flex items-center gap-2 shadow-sm">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i> Sebelumnya
                    </button>
                    <button type="button" @click="nextStep()" class="btn btn-primary flex items-center gap-2 px-6 py-2.5 shadow-sm">
                        Selanjutnya <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </div>
                @endif
EOT;

$tambahan_block_fixed = str_replace("{{ \$stepDokumen }}", "4", $tambahan_block);
$content = str_replace($tambahan_block, $tambahan_block_fixed, $content);

// And fix the Edit button in the Review section for Informasi Tambahan
$edit_btn = <<<EOT
                                  <h3 class="font-bold text-lg text-amber-800">E. Informasi Tambahan</h3>
                                  <button type="button" @click="step = {{ \$stepDokumen }}; window.scrollTo({ top: 0, behavior: 'smooth' })"
EOT;
$edit_btn_fixed = str_replace("{{ \$stepDokumen }}", "4", $edit_btn);
$content = str_replace($edit_btn, $edit_btn_fixed, $content);

file_put_contents("resources/views/pendaftaran/create.blade.php", $content);
echo "Fixed!";
?>