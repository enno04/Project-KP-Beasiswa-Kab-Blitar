<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportHistoriRequest;
use App\Models\User;
use App\Models\Opd;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\Pendaftaran;
use App\Models\Periode;
use App\Models\Program;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    /**
     * Dashboard Super Admin — kondisi keseluruhan sistem.
     */
    public function dashboard()
    {
        $stats = [
            'total_user' => User::count(),
            'total_opd' => Opd::count(),
            'total_kecamatan' => Kecamatan::count(),
            'total_desa' => Desa::count(),
            'total_program' => Program::count(),
            'total_pendaftar' => Pendaftaran::count(),
            'periode_aktif' => Periode::aktif()->count(),
            'total_penerima' => Pendaftaran::where('status', 'lulus')->count(),
        ];

        // Data for Charts
        $pendaftarPerProgram = DB::table('pendaftarans')
            ->join('programs', 'pendaftarans.program_id', '=', 'programs.id')
            ->select('programs.nama', DB::raw('count(pendaftarans.id) as total'))
            ->groupBy('programs.id', 'programs.nama')
            ->get();

        $pendaftarPerStatus = DB::table('pendaftarans')
            ->select('status', DB::raw('count(id) as total'))
            ->groupBy('status')
            ->get();

        return view('super-admin.dashboard', compact('stats', 'pendaftarPerProgram', 'pendaftarPerStatus'));
    }

    /**
     * Monitoring Pendaftaran Keseluruhan.
     */
    public function monitoringPendaftaran(Request $request)
    {
        $query = Pendaftaran::with(['program', 'desa', 'kecamatan', 'uploadDokumens.dokumen.opd'])->latest();

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }
        if ($request->filled('status')) {
            if ($request->status === 'menunggu_verifikasi') {
                $query->where('status', 'menunggu_verifikasi');
            } elseif ($request->status === 'sudah_diverifikasi') {
                $query->where('status', 'lolos_verifikasi');
            } elseif ($request->status === 'sudah_dinilai') {
                $query->where('status', 'menunggu_penetapan');
            } elseif ($request->status === 'ditetapkan') {
                $query->where('status', 'lulus');
            } elseif ($request->status === 'ditolak') {
                $query->whereIn('status', ['tidak_lolos_verifikasi', 'tidak_lulus']);
            }
        }

        $pendaftaran = $query->search(['nomor_pendaftaran'], request('search'))
                             ->sort(request('sort', 'created_at'), request('dir', 'desc'))
                             ->paginate(15)
                             ->withQueryString();
        $programs = Program::orderBy('urutan')->get();
        $tahunList = Periode::orderBy('tahun', 'desc')->pluck('tahun');

        return view('super-admin.monitoring.pendaftaran', compact('pendaftaran', 'programs', 'tahunList'));
    }

    /**
     * Halaman Histori Penerima Beasiswa.
     */
    public function historiPenerima(Request $request)
    {
        $legacy = DB::table('histori_penerimas')
            ->select('tahun', 'nik', 'nama_lengkap', 'asal_perguruan_tinggi', 'jenis_beasiswa');

        $baru = DB::table('penerima_beasiswas')
            ->join('pendaftarans', 'penerima_beasiswas.pendaftaran_id', '=', 'pendaftarans.id')
            ->join('pendaftaran_identitas', 'pendaftarans.id', '=', 'pendaftaran_identitas.pendaftaran_id')
            ->join('programs', 'pendaftarans.program_id', '=', 'programs.id')
            ->select(
                'pendaftarans.tahun',
                'pendaftaran_identitas.nik',
                'pendaftaran_identitas.nama_lengkap',
                'pendaftaran_identitas.asal_perguruan_tinggi',
                'programs.nama as jenis_beasiswa'
            );

        if ($request->filled('tahun')) {
            $legacy->where('tahun', $request->tahun);
            $baru->where('pendaftarans.tahun', $request->tahun);
        }

        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $legacy->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', $search)
                  ->orWhere('nik', 'like', $search)
                  ->orWhere('asal_perguruan_tinggi', 'like', $search)
                  ->orWhere('jenis_beasiswa', 'like', $search);
            });

            $baru->where(function ($q) use ($search) {
                $q->where('pendaftaran_identitas.nama_lengkap', 'like', $search)
                  ->orWhere('pendaftaran_identitas.nik', 'like', $search)
                  ->orWhere('pendaftaran_identitas.asal_perguruan_tinggi', 'like', $search)
                  ->orWhere('programs.nama', 'like', $search);
            });
        }

        // Wrap the union in a subquery to sort and paginate cleanly
        $combinedQuery = $legacy->unionAll($baru);
        $histori = DB::query()
            ->fromSub($combinedQuery, 'combined')
            ->orderBy('tahun', 'desc')
            ->orderBy('nama_lengkap', 'asc')
            ->paginate(50)
            ->withQueryString();

        // Ambil daftar tahun secara dinamis dari histori_penerimas & periode master
        $tahunLegacy = DB::table('histori_penerimas')->pluck('tahun')->toArray();
        $tahunPeriode = Periode::pluck('tahun')->toArray();
        $tahunList = collect(array_merge($tahunLegacy, $tahunPeriode))
            ->map(fn($t) => (int)$t)
            ->filter(fn($t) => $t > 0)
            ->unique()
            ->sortDesc()
            ->values();

        return view('super-admin.histori', compact('histori', 'tahunList'));
    }

    /**
     * Import Histori dari Excel.
     */
    public function importHistori(ImportHistoriRequest $request)
    {
        try {
            \Maatwebsite\Excel\Facades\Excel::import(
                new \App\Imports\HistoriPenerimaImport(),
                $request->file('file_excel')
            );

            return redirect()->back()->with('success', 'Data histori berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Audit Log.
     */
    public function auditLog(Request $request)
    {
        $query = AuditLog::with('user')->latest('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('aktivitas')) {
            $query->where('aktivitas', $request->aktivitas);
        }
        
        if ($request->filled('modul')) {
            $query->where('model_type', 'like', '%' . $request->modul . '%');
        }

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('created_at', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_sampai);
        }

        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function($q) use ($search) {
                $q->where('deskripsi', 'like', $search)
                  ->orWhere('ip_address', 'like', $search)
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('nama', 'like', $search);
                  });
            });
        }

        $logs = $query->paginate(50)->withQueryString();
        
        $users = User::orderBy('nama')->get(['id', 'nama']);
        $aktivitasList = AuditLog::select('aktivitas')->distinct()->pluck('aktivitas');

        return view('super-admin.log.index', compact('logs', 'users', 'aktivitasList'));
    }
}
