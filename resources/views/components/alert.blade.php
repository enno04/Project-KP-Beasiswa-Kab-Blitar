@props(['type' => 'info', 'dismissible' => false])

@php
    $config = match($type) {
        'success' => ['class' => 'alert-success', 'icon' => 'check-circle-2'],
        'danger', 'error' => ['class' => 'alert-danger', 'icon' => 'alert-circle'],
        'warning' => ['class' => 'alert-warning', 'icon' => 'alert-triangle'],
        default => ['class' => 'alert-info', 'icon' => 'info'],
    };
@endphp

<div {{ $attributes->merge(['class' => "alert {$config['class']}"]) }}
     @if($dismissible) x-data="{ show: true }" x-show="show" x-transition @endif>
    <i data-lucide="{{ $config['icon'] }}" class="w-5 h-5 shrink-0 mt-0.5"></i>
    <span class="flex-1">{{ $slot }}</span>
    @if($dismissible)
        <button @click="show = false" class="shrink-0 opacity-60 hover:opacity-100">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    @endif
</div>
