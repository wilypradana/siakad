<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\JenisUjian;
use App\Models\Nilai;
use App\Models\CatatanRapor;
use App\Models\Ekstrakurikuler;
use App\Models\Prestasi;
use Maatwebsite\Excel\Facades\Excel;

class WaliKelasController extends Controller
{
    public function index()
    {
        $guru = Guru::where('user_id', Auth::id())->first();

        if (Auth::user()->role == 'admin') {
            return redirect()->route('admin.kelas.index')->with('info', 'Admin mengelola rapor lewat menu Data Kelas.');
        }

        if (!$guru) {
            return redirect('/')->with('error', 'Profil guru tidak ditemukan.');
        }

        $kelas_wali = Kelas::where('guru_id', $guru->id)->first();

        if (!$kelas_wali) {
            return redirect('/')->with('error', 'Maaf, Anda belum ditugaskan sebagai Wali Kelas.');
        }

        // 3. Jika ketemu, ambil semua daftar siswanya
        $data_siswa = Siswa::where('kelas_id', $kelas_wali->id)->get();
        
        // Ambil semua jenis ujian yang tersedia untuk pilihan input catatan
        $jenis_ujian_tersedia = JenisUjian::all();

        return view('wali.dashboard', compact('kelas_wali', 'data_siswa', 'jenis_ujian_tersedia'));
    }

    public function cetak($siswa_id)
    {
        // Ambil data siswa beserta relasi kelas dan nilainya
        $siswa = Siswa::with(['kelas', 'nilai.mapel'])->findOrFail($siswa_id);
        
        return view('wali.cetak_rapor', compact('siswa'));
    }

