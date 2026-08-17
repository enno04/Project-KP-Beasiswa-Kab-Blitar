<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Verifikasi Beasiswa</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f4f6f9; color: #333; }
        .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #2B5C92, #0C1446); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; letter-spacing: 0.5px; }
        .header p { margin: 8px 0 0; font-size: 13px; opacity: 0.85; }
        .content { padding: 30px; line-height: 1.7; }
        .status-badge { display: inline-block; padding: 10px 24px; border-radius: 8px; font-weight: bold; font-size: 16px; letter-spacing: 1px; margin: 16px 0; }
        .status-diterima { background: #d1fae5; color: #065f46; border: 2px solid #34d399; }
        .status-ditolak { background: #fee2e2; color: #991b1b; border: 2px solid #f87171; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table td { padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .info-table td:first-child { font-weight: 600; color: #6b7280; width: 40%; }
        .info-table td:last-child { color: #111827; }
        .catatan { background: #fefce8; border-left: 4px solid #facc15; padding: 14px 16px; border-radius: 6px; margin: 16px 0; font-size: 14px; }
        .footer { background: #f9fafb; padding: 20px 30px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
        .footer a { color: #2B5C92; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Beasiswa Blitar Mengabdi</h1>
            <p>Dinas Kepemudaan dan Olahraga Kabupaten Blitar</p>
        </div>

        <div class="content">
            <p>Yth. <strong>{{ $pendaftaran->identitas->nama_lengkap ?? 'Pendaftar' }}</strong>,</p>

            <p>Dengan ini kami sampaikan bahwa permohonan beasiswa Anda dengan nomor pendaftaran <strong>{{ $pendaftaran->nomor_pendaftaran }}</strong> telah selesai diverifikasi oleh Tim Seleksi Beasiswa Blitar Mengabdi.</p>

            <div style="text-align: center;">
                <span class="status-badge {{ $hasilVerifikasi === 'lulus' ? 'status-diterima' : 'status-ditolak' }}">
                    {{ $hasilVerifikasi === 'lulus' ? '✅ DITERIMA' : '❌ TIDAK DITERIMA' }}
                </span>
            </div>

            <table class="info-table">
                <tr>
                    <td>Nomor Pendaftaran</td>
                    <td>{{ $pendaftaran->nomor_pendaftaran }}</td>
                </tr>
                <tr>
                    <td>Jenis Beasiswa</td>
                    <td>
                        @php
                            $labelJenis = [
                                'sdss' => 'Satu Desa Satu Sarjana',
                                'berdaya_berjaya' => 'Berdaya Berjaya',
                                'bantuan_biaya' => 'Bantuan Biaya Pendidikan',
                            ];
                        @endphp
                        {{ $labelJenis[$pendaftaran->jenis_beasiswa] ?? $pendaftaran->jenis_beasiswa }}
                    </td>
                </tr>
                <tr>
                    <td>Total Skor Penilaian</td>
                    <td><strong>{{ $totalSkor }}</strong></td>
                </tr>
                <tr>
                    <td>Tanggal Verifikasi</td>
                    <td>{{ now()->format('d F Y') }}</td>
                </tr>
            </table>

            @if($catatan)
                <div class="catatan">
                    <strong>📝 Catatan Tim Seleksi:</strong><br>
                    {{ $catatan }}
                </div>
            @endif

            @if($hasilVerifikasi === 'lulus')
                <p>Selamat! Selanjutnya silakan mempersiapkan kelengkapan administrasi penyaluran beasiswa. Informasi lebih lanjut akan disampaikan melalui sekretariat Dinas Kepemudaan dan Olahraga Kabupaten Blitar.</p>
            @else
                <p>Kami menyampaikan bahwa keputusan ini bersifat final. Terima kasih atas partisipasi Anda dalam program Beasiswa Blitar Mengabdi.</p>
            @endif
        </div>

        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem. Harap tidak membalas email ini.</p>
            <p>
                <a href="https://maps.app.goo.gl/Wgz7JqscQjiqs348A">📍 Kantor Dispora Kab. Blitar</a> |
                Contact Person: Akhyat — 0813-3400-1600
            </p>
        </div>
    </div>
</body>
</html>
