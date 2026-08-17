<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\KategoriBeasiswa;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman Laporan Rekapitulasi
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $kategoriList = KategoriBeasiswa::all();
        
        $query = Pendaftaran::with(['desa', 'kecamatan', 'kategori']);

        // Scope berdasarkan role
        if ($user->role === 'admin_desa') {
            $query->where('desa_id', $user->desa_id);
        } elseif ($user->role === 'admin_kecamatan') {
            $query->where('kecamatan_id', $user->kecamatan_id);
        }

        // Filter
        if ($request->has('kategori_id') && $request->kategori_id != '') {
            $query->where('kategori_id', $request->kategori_id);
        }
        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('tahun', $request->tahun);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $pendaftar = $query->latest()->paginate(20)->withQueryString();

        return view('laporan.index', compact('pendaftar', 'kategoriList'));
    }

    /**
     * Ekspor data ke CSV
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $query = Pendaftaran::with(['desa', 'kecamatan', 'kategori']);

        if ($user->role === 'admin_desa') {
            $query->where('desa_id', $user->desa_id);
        } elseif ($user->role === 'admin_kecamatan') {
            $query->where('kecamatan_id', $user->kecamatan_id);
        }

        if ($request->has('kategori_id') && $request->kategori_id != '') {
            $query->where('kategori_id', $request->kategori_id);
        }
        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('tahun', $request->tahun);
        }
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $pendaftars = $query->orderBy('kategori_id')->orderBy('ranking')->get();

        $filename = "laporan_beasiswa_" . date('Ymd_His') . ".csv";

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = [
            'No', 'Tahun', 'Kategori Beasiswa', 'Nomor Pendaftaran', 'NIK', 'Nama Lengkap', 
            'Desa', 'Kecamatan', 'Asal Sekolah/PT', 'Total Nilai', 'Peringkat', 'Status'
        ];

        $callback = function() use($pendaftars, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $no = 1;
            foreach ($pendaftars as $p) {
                $row['No'] = $no++;
                $row['Tahun'] = $p->tahun;
                $row['Kategori Beasiswa'] = $p->kategori->nama ?? '-';
                $row['Nomor Pendaftaran'] = $p->nomor_pendaftaran;
                $row['NIK'] = "'" . $p->nik; // Prevent Excel from scientific notation
                $row['Nama Lengkap'] = $p->nama_lengkap;
                $row['Desa'] = $p->desa->nama_desa ?? '-';
                $row['Kecamatan'] = $p->kecamatan->nama_kecamatan ?? '-';
                $row['Asal Sekolah/PT'] = $p->nama_sekolah ?? $p->asal_perguruan_tinggi ?? '-';
                $row['Total Nilai'] = $p->total_nilai ?? 0;
                $row['Peringkat'] = $p->ranking ?? '-';
                $row['Status'] = $p->status_label;

                fputcsv($file, array(
                    $row['No'], $row['Tahun'], $row['Kategori Beasiswa'], $row['Nomor Pendaftaran'],
                    $row['NIK'], $row['Nama Lengkap'], $row['Desa'], $row['Kecamatan'], 
                    $row['Asal Sekolah/PT'], $row['Total Nilai'], $row['Peringkat'], $row['Status']
                ));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
