<x-layouts.public :title="'FAQ & Bantuan — Beasiswa Blitar Mengabdi'">
    {{-- Page Hero --}}
    <section class="bg-cover bg-center bg-no-repeat border-b border-slate-200"
        style="background-image: url('{{ asset('images/informasi.png') }}');">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <p class="text-sm font-bold text-white uppercase tracking-widest mb-3">Pusat Bantuan</p>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">Frequently Asked Questions</h1>
            <p class="text-white mt-3 max-w-2xl mx-auto">Temukan jawaban cepat untuk pertanyaan-pertanyaan yang paling sering diajukan terkait Beasiswa Blitar Mengabdi di bawah ini.</p>
        </div>
    </section>

    <div class="bg-cover bg-center md:bg-fixed min-h-screen pt-12 pb-24" style="background-image: url('{{ asset('images/background.jpeg') }}')">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div x-data="{ activeAccordion: null }">
                <div class="space-y-4">
                    @forelse($faqs as $index => $faq)
                        <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden transition-all duration-300"
                            :class="activeAccordion === {{ $index }} ? 'ring-2 ring-primary-light border-transparent' : 'hover:border-slate-300'">
                            <button @click="activeAccordion = activeAccordion === {{ $index }} ? null : {{ $index }}"
                                class="w-full text-left px-6 py-5 flex items-center justify-between gap-4 focus:outline-none">
                                <span class="font-bold text-slate-800 text-sm sm:text-base leading-snug">{{ $faq->pertanyaan }}</span>
                                <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center shrink-0 transition-transform duration-300"
                                    :class="activeAccordion === {{ $index }} ? 'rotate-180 bg-primary-light text-primary' : 'text-slate-400'">
                                    <i data-lucide="chevron-down" class="w-5 h-5"></i>
                                </div>
                            </button>
                            
                            <div x-show="activeAccordion === {{ $index }}" 
                                 x-collapse 
                                 x-cloak>
                                <div class="px-6 pb-6 text-sm text-slate-600 leading-relaxed border-t border-slate-100 pt-4 whitespace-pre-line">
                                    {{ $faq->jawaban }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white rounded-2xl border border-slate-200 shadow-sm">
                            <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="message-square" class="w-8 h-8"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada FAQ</h3>
                            <p class="text-sm text-slate-500">Pertanyaan dan jawaban akan segera ditambahkan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            
            {{-- Contact Button --}}
            <div class="mt-12 text-center bg-white p-8 rounded-3xl border border-slate-200 shadow-sm">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="help-circle" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900 mb-2">Masih Punya Pertanyaan?</h3>
                <p class="text-sm text-slate-600 mb-6 max-w-md mx-auto">Jika Anda tidak menemukan jawaban yang Anda cari di atas, silakan hubungi tim kami.</p>
                <a href="{{ route('informasi') }}?scroll=bantuan" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-primary hover:bg-primary-dark text-white font-bold transition-all shadow-md">
                    <i data-lucide="phone" class="w-4 h-4"></i> Hubungi Call Center
                </a>
            </div>
        </div>
    </div>
</x-layouts.public>
