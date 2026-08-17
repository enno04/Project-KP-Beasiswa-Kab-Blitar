<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk Administrator — Beasiswa Blitar Mengabdi</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-kab-blitar.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-kab-blitar.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen antialiased flex items-center justify-center p-4 sm:p-6" style="background: radial-gradient(circle at top, #f8fafc 0%, #e2e8f0 100%);">

    <div class="w-full max-w-md py-6">
        {{-- Single Card Container --}}
        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8 sm:p-10 relative overflow-hidden">
            {{-- Top Accent Line --}}
            <div class="absolute top-0 left-0 right-0 h-1.5" style="background: linear-gradient(to right, #2B5C92, #FFD800);"></div>

            {{-- Content Slot --}}
            {{ $slot }}
        </div>

        {{-- Copyright Footer --}}
        <p class="text-center text-xs font-semibold text-slate-400 mt-6">
            &copy; {{ date('Y') }} Beasiswa Blitar Mengabdi — Dispora Kab. Blitar
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
