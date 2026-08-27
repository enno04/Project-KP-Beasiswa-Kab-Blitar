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
        $query = Pendaftaran::with(['program', 'jalur', 'desa', 'kecamatan', 'identitas', 'uploadDokumens.dokumen.opd'])->latest();

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }
        if ($request->filled('jalur_id')) {
            $query->where('jalur_id', $request->jalur_id);
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

        if ($request->filled('kecamatan_id')) {
            $query->whereHas('identitas', function($q) use ($request) {
                $q->where('kecamatan_id', $request->kecamatan_id);
            });
        }

        if ($request->filled('urutan_waktu')) {
            if ($request->urutan_waktu === 'terlama') {
                $query->oldest('pendaftarans.created_at');
            } else {
                $query->latest('pendaftarans.created_at');
            }
        }

        $search = request('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nomor_pendaftaran', 'LIKE', "%{$search}%")
                  ->orWhereHas('identitas', function($qIdentitas) use ($search) {
                      $qIdentitas->where('nama_lengkap', 'LIKE', "%{$search}%")
                                 ->orWhere('nik', 'LIKE', "%{$search}%");
                  });
            });
        }

        $perPage = request('per_page', 15);
        $pendaftaran = $query->sort(request('sort', 'created_at'), request('dir', 'desc'))
                             ->paginate($perPage)
                             ->withQueryString();
        $programs = Program::orderBy('urutan')->get();
        $tahunList = Periode::orderBy('tahun', 'desc')->pluck('tahun');
        $kecamatanList = Kecamatan::orderBy('nama_kecamatan')->get();
        $jalurList = \App\Models\Jalur::orderBy('nama')->get();

        return view('super-admin.monitoring.pendaftaran', compact('pendaftaran', 'programs', 'tahunList', 'kecamatanList', 'jalurList'));
    }

    /**
     * Halaman Histori Penerima Beasiswa.
     */
    public function historiPenerima(Request $request)
    {
        $tab = $request->query('tab', 'lama'); // default tab lama

        if ($tab === 'lama') {
            $query = DB::table('histori_penerimas')->where('sumber_data', 'dinas');

            if ($request->filled('search')) {
                $search = '%' . trim($request->search) . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', $search)
                      ->orWhere('nik', 'like', $search)
                      ->orWhere('asal_perguruan_tinggi', 'like', $search)
                      ->orWhere('jenis_beasiswa', 'like', $search);
                });
            }

            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }

            if ($request->filled('jenis_beasiswa')) {
                $query->where('jenis_beasiswa', $request->jenis_beasiswa);
            }

            if ($request->filled('sort_added')) {
                if ($request->sort_added === 'terbaru') {
                    $query->orderBy('id', 'desc');
                } else if ($request->sort_added === 'terlama') {
                    $query->orderBy('id', 'asc');
                }
            } else {
                $query->orderBy('tahun', 'desc')
                      ->orderBy('nama_lengkap', 'asc');
            }

            $histori = $query->paginate($request->get('per_page', 50))
                ->withQueryString();

            $tahunList = DB::table('histori_penerimas')
                ->where('sumber_data', 'dinas')
                ->select('tahun')
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->pluck('tahun');
            
            $programList = DB::table('histori_penerimas')
                ->where('sumber_data', 'dinas')
                ->whereNotNull('jenis_beasiswa')
                ->select('jenis_beasiswa')
                ->distinct()
                ->orderBy('jenis_beasiswa', 'asc')
                ->pluck('jenis_beasiswa');
                
        } else {
            // Tab Baru: HANYA menampilkan data histori sistem (import), TANPA data live pendaftarans
            $query = DB::table('histori_penerimas')->where('sumber_data', 'sistem');

            if ($request->filled('search')) {
                $search = '%' . trim($request->search) . '%';
                $query->where(function ($q) use ($search) {
                    $q->where('nama_lengkap', 'like', $search)
                      ->orWhere('nik', 'like', $search)
                      ->orWhere('asal_perguruan_tinggi', 'like', $search)
                      ->orWhere('jenis_beasiswa', 'like', $search)
                      ->orWhere('nomor_pendaftaran', 'like', $search);
                });
            }

            if ($request->filled('tahun')) {
                $query->where('tahun', $request->tahun);
            }

            if ($request->filled('jenis_beasiswa')) {
                $query->where('jenis_beasiswa', $request->jenis_beasiswa);
            }

            if ($request->filled('sort_added')) {
                if ($request->sort_added === 'terbaru') {
                    $query->orderBy('id', 'desc');
                } else if ($request->sort_added === 'terlama') {
                    $query->orderBy('id', 'asc');
                }
            } else {
                $query->orderBy('tahun', 'desc')
                      ->orderBy('nama_lengkap', 'asc');
            }

            $histori = $query->paginate($request->get('per_page', 50))
                ->withQueryString();

            $tahunList = DB::table('histori_penerimas')
                ->where('sumber_data', 'sistem')
                ->select('tahun')
                ->distinct()
                ->orderBy('tahun', 'desc')
                ->pluck('tahun');
            
            $programList = DB::table('histori_penerimas')
                ->where('sumber_data', 'sistem')
                ->whereNotNull('jenis_beasiswa')
                ->select('jenis_beasiswa')
                ->distinct()
                ->orderBy('jenis_beasiswa', 'asc')
                ->pluck('jenis_beasiswa');
        }

        return view('super-admin.histori', compact('histori', 'tahunList', 'programList', 'tab'));
    }

    /**
     * Import Histori dari Excel (Format Lama/Dinas).
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
     * Import Histori dari Excel (Format Baru/Admin Kab).
     */
    public function importHistoriBaru(ImportHistoriRequest $request)
    {
        try {
            \Maatwebsite\Excel\Facades\Excel::import(
                new \App\Imports\RiwayatPenetapanImport(),
                $request->file('file_excel')
            );

            return redirect()->back()->with('success', 'Data histori (Format Baru) berhasil diimpor!');
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

        // Metrik Ringkas
        $today = now()->toDateString();
        $totalHariIni = AuditLog::whereDate('created_at', $today)->count();
        $totalKritis = AuditLog::whereDate('created_at', $today)
            ->where(function($q) {
                $q->where('aktivitas', 'like', '%Hapus%')
                  ->orWhere('aktivitas', 'like', '%Gagal%')
                  ->orWhere('aktivitas', 'like', '%Error%');
            })->count();
            
        $topUserObj = AuditLog::whereDate('created_at', $today)
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->first();
        $topUser = $topUserObj ? User::find($topUserObj->user_id)?->nama : 'Belum Ada';

        // Ekspor ke CSV
        if ($request->get('export') === 'csv') {
            $exportQuery = clone $query;
            $exportData = $exportQuery->get();
            
            $filename = "audit_log_" . date('Ymd_His') . ".csv";
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$filename",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($exportData) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Waktu', 'User', 'IP Address', 'Aktivitas', 'Modul', 'ID Data', 'Deskripsi', 'Data Lama', 'Data Baru']);
                
                foreach ($exportData as $row) {
                    fputcsv($file, [
                        $row->created_at?->format('Y-m-d H:i:s'),
                        $row->user?->nama ?? 'Sistem',
                        $row->ip_address,
                        $row->aktivitas,
                        class_basename($row->model_type),
                        $row->model_id,
                        $row->deskripsi,
                        $row->data_lama ? json_encode($row->data_lama) : '',
                        $row->data_baru ? json_encode($row->data_baru) : ''
                    ]);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        $logs = $query->paginate(50)->withQueryString();
        
        $users = User::orderBy('nama')->get(['id', 'nama']);
        $aktivitasList = AuditLog::select('aktivitas')->distinct()->pluck('aktivitas');

        return view('super-admin.log.index', compact('logs', 'users', 'aktivitasList', 'totalHariIni', 'totalKritis', 'topUser'));
    }

    public function pembersihanData()
    {
        // Ambil daftar program beserta jumlah pendaftarannya
        $programs = \App\Models\Program::withCount('pendaftarans')->get();
        return view('super-admin.pembersihan-data', compact('programs'));
    }

    public function detailPembersihanData(\Illuminate\Http\Request $request, $program_id)
    {
        $program = \App\Models\Program::findOrFail($program_id);
        $query = \App\Models\Pendaftaran::with(['identitas', 'desa', 'kecamatan'])
            ->where('program_id', $program_id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nomor_pendaftaran', 'like', "%{$search}%")
                  ->orWhereHas('identitas', function($q2) use ($search) {
                      $q2->where('nama_lengkap', 'like', "%{$search}%")
                         ->orWhere('nik', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('kecamatan_id')) {
            $query->whereHas('identitas', fn($q) => $q->where('kecamatan_id', $request->kecamatan_id));
        }

        if ($request->filled('desa_id')) {
            $query->whereHas('identitas', fn($q) => $q->where('desa_id', $request->desa_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pendaftarans = $query->latest()->paginate(50);
        
        $kecamatans = \App\Models\Kecamatan::orderBy('nama_kecamatan')->get();
        $desas = [];
        if ($request->filled('kecamatan_id')) {
            $desas = \App\Models\Desa::where('kecamatan_id', $request->kecamatan_id)->orderBy('nama_desa')->get();
        }

        return view('super-admin.pembersihan-data-detail', compact('program', 'pendaftarans', 'kecamatans', 'desas'));
    }

    public function destroyDataPendaftar(\Illuminate\Http\Request $request)
    {
        $ids = $request->input('pendaftaran_ids');
        if (empty($ids) || !is_array($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu data pendaftar untuk dihapus.');
        }

        $pendaftarans = \App\Models\Pendaftaran::whereIn('id', $ids)->get();
        $count = $pendaftarans->count();

        if ($count === 0) {
            return redirect()->back()->with('error', 'Tidak ada data pendaftar valid yang dipilih.');
        }

        $deletedDetailsText = "Super Admin menghapus $count data pendaftar:\n";
        $i = 1;

        foreach ($pendaftarans as $p) {
            $desa = $p->desa->nama_desa ?? '-';
            $kecamatan = $p->kecamatan->nama_kecamatan ?? '-';
            
            // Kumpulkan detail sebelum dihapus dalam bentuk teks
            $deletedDetailsText .= "$i. Nama: {$p->nama_lengkap} | NIK: {$p->nik} | Asal: Desa $desa, Kec. $kecamatan | Status Terakhir: " . ($p->status_label ?? $p->status) . "\n";
            $i++;

            // Hapus file fisik (jika ada)
            $dokumens = \App\Models\UploadDokumen::where('pendaftaran_id', $p->id)->get();
            foreach ($dokumens as $dok) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($dok->file_path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($dok->file_path);
                }
            }

            // Hapus histori penerima jika ada (agar tidak error duplikat saat import ulang)
            if ($p->nik) {
                \App\Models\HistoriPenerima::where('nik', $p->nik)
                    ->where('tahun', $p->tahun)
                    ->delete();
            }

            // Hapus log audit yang berkaitan dengan model pendaftaran ini
            \App\Models\AuditLog::where('model_id', $p->id)
                ->where('model_type', \App\Models\Pendaftaran::class)
                ->delete();

            // Pendaftaran dihapus, cascade FK akan menghapus upload_dokumens, jawaban dll
            $p->delete();
        }

        // Catat di log bahwa super admin melakukan pembersihan sebagian dengan rincian teks biasa
        \App\Models\AuditLog::catat(
            'Pembersihan Data Spesifik',
            $deletedDetailsText,
            \App\Models\Pendaftaran::class,
            0
        );

        return redirect()->back()->with('success', "Berhasil menghapus $count data pendaftar beserta file dokumennya secara permanen.");
    }

    public function dataDummy()
    {
        $programs = \App\Models\Program::all();
        $kecamatans = \App\Models\Kecamatan::with('desa')->orderBy('nama_kecamatan')->get();
        return view('super-admin.data-dummy', compact('programs', 'kecamatans'));
    }

    public function generateDataDummy(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
            'jumlah' => 'required|integer|min:1|max:100',
            'desa_id' => 'nullable|exists:desa,id',
        ]);

        $program = \App\Models\Program::findOrFail($request->program_id);
        $jumlah = $request->jumlah;
        $faker = \Faker\Factory::create('id_ID');

        $jalurIds = \App\Models\Jalur::where('program_id', $program->id)->pluck('id')->toArray();
        if(empty($jalurIds)) {
            return redirect()->back()->with('error', 'Program ini tidak memiliki jalur pendaftaran aktif.');
        }

        $desaIds = \App\Models\Desa::pluck('id')->toArray();
        if(empty($desaIds)) {
            return redirect()->back()->with('error', 'Data desa kosong. Harap isi master desa terlebih dahulu.');
        }

        for ($i = 0; $i < $jumlah; $i++) {
            $jalur_id = $faker->randomElement($jalurIds);
            
            // Buat Pendaftaran
            $nomor_pendaftaran = \App\Models\Pendaftaran::generateNomorPendaftaran(
                $program->kode ?? 'DUMMY',
                \App\Models\Jalur::find($jalur_id)->kode ?? 'JALUR',
                $program->tahun ?? date('Y')
            );

            $pendaftaran = \App\Models\Pendaftaran::create([
                'nomor_pendaftaran' => $nomor_pendaftaran,
                'periode_id' => $program->periode_id ?? 1,
                'program_id' => $program->id,
                'jalur_id' => $jalur_id,
                'tahun' => $program->tahun ?? date('Y'),
                'total_nilai' => 0,
                'ranking' => null,
                'status' => 'menunggu_verifikasi',
                'created_by' => auth()->id() ?? 1,
            ]);

            // Buat Identitas
            $desa_id = $request->desa_id ?? $faker->randomElement($desaIds);
            $desa = \App\Models\Desa::find($desa_id);
            
            \App\Models\PendaftaranIdentitas::create([
                'pendaftaran_id' => $pendaftaran->id,
                'nama_lengkap' => '[DUMMY] ' . $faker->name,
                'nik' => $faker->nik(),
                'no_kk' => $faker->nik(),
                'tempat_lahir' => $faker->city,
                'tanggal_lahir' => $faker->date('Y-m-d', '-18 years'),
                'jenis_kelamin' => $faker->randomElement(['L', 'P']),
                'alamat_ktp' => $faker->streetAddress . ', RT ' . $faker->numberBetween(1, 10) . ' / RW ' . $faker->numberBetween(1, 10), // diperbaiki
                'desa_id' => $desa_id,
                'kecamatan_id' => $desa->kecamatan_id ?? null,
                'kodepos' => $faker->postcode,
                'no_hp' => $faker->phoneNumber,
                'email' => $faker->unique()->safeEmail,
                'asal_perguruan_tinggi' => $faker->company . ' University',
                'program_studi' => $faker->randomElement(['S1 Teknik Informatika', 'S1 Sistem Informasi', 'S1 Kedokteran', 'S1 Ilmu Hukum', 'S1 Ekonomi Pembangunan']), // diperbaiki
                'semester' => (string) $faker->numberBetween(1, 8), // diperbaiki
            ]);

            // Buat Data Orang Tua Dummy
            \App\Models\PendaftaranOrangtua::create([
                'pendaftaran_id' => $pendaftaran->id,
                'nama_ayah' => '[DUMMY] ' . $faker->name('male'),
                'nik_ayah' => $faker->nik(),
                'alamat_ayah' => $faker->address,
                'tempat_lahir_ayah' => $faker->city,
                'tanggal_lahir_ayah' => $faker->date('Y-m-d', '-45 years'),
                'no_hp_ayah' => $faker->phoneNumber,
                'nama_ibu' => '[DUMMY] ' . $faker->name('female'),
                'nik_ibu' => $faker->nik(),
                'alamat_ibu' => $faker->address,
                'tempat_lahir_ibu' => $faker->city,
                'tanggal_lahir_ibu' => $faker->date('Y-m-d', '-40 years'),
                'no_hp_ibu' => $faker->phoneNumber,
            ]);

            // Buat Dummy Dokumen Lampiran
            $dokumens = \App\Models\Dokumen::where('jalur_id', $jalur_id)->get();
            
            // Pastikan file dummy.pdf benar-benar ada secara fisik
            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists('dokumen/dummy.pdf')) {
                $pdfContent = base64_decode('JVBERi0xLjQKJeLjz9MKMSAwIG9iago8PC9UeXBlL0NhdGFsb2cvUGFnZXMgMiAwIFI+PgplbmRvYmoKMiAwIG9iago8PC9UeXBlL1BhZ2VzL0NvdW50IDEvS2lkc1szIDAgUl0+PgplbmRvYmoKMyAwIG9iago8PC9UeXBlL1BhZ2UvTWVkaWFCb3hbMCAwIDIwMCAyMDBdL1BhcmVudCAyIDAgUi9SZXNvdXJjZXM8PC9Gb250PDwvRjEgPDwvVHlwZS9Gb250L1N1YnR5cGUvVHlwZTEvQmFzZUZvbnQvSGVsdmV0aWNhPj4+Pj4+L0NvbnRlbnRzIDQgMCBSPj4KZW5kb2JqCjQgMCBvYmoKPDwvTGVuZ3RoIDQzPj4Kc3RyZWFtCkJUCjAvRjEgMjQgVGYKMTAgMTAwIFRkCihEVU1NWSBER0tVTUVOIFRFU1QpVGoKRVQKZW5kc3RyZWFtCmVuZG9iagp4cmVmCjAgNQowMDAwMDAwMDAwIDY1NTM1IGYgCjAwMDAwMDAwMTUgMDAwMDAgbiAKMDAwMDAwMDA2MCAwMDAwMCBuIAowMDAwMDAwMTExIDAwMDAwIG4gCjAwMDAwMDAyMzEgMDAwMDAgbiAKdHJhaWxlcgo8PC9TaXplIDUvUm9vdCAxIDAgUj4+CnN0YXJ0eHJlZgoyOTUKJSVFT0YK');
                \Illuminate\Support\Facades\Storage::disk('public')->put('dokumen/dummy.pdf', $pdfContent);
            }

            foreach ($dokumens as $dok) {
                \App\Models\UploadDokumen::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'dokumen_id' => $dok->id,
                    'nama_file' => 'dummy_' . \Illuminate\Support\Str::slug($dok->nama) . '.pdf',
                    'file_path' => 'dokumen/dummy.pdf', // File dummy tunggal
                    'versi' => 1,
                    'status' => 'belum_diverifikasi'
                ]);
            }

            // Buat Dummy Jawaban Kriteria (Agar bisa diranking / dinilai)
            $kelompoks = \App\Models\KelompokKriteria::where('jalur_id', $jalur_id)->with('kriterias.pilihans')->get();
            foreach ($kelompoks as $kelompok) {
                foreach ($kelompok->kriterias as $kriteria) {
                    if ($kriteria->tipe_input === 'pilihan') {
                        if ($kriteria->pilihans->count() > 0) {
                            $pilihan = $kriteria->pilihans->random();
                            \App\Models\JawabanKriteria::create([
                                'pendaftaran_id' => $pendaftaran->id,
                                'kriteria_id' => $kriteria->id,
                                'pilihan_kriteria_id' => $pilihan->id,
                                'nilai_input' => null,
                            ]);
                        }
                    } elseif ($kriteria->tipe_input === 'angka') {
                        $min = $kriteria->nilai_min > 0 ? $kriteria->nilai_min : 50;
                        $max = $kriteria->nilai_max > 0 ? $kriteria->nilai_max : 100;
                        \App\Models\JawabanKriteria::create([
                            'pendaftaran_id' => $pendaftaran->id,
                            'kriteria_id' => $kriteria->id,
                            'pilihan_kriteria_id' => null,
                            'nilai_input' => $faker->randomFloat(2, $min, $max),
                        ]);
                    }
                }
            }

            // Catat log
            \App\Models\AuditLog::catat(
                'Data Dummy',
                "Sistem membuat data pendaftar dummy: {$pendaftaran->nomor_pendaftaran} (Termasuk orang tua, dokumen, & kriteria)",
                \App\Models\Pendaftaran::class,
                $pendaftaran->id
            );
        }

        return redirect()->back()->with('success', "Berhasil me-generate $jumlah data pendaftar dummy beserta kelengkapannya pada program {$program->nama}.");
    }

    public function bypassOpdList(\Illuminate\Http\Request $request)
    {
        $programs = \App\Models\Program::all();
        $kecamatans = \App\Models\Kecamatan::orderBy('nama_kecamatan')->get();
        
        $desas = [];
        if ($request->kecamatan_id) {
            $desas = \App\Models\Desa::where('kecamatan_id', $request->kecamatan_id)->orderBy('nama_desa')->get();
        }

        $query = \App\Models\Pendaftaran::with(['identitas', 'program', 'jalur'])
            ->whereIn('status', ['menunggu_verifikasi', 'sedang_diverifikasi']);

        if ($request->program_id) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->kecamatan_id) {
            $query->whereHas('identitas', fn($q) => $q->where('kecamatan_id', $request->kecamatan_id));
        }

        if ($request->desa_id) {
            $query->whereHas('identitas', fn($q) => $q->where('desa_id', $request->desa_id));
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nomor_pendaftaran', 'like', '%' . $request->search . '%')
                  ->orWhereHas('identitas', function($q2) use ($request) {
                      $q2->where('nama_lengkap', 'like', '%' . $request->search . '%')
                         ->orWhere('nik', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $pendaftarans = $query->latest()->paginate(50);
        $pendaftarans->appends($request->all());

        return view('super-admin.bypass-opd', compact('pendaftarans', 'programs', 'kecamatans', 'desas'));
    }

    public function autoVerifyOpd(\Illuminate\Http\Request $request, \App\Services\VerifikasiService $verifikasiService)
    {
        $request->validate([
            'pendaftaran_ids' => 'required|array',
            'pendaftaran_ids.*' => 'exists:pendaftarans,id'
        ]);

        $pendaftarans = \App\Models\Pendaftaran::whereIn('id', $request->pendaftaran_ids)
            ->whereIn('status', ['menunggu_verifikasi', 'sedang_diverifikasi'])
            ->get();
        
        if ($pendaftarans->isEmpty()) {
            return redirect()->back()->with('error', 'Pendaftar yang dipilih tidak ditemukan atau sudah diverifikasi.');
        }

        $count = 0;
        foreach ($pendaftarans as $p) {
            // Ambil semua dokumen yang belum diverifikasi
            $uploads = \App\Models\UploadDokumen::where('pendaftaran_id', $p->id)
                ->where('status', 'belum_diverifikasi')->get();
                
            foreach ($uploads as $up) {
                $verifikasiService->verifikasiDokumen($up->id, 'valid', 'Otomatis divalidasi oleh sistem Super Admin (Bypass)');
                $count++;
            }
            
            // Cek apakah pendaftar ini sudah lolos verifikasi OPD
            $verifikasiService->cekStatusVerifikasi($p);
        }
        
        \App\Models\AuditLog::catat(
            'Auto-Verifikasi OPD',
            "Super Admin mem-bypass $count dokumen untuk " . $pendaftarans->count() . " pendaftar ke status Valid.",
            \App\Models\User::class,
            auth()->id()
        );

        return redirect()->back()->with('success', "Berhasil memverifikasi otomatis $count dokumen untuk " . $pendaftarans->count() . " pendaftar (Bypass OPD berhasil).");
    }
    public function triggerAutoVerifySla()
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('beasiswa:auto-verify');
            $output = \Illuminate\Support\Facades\Artisan::output();
            return back()->with('success', 'Berhasil menjalankan Auto-Verify SLA. Output: ' . trim($output));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menjalankan Auto-Verify: ' . $e->getMessage());
        }
    }
}
