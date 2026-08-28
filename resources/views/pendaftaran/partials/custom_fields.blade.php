@foreach($customFields->where('penempatan', $penempatan) as $field)
    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold mb-2 text-gray-700">
            {{ $field->nama_field }} 
            @if($field->is_required) <span class="text-red-500">*</span> @endif
        </label>
        
        @if($field->tipe_field === 'textarea')
            <textarea name="custom_fields[{{ $field->id }}]" rows="3" data-nama="{{ $field->nama_field }}"
                class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                {{ $field->is_required ? 'required' : '' }}>{{ old('custom_fields.'.$field->id) }}</textarea>
        @elseif($field->tipe_field === 'select' && $field->options)
            <select name="custom_fields[{{ $field->id }}]" data-nama="{{ $field->nama_field }}"
                class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none bg-white"
                {{ $field->is_required ? 'required' : '' }}>
                <option value="">Pilih {{ $field->nama_field }}</option>
                @foreach($field->options as $opt)
                    <option value="{{ $opt }}" {{ old('custom_fields.'.$field->id) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        @elseif($field->tipe_field === 'rupiah')
            <div x-data="{ 
                val: '{{ old('custom_fields.'.$field->id) }}',
                formatRupiah(value) {
                    let number_string = value.replace(/[^,\d]/g, '').toString(),
                        split = number_string.split(','),
                        sisa = split[0].length % 3,
                        rupiah = split[0].substr(0, sisa),
                        ribuan = split[0].substr(sisa).match(/\d{3}/gi);
                    if (ribuan) {
                        let separator = sisa ? '.' : '';
                        rupiah += separator + ribuan.join('.');
                    }
                    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
                    return rupiah ? 'Rp. ' + rupiah : '';
                }
            }" x-init="val = formatRupiah(val)">
                <input type="text" 
                    name="custom_fields[{{ $field->id }}]" data-nama="{{ $field->nama_field }}"
                    x-model="val"
                    @input="val = formatRupiah($event.target.value)"
                    class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                    placeholder="Rp. 0"
                    {{ $field->is_required ? 'required' : '' }}>
                <p class="text-xs text-slate-500 mt-1.5"><i data-lucide="info" class="w-3 h-3 inline-block mr-0.5"></i> Cukup ketikkan angka, format <b>Rp.</b> dan titik akan menyesuaikan otomatis.</p>
            </div>
        @else
            <input type="{{ $field->tipe_field === 'date' ? 'date' : ($field->tipe_field === 'number' ? 'number' : 'text') }}" 
                name="custom_fields[{{ $field->id }}]" data-nama="{{ $field->nama_field }}"
                value="{{ old('custom_fields.'.$field->id) }}"
                class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                {{ $field->is_required ? 'required' : '' }}>
        @endif
    </div>
@endforeach
