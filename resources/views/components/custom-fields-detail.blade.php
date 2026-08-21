@if($pendaftaran->customFieldAnswers->where('customField.penempatan', $penempatan)->count() > 0)
    <div class="md:col-span-2 mt-4 pt-4 border-t border-dashed border-slate-200">
        <h4 class="text-sm font-bold text-slate-800 mb-2">{{ $title ?? 'Informasi Tambahan' }}</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($pendaftaran->customFieldAnswers->where('customField.penempatan', $penempatan) as $answer)
            <div>
                <span class="block text-[11px] text-slate-400 font-bold uppercase tracking-wider mb-1">{{ $answer->customField->nama_field }}</span>
                <div class="font-medium text-sm text-slate-900">{{ $answer->jawaban ?? '-' }}</div>
            </div>
            @endforeach
        </div>
    </div>
@endif
