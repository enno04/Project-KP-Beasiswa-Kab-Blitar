@foreach($customFields->where('penempatan', $penempatan) as $field)
    <div class="sm:col-span-2">
        <label class="block text-sm font-semibold mb-2 text-gray-700">
            {{ $field->nama_field }} 
            @if($field->is_required) <span class="text-red-500">*</span> @endif
        </label>
        
        @if($field->tipe_field === 'textarea')
            <textarea name="custom_fields[{{ $field->id }}]" rows="3" 
                class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                {{ $field->is_required ? 'required' : '' }}>{{ old('custom_fields.'.$field->id) }}</textarea>
        @elseif($field->tipe_field === 'select' && $field->options)
            <select name="custom_fields[{{ $field->id }}]" 
                class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none bg-white"
                {{ $field->is_required ? 'required' : '' }}>
                <option value="">Pilih {{ $field->nama_field }}</option>
                @foreach($field->options as $opt)
                    <option value="{{ $opt }}" {{ old('custom_fields.'.$field->id) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        @else
            <input type="{{ $field->tipe_field === 'date' ? 'date' : ($field->tipe_field === 'number' ? 'number' : 'text') }}" 
                name="custom_fields[{{ $field->id }}]" 
                value="{{ old('custom_fields.'.$field->id) }}"
                class="w-full px-4 py-3 rounded-xl border text-sm focus:ring-2 focus:ring-primary outline-none"
                {{ $field->is_required ? 'required' : '' }}>
        @endif
    </div>
@endforeach
