                            </h4>
                            <div class="bg-blue-50 p-6 rounded-xl border border-blue-100 text-center">
                                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="info" class="w-6 h-6"></i>
                                </div>
                                <h5 class="font-bold text-blue-900 mb-1">Panduan Sedang Disusun</h5>
                                <p class="text-sm text-blue-700">Panduan alur kerja khusus untuk program Bantuan Pendidikan saat ini sedang dalam tahap penyusunan dan akan segera diperbarui.</p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Kolom Kanan: Fitur & Tips --}}
                    <div class="space-y-4">
                        <h4 class="font-extrabold text-slate-800 border-b border-slate-100 pb-3 flex items-center gap-2 mb-2 lg:mt-0 mt-6">
                            <span class="text-xl">💡</span> Fitur Tambahan
                        </h4>

                        <div class="bg-indigo-50/70 p-4 rounded-xl border border-indigo-100">
                            <h5 class="font-bold text-indigo-900 mb-1.5 flex items-center gap-2">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 text-indigo-600"></i> Pantau Status
                            </h5>
                            <p class="text-xs text-indigo-800 leading-relaxed">Melalui halaman Dashboard, Anda bisa melihat ringkasan status pendaftar dan progres penilaian yang sedang berjalan.</p>
                        </div>

                        <div class="bg-amber-50/70 p-4 rounded-xl border border-amber-100">
                            <h5 class="font-bold text-amber-900 mb-1.5 flex items-center gap-2">
                                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-amber-600"></i> Ekspor Data
                            </h5>
                            <p class="text-xs text-amber-800 leading-relaxed">Anda bisa mengunduh rekap pendaftar dalam format Excel untuk keperluan pelaporan melalui tombol Export Data.</p>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                            <h5 class="font-bold text-slate-700 mb-1.5 flex items-center gap-2">
                                <i data-lucide="monitor" class="w-4 h-4 text-slate-500"></i> Tip Perangkat
                            </h5>
                            <p class="text-xs text-slate-600 leading-relaxed">Sangat disarankan menggunakan <strong>Komputer/Laptop</strong> agar tampilan tabel data dan pratinjau dokumen terlihat lebih jelas dan mudah dioperasikan.</p>
                        </div>
                    </div>

                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-end">
                <button @click="showFaq = false" class="btn btn-outline bg-white font-medium shadow-sm">Tutup Panduan</button>
            </div>
        </div>
     </div>
</div>
@endif
