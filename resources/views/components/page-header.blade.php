@props(['title', 'subtitle' => null, 'actions' => null])

<div class="page-header flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h1>{{ $title }}</h1>
        @if($subtitle)
            <p>{{ $subtitle }}</p>
        @endif
    </div>
    @if($actions)
        <div class="flex items-center gap-3 shrink-0">
            {{ $actions }}
        </div>
    @endif
</div>
