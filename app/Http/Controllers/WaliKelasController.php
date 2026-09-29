<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\JenisUjian;
use App\Models\Nilai;
use Maatwebsite\Excel\Facades\Excel;

class WaliKelasController extends Controller
{
    public function index()
    {
        // 1. Cari profil guru berdasarkan akun yang sedang login
        $guru = Guru::where('user_id', Auth::id())->first();

        // (Opsional) Jika Admin yang akses, arahkan saja ke menu Data Kelas
        if (Auth::user()->role == 'admin') {
            return redirect()->route('admin.kelas.index')->with('info', 'Admin mengelola rapor lewat menu Data Kelas.');
        }

        if (!$guru) {
            return redirect('/')->with('error', 'Profil guru tidak ditemukan.');
        }

        // 2. Cari kelas di mana guru ini adalah wali kelasnya
        $kelas_wali = Kelas::where('guru_id', $guru->id)->first();

        if (!$kelas_wali) {
            return redirect('/')->with('error', 'Maaf, Anda belum ditugaskan sebagai Wali Kelas.');
        }

        // 3. Jika ketemu, ambil semua daftar siswanya
        $data_siswa = Siswa::where('kelas_id', $kelas_wali->id)->get();

        return view('wali.dashboard', compact('kelas_wali', 'data_siswa'));
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
    
}
