<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Kedaluwarsa</title>
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
            padding: 2.5rem;
            background: rgba(30, 41, 59, 0.8);
            border-radius: 1rem;
            border: 1px solid rgba(100, 116, 139, 0.3);
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #f8fafc;
        }
        p {
            color: #94a3b8;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        a {
            display: inline-block;
            margin-top: 1rem;
            padding: 0.6rem 1.5rem;
            background: #6366f1;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        a:hover { background: #4f46e5; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">⚠️</div>
        <h1>Sesi Kedaluwarsa</h1>
        <p>Token keamanan halaman ini sudah tidak berlaku. Silakan kembali ke halaman sebelumnya dan coba lagi.</p>
        <br>
        <a href="{{ url()->previous() ?: url('/') }}">Kembali Sekarang</a>
    </div>
</body>
</html>
