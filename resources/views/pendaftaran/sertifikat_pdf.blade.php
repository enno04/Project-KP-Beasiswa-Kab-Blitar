<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Lulus Beasiswa</title>
    <style>
        @page { margin: 40px; }
        body { margin: 0; font-family: 'Helvetica', 'Arial', sans-serif; background-color: #ffffff; color: #1a202c; font-size: 14px; }
        .container {
            width: 100%; height: 100%; padding: 20px; box-sizing: border-box; text-align: left;
        }
        .header { margin-top: 10px; margin-bottom: 30px; text-align: center; border-bottom: 2px solid #1a202c; padding-bottom: 10px; }
        .title { font-size: 24px; font-weight: bold; color: #1e3a8a; text-transform: uppercase; margin: 0; }
        .subtitle { font-size: 16px; color: #475569; margin-top: 5px; }
        
        .content { margin-top: 40px; line-height: 1.6; }
        .data-table { width: 100%; margin-top: 20px; margin-bottom: 30px; border-collapse: collapse; }
        .data-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; }
        .data-table td.label { width: 30%; font-weight: bold; color: #475569; }
        .data-table td.value { width: 70%; font-weight: bold; }
        
        .footer { margin-top: 50px; width: 100%; display: table; }
        .footer-left { display: table-cell; text-align: left; vertical-align: top; width: 30%; }
        .footer-center { display: table-cell; text-align: center; vertical-align: top; width: 40%; }
        .footer-right { display: table-cell; text-align: right; vertical-align: top; width: 30%; }
        .signature-line { border-bottom: 1px solid #1a202c; width: 200px; margin-bottom: 5px; display: inline-block; }
        .signature-title { font-size: 14px; font-weight: bold; color: #334155; }
        .qr-code { width: 120px; height: 120px; margin-bottom: 5px; }
        .qr-text { font-size: 10px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Surat Keterangan Penerima Beasiswa</h1>
            <p class="subtitle">PEMERINTAH KABUPATEN BLITAR</p>
        </div>

        <div class="content">
            <p>Dengan ini menerangkan bahwa pendaftar di bawah ini:</p>
            
            <table class="data-table">
                <tr>
                    <td class="label">Nama Lengkap</td>
                    <td class="value">{{ strtoupper($pendaftaran->nama_lengkap) }}</td>
                </tr>
                <tr>
                    <td class="label">Nomor Registrasi</td>
                    <td class="value">{{ $pendaftaran->nomor_pendaftaran }}</td>
                </tr>
                <tr>
                    <td class="label">Asal Perguruan Tinggi</td>
                    <td class="value">{{ $pendaftaran->asal_perguruan_tinggi ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Program Beasiswa</td>
                    <td class="value">{{ $pendaftaran->program->nama ?? $pendaftaran->jenis_beasiswa }}</td>
                </tr>
                <tr>
                    <td class="label">Tahun Anggaran</td>
                    <td class="value">{{ $pendaftaran->periode->tahun ?? $pendaftaran->tahun }}</td>
                </tr>
            </table>

            <p>
                Telah ditetapkan sebagai <b>Penerima Beasiswa Kabupaten Blitar Tahun {{ $pendaftaran->periode->tahun ?? $pendaftaran->tahun }}</b> yang sah. 
                Dokumen ini merupakan bukti validasi digital hasil pendaftaran online beasiswa.
            </p>
        </div>

        <div class="footer">
            <div class="footer-left">
                <img src="data:image/svg+xml;base64,{{ $qrCode }}" class="qr-code"><br>
                <span class="qr-text">Scan QR Code untuk Validasi Keaslian</span>
            </div>
            <div class="footer-center">
            </div>
            <div class="footer-right">
                <div style="font-size: 14px; margin-bottom: 60px; color:#475569">
                    Diterbitkan secara elektronik di Blitar, <br>
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </div>
                <div class="signature-line"></div><br>
                <span class="signature-title">Pemerintah Kabupaten Blitar</span>
            </div>
        </div>
    </div>
</body>
</html>
