@props([
    'label' => '',
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => '',
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
    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        class="form-input @error($name) !border-red-400 !bg-red-50/50 @enderror"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except('class') }}
    >
    @if($helper && !$errors->has($name))
        <p class="text-xs text-slate-400 mt-1.5">{{ $helper }}</p>
    @endif
    @error($name)
        <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
            <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
        </p>
    @enderror
</div>