    public function cetakRapor(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'siswa_id' => 'required',
            'jenis_ujian_id' => 'required',
        ]);

        // 2. Tangkap data dari form
        $siswa_id = $request->siswa_id;
        $jenis_ujian_id = $request->jenis_ujian_id;

        // 3. Ambil data yang dibutuhkan dari database
        $siswa = Siswa::with('kelas')->findOrFail($siswa_id);
        $jenis_ujian = JenisUjian::findOrFail($jenis_ujian_id);
        
        // 4. Ambil rekap nilai siswa tersebut
        // Tarik data nilai berdasarkan Siswa DAN Jenis Ujian yang dipilih
        $data_nilai = Nilai::with('mapel')
                           ->where('siswa_id', $siswa_id)
                           ->where('jenis_ujian_id', $jenis_ujian_id)
                           ->get();

        // 5. Lempar ke halaman khusus cetak (print view)
        return view('wali.cetak_rapor', compact('siswa', 'jenis_ujian', 'data_nilai'));
    }

    // ==========================================
    // FITUR BARU: Pratinjau Legger Nilai di Web
    // ==========================================
    public function lihatLegger(Request $request)
    {
        $request->validate([
            'jenis_ujian_id' => 'required',
        ]);

        $guru = Guru::where('user_id', Auth::id())->first();
        $kelas_wali = Kelas::where('guru_id', $guru->id)->first();
        $jenis_ujian = JenisUjian::findOrFail($request->jenis_ujian_id);

        $data_siswa = Siswa::where('kelas_id', $kelas_wali->id)->orderBy('nama', 'asc')->get();
        $data_mapel = \App\Models\Mapel::orderBy('nama_mapel', 'asc')->get();

        // Optimasi Query: Ambil semua nilai sekaligus, lalu kelompokkan
        $siswa_ids = $data_siswa->pluck('id');
        $semua_nilai = Nilai::whereIn('siswa_id', $siswa_ids)
                            ->where('jenis_ujian_id', $request->jenis_ujian_id)
                            ->get();

        // Susun nilai ke dalam array matrix agar mudah dipanggil di View
        $nilai_matrix = [];
        $mapel_ids = $semua_nilai->pluck('mapel_id')->unique();
        $data_mapel = \App\Models\Mapel::whereIn('id', $mapel_ids)->orderBy('nama_mapel', 'asc')->get();

        return view('wali.legger', compact('kelas_wali', 'jenis_ujian', 'data_siswa', 'data_mapel', 'nilai_matrix'));
    }
    // ==========================================
    // FITUR BARU: Export Rekap Nilai 1 Kelas
    // ==========================================
    // Jangan lupa panggil class ini di bagian paling atas file (dibawah use Illuminate\Http\Request;):
    // use App\Exports\LeggerExport;
    // use Maatwebsite\Excel\Facades\Excel;

    public function exportRekap(Request $request)
    {
        $request->validate([
            'jenis_ujian_id' => 'required',
        ]);

        $guru = Guru::where('user_id', Auth::id())->first();
        $kelas_wali = Kelas::where('guru_id', $guru->id)->first();
        $jenis_ujian = JenisUjian::findOrFail($request->jenis_ujian_id);

        // Buat nama file rapi tanpa spasi
        $nama_file = 'Legger_Nilai_' . str_replace(' ', '_', $kelas_wali->nama_kelas) . '_' . date('dMy') . '.xlsx';

        // Panggil library Maatwebsite Excel untuk men-download file .xlsx
        return Excel::download(new \App\Exports\LeggerExport($kelas_wali->id, $request->jenis_ujian_id), $nama_file);
    }

    public function cetakRaporMassal(Request $request)
    {
        $request->validate([
            'jenis_ujian_id' => 'required',
        ]);

        $guru = Guru::where('user_id', Auth::id())->first();
        $kelas_wali = Kelas::where('guru_id', $guru->id)->first();
        $jenis_ujian = JenisUjian::findOrFail($request->jenis_ujian_id);

        // Ambil semua siswa di kelas ini, urutkan berdasarkan nama
        $kumpulan_siswa = Siswa::with('kelas')
                            ->where('kelas_id', $kelas_wali->id)
                            ->orderBy('nama', 'asc')
                            ->get();
        
        // Optimasi Query: Ambil semua nilai dari seluruh siswa di kelas ini sekaligus
        $semua_nilai = Nilai::with('mapel')
                           ->whereIn('siswa_id', $kumpulan_siswa->pluck('id'))
                           ->where('jenis_ujian_id', $jenis_ujian->id)
                           ->get();

        return view('wali.cetak_rapor_massal', compact('kumpulan_siswa', 'jenis_ujian', 'semua_nilai'));
    }

    // ==========================================
    // FITUR BARU: Simpan Catatan & Kehadiran
    // ==========================================
    public function simpanCatatan(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required',
            'jenis_ujian_id' => 'required',
            'sakit' => 'nullable|integer|min:0',
            'izin' => 'nullable|integer|min:0',
            'alfa' => 'nullable|integer|min:0',
            'catatan_wali_kelas' => 'nullable|string'
        ]);

        // Gunakan updateOrCreate agar tidak duplicate. 
        // Jika sudah ada data untuk siswa dan jenis ujian ini, maka update. Jika belum, create baru.
        CatatanRapor::updateOrCreate(
            [
                'siswa_id' => $request->siswa_id,
                'jenis_ujian_id' => $request->jenis_ujian_id
            ],
            [
                'sakit' => $request->sakit ?? 0,
                'izin' => $request->izin ?? 0,
                'alfa' => $request->alfa ?? 0,
                'catatan_wali_kelas' => $request->catatan_wali_kelas
            ]
        );

        return redirect()->back()->with('success', 'Catatan dan Kehadiran berhasil disimpan!');
    }

    // ==========================================
    // FITUR BARU: Ambil Data Catatan via AJAX
    // ==========================================
    public function getCatatan(Request $request)
    {
        $catatan = CatatanRapor::where('siswa_id', $request->siswa_id)
                               ->where('jenis_ujian_id', $request->jenis_ujian_id)
                               ->first();

        if ($catatan) {
            return response()->json([
                'status' => 'success', 
                'data' => $catatan
            ]);
        }

        return response()->json(['status' => 'empty']);
    }

    // ==========================================
    // FITUR: Halaman Input Catatan Massal
    // ==========================================
    public function inputCatatanMassal(Request $request)
    {
        $guru = Guru::where('user_id', Auth::id())->first();
        $kelas_wali = Kelas::where('guru_id', $guru->id)->first();
        $jenis_ujian_tersedia = JenisUjian::all();
        
        $data_siswa = Siswa::where('kelas_id', $kelas_wali->id)->orderBy('nama', 'asc')->get();
        
        // Tangkap jenis ujian jika wali kelas sudah memilih filter dropdown
        $jenis_ujian_id = $request->jenis_ujian_id;
        $catatan_tersimpan = [];
        
        if ($jenis_ujian_id) {
            // Tarik data yang sudah pernah disimpan sebelumnya agar otomatis terisi di form
            $catatan = CatatanRapor::whereIn('siswa_id', $data_siswa->pluck('id'))
                                   ->where('jenis_ujian_id', $jenis_ujian_id)
                                   ->get()
                                   ->keyBy('siswa_id'); // Jadikan ID siswa sebagai key array
            $catatan_tersimpan = $catatan;
        }

        return view('wali.input_catatan_massal', compact('kelas_wali', 'data_siswa', 'jenis_ujian_tersedia', 'jenis_ujian_id', 'catatan_tersimpan'));
    }

    public function simpanCatatanMassal(Request $request)
    {
        $request->validate([
            'jenis_ujian_id' => 'required',
            'siswa_ids' => 'required|array',
        ]);

        $jenis_ujian_id = $request->jenis_ujian_id;

        // Looping untuk menyimpan data setiap siswa yang dikirim dari form massal
        foreach ($request->siswa_ids as $siswa_id) {
            CatatanRapor::updateOrCreate(
                [
                    'siswa_id' => $siswa_id,
                    'jenis_ujian_id' => $jenis_ujian_id
                ],
                [
                    'sakit' => $request->sakit[$siswa_id] ?? 0,
                    'izin' => $request->izin[$siswa_id] ?? 0,
                    'alfa' => $request->alfa[$siswa_id] ?? 0,
                    'catatan_wali_kelas' => $request->catatan_wali_kelas[$siswa_id] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Kehadiran dan Catatan untuk seluruh siswa berhasil disimpan!');
    }

   // ==========================================
    // FITUR: Input Massal Ekstrakurikuler & Prestasi
    // ==========================================
    public function ekskulPrestasiMassal(Request $request)
    {
        $guru = Guru::where('user_id', Auth::id())->first();
        $kelas_wali = Kelas::where('guru_id', $guru->id)->first();
        
        $jenis_ujian_tersedia = JenisUjian::all();
        $jenis_ujian_id = $request->jenis_ujian_id ?? null;
        
        $data_siswa = Siswa::where('kelas_id', $kelas_wali->id)->orderBy('nama', 'asc')->get();
        
        $ekskul_tersimpan = [];
        $prestasi_tersimpan = [];

        if ($jenis_ujian_id) {
            // Ambil data ekskul dan kelompokkan berdasarkan siswa_id
            $ekskul_tersimpan = Ekstrakurikuler::whereIn('siswa_id', $data_siswa->pluck('id'))
                                    ->where('jenis_ujian_id', $jenis_ujian_id)
                                    ->get()
                                    ->groupBy('siswa_id');
                                    
            $prestasi_tersimpan = Prestasi::whereIn('siswa_id', $data_siswa->pluck('id'))
                                    ->where('jenis_ujian_id', $jenis_ujian_id)
                                    ->get()
                                    ->groupBy('siswa_id');
        }

        return view('wali.ekskul_prestasi', compact('kelas_wali', 'data_siswa', 'jenis_ujian_tersedia', 'jenis_ujian_id', 'ekskul_tersimpan', 'prestasi_tersimpan'));
    }

    public function simpanEkskulPrestasiMassal(Request $request)
    {
        $request->validate([
            'jenis_ujian_id' => 'required',
            'siswa_ids' => 'required|array',
        ]);

        $jenis_ujian_id = $request->jenis_ujian_id;

        foreach ($request->siswa_ids as $siswa_id) {
            // Hapus data lama untuk siswa dan semester ini agar bersih sebelum diisi ulang
            Ekstrakurikuler::where('siswa_id', $siswa_id)->where('jenis_ujian_id', $jenis_ujian_id)->delete();
            Prestasi::where('siswa_id', $siswa_id)->where('jenis_ujian_id', $jenis_ujian_id)->delete();

            // Simpan Ekskul 1 (Jika Diisi)
            if (!empty($request->ekskul_1[$siswa_id])) {
                Ekstrakurikuler::create([
                    'siswa_id' => $siswa_id, 'jenis_ujian_id' => $jenis_ujian_id,
                    'nama_ekskul' => $request->ekskul_1[$siswa_id],
                    'predikat' => $request->predikat_1[$siswa_id] ?? 'Baik',
                    'keterangan' => '-' // Kosongkan keterangan karena Anda tidak membutuhkannya
                ]);
            }

            // Simpan Ekskul 2 (Jika Diisi)
            if (!empty($request->ekskul_2[$siswa_id])) {
                Ekstrakurikuler::create([
                    'siswa_id' => $siswa_id, 'jenis_ujian_id' => $jenis_ujian_id,
                    'nama_ekskul' => $request->ekskul_2[$siswa_id],
                    'predikat' => $request->predikat_2[$siswa_id] ?? 'Baik',
                    'keterangan' => '-'
                ]);
            }

            // Simpan Prestasi (Jika Diisi)
            if (!empty($request->prestasi[$siswa_id])) {
                Prestasi::create([
                    'siswa_id' => $siswa_id, 'jenis_ujian_id' => $jenis_ujian_id,
                    'jenis_prestasi' => 'Bebas', 
                    'nama_prestasi' => $request->prestasi[$siswa_id],
                    'keterangan' => '-'
                ]);
            }
        }

        return redirect()->back()->with('success', 'Data Ekstrakurikuler dan Prestasi massal berhasil disimpan!');
    }
}
