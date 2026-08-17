@props(['type' => 'muted', 'label' => '', 'dot' => false])

@php
    $classes = match($type) {
        'primary' => 'badge-primary',
        'success' => 'badge-success',
        'danger' => 'badge-danger',
        'warning' => 'badge-warning',
        'info' => 'badge-info',
        'accent' => 'badge-accent',
        default => 'badge-muted',
    };
    if ($dot) $classes .= ' badge-dot';
@endphp

<span {{ $attributes->merge(['class' => "badge $classes"]) }}>
    {{ $label ?: $slot }}
</span>
