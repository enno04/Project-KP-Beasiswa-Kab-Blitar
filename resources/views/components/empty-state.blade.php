@props(['icon' => 'inbox', 'title' => 'Tidak Ada Data', 'text' => 'Data yang Anda cari belum tersedia.', 'action' => null])

<div class="empty-state">
    <div class="empty-icon">
        <i data-lucide="{{ $icon }}" class="w-7 h-7"></i>
    </div>
    <p class="empty-title">{{ $title }}</p>
    <p class="empty-text">{{ $text }}</p>
    @if($action)
        <div class="mt-5">
            {{ $action }}
        </div>
    @endif
</div>
