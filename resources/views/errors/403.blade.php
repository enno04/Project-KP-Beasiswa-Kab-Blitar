<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak (403)</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            text-align: center;
            padding: 3rem 2.5rem;
            background: rgba(30, 41, 59, 0.8);
            border-radius: 1.5rem;
            border: 1px solid rgba(100, 116, 139, 0.3);
            max-width: 480px;
            width: 90%;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
        }
        .icon {
            font-size: 4.5rem;
            margin-bottom: 1rem;
            color: #ef4444;
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        h1 {
            font-size: 1.75rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            color: #f8fafc;
            letter-spacing: -0.025em;
        }
        .error-code {
            display: inline-block;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        p {
            color: #94a3b8;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .btn-group {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            background: #2B5C92;
            color: white;
            text-decoration: none;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        a:hover { 
            background: #1e446d; 
            transform: translateY(-2px);
        }
        .btn-outline {
            background: transparent;
            border: 1px solid #64748b;
            color: #e2e8f0;
        }
        .btn-outline:hover {
            background: rgba(100, 116, 139, 0.1);
            border-color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🛑</div>
        <div class="error-code">Error 403</div>
        <h1>Akses Ditolak</h1>
        <p>Maaf, Anda tidak memiliki izin atau akses (*role*) yang diperlukan untuk melihat halaman ini. Silakan kembali ke halaman sebelumnya atau ke beranda.</p>
        
        <div class="btn-group">
            <a href="javascript:history.back()" class="btn-outline">
                Kembali
            </a>
            <a href="{{ route('home') }}">
                Beranda Utama
            </a>
        </div>
    </div>
</body>
</html>
