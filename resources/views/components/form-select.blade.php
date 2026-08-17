@props([
    'label' => '',
    'name',
    'options' => [],
    'value' => null,
    'placeholder' => '— Pilih —',
    'required' => false,
    'helper' => null,
    'disabled' => false,
])

<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required) <span class="required">*</span> @endif
        </label>
    @endif
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        class="form-select @error($name) !border-red-400 !bg-red-50/50 @enderror"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except('class') }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @if(is_array($options) && count($options) > 0)
            @foreach($options as $optVal => $optLabel)
                <option value="{{ $optVal }}" {{ old($name, $value) == $optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
            @endforeach
        @endif
        {{ $slot }}
    </select>
    @if($helper && !$errors->has($name))
        <p class="text-xs text-slate-400 mt-1.5">{{ $helper }}</p>
    @endif
    @error($name)
        <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
            <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
        </p>
    @enderror
</div>
