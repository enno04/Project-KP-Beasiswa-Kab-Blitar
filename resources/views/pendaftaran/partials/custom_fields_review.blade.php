@foreach($customFields->where('penempatan', $penempatan) as $field)
    <div>
        <span class="text-gray-500 block text-xs">{{ $field->nama_field }}</span>
        <span class="font-medium" x-text="getFormVal('custom_fields[{{ $field->id }}]')"></span>
    </div>
@endforeach
