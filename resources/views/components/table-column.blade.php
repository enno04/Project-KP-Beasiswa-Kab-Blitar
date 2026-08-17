@props([
    'label',
    'sortable' => null, 
    'align' => 'left', // left, center, right
    'class' => ''
])

@php
    $alignment = match($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };

    $isSorted = request('sort') === $sortable;
    $currentDir = request('dir', 'asc');
    $nextDir = $isSorted && $currentDir === 'asc' ? 'desc' : 'asc';
    
    $url = $sortable 
        ? request()->fullUrlWithQuery(['sort' => $sortable, 'dir' => $nextDir])
        : '#';
@endphp

<th class="px-4 py-3 sticky top-0 bg-slate-50 z-10 shadow-[0_1px_0_0_#e2e8f0] font-semibold text-slate-700 whitespace-nowrap {{ $alignment }} {{ $class }}">
    @if($sortable)
        <a href="{{ $url }}" @click.prevent="confirmSort('{{ $url }}')" class="group inline-flex items-center gap-1 hover:text-primary transition-colors cursor-pointer w-full {{ $align === 'center' ? 'justify-center' : ($align === 'right' ? 'justify-end' : '') }}">
            {{ $label }}
            
            <span class="flex flex-col text-[8px] text-slate-300 opacity-50 group-hover:opacity-100 transition-opacity">
                <i data-lucide="chevron-up" class="w-3 h-3 -mb-1 {{ $isSorted && $currentDir === 'asc' ? 'text-primary !opacity-100' : '' }}"></i>
                <i data-lucide="chevron-down" class="w-3 h-3 {{ $isSorted && $currentDir === 'desc' ? 'text-primary !opacity-100' : '' }}"></i>
            </span>
        </a>
    @else
        {{ $label }}
    @endif
</th>
