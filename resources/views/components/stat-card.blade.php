@props(['label', 'value', 'icon' => 'activity', 'color' => '#2B5C92', 'trend' => null])

<div class="stat-card group">
    <div class="flex items-center justify-between">
        <div>
            <p class="stat-label">{{ $label }}</p>
            <p class="stat-value" style="color: {{ $color }};">{{ is_numeric($value) ? number_format($value) : $value }}</p>
            @if($trend)
                <p class="text-xs mt-1.5 font-medium {{ $trend > 0 ? 'text-green-600' : 'text-red-500' }}">
                    {{ $trend > 0 ? '↑' : '↓' }} {{ abs($trend) }}%
                </p>
            @endif
        </div>
        <div class="stat-icon" style="background-color: {{ $color }}12; color: {{ $color }};">
            <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
        </div>
    </div>
</div>
