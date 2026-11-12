<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembelajaran; // Relasi jadwal mapel guru
use App\Models\Materi;
use Illuminate\Support\Facades\Auth;

class ElearningController extends Controller
{
    // 1. Menampilkan Daftar Mapel yang diampu Guru (Dikelompokkan per Mapel)
    public function index()
    {
        // PROTEKSI: Cek apakah user yang login benar-benar punya profil guru
        if (!Auth::user()->guru) {
            abort(403, 'Akses Ditolak: Akun Anda tidak memiliki profil Guru.');
        }

        $guru_id = Auth::user()->guru->id;
        
        // Kelompokkan berdasarkan mapel_id agar rapi (1 kartu per mata pelajaran)
        $jadwal_mengajar = Pembelajaran::with(['kelas', 'mapel'])
                            ->where('guru_id', $guru_id)
                            ->get()
                            ->groupBy('mapel_id');

        return view('guru.elearning.index', compact('jadwal_mengajar'));
    }

    // 2. Menampilkan Form Tambah Materi (Update Multi-Posting)
    public function createMateri($mapel_id) 
    {
        $guru_id = Auth::user()->guru->id;
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        // Cari kelas mana saja yang diajar oleh guru ini KHUSUS untuk mapel ini
        $kelas_tersedia = Pembelajaran::with('kelas')
                            ->where('guru_id', $guru_id)
                            ->where('mapel_id', $mapel_id)
                            ->get();

        return view('guru.elearning.create_materi', compact('mapel', 'kelas_tersedia', 'mapel_id'));
    }

    // 3. Menyimpan Data Materi ke Database (Update Looping Multi-Kelas)
    public function storeMateri(Request $request, $mapel_id)
    {
        $request->validate([
            'kelas_id' => 'required|array|min:1', // Wajib minimal 1 kelas dicentang
            'judul_materi' => 'required|string|max:255',
            'deskripsi' => 'required',
            'link_materi' => 'nullable|url'
        ]);

        // Tangkap status centang tugas dari form
        $is_tugas = $request->has('is_tugas') ? 1 : 0;

        // Looping: Simpan materi sebanyak kelas yang dicentang guru
        foreach ($request->kelas_id as $kelas_id) {
            Materi::create([
                'guru_id' => Auth::user()->guru->id,
                'mapel_id' => $mapel_id,
                'kelas_id' => $kelas_id,
                'judul_materi' => $request->judul_materi,
                'deskripsi' => $request->deskripsi,
                'link_materi' => $request->link_materi,
                'is_tugas' => $is_tugas,
            ]);
        }

        return redirect()->route('guru.elearning.index')->with('success', 'Materi/Tugas berhasil didistribusikan ke ' . count($request->kelas_id) . ' kelas sekaligus!');
    }

    // 4. Menampilkan Daftar Materi yang Sudah Dibuat
    public function listMateri($kelas_id, $mapel_id)
    {
        $guru_id = Auth::user()->guru->id;
        
        $kelas = \App\Models\Kelas::findOrFail($kelas_id);
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        $materis = Materi::where('guru_id', $guru_id)
                        ->where('kelas_id', $kelas_id)
                        ->where('mapel_id', $mapel_id)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('guru.elearning.list_materi', compact('materis', 'kelas', 'mapel', 'kelas_id', 'mapel_id'));
    }

    // 5. Menampilkan Form Edit Materi
    public function editMateri($id)
    {
        $materi = Materi::findOrFail($id);
        
        if ($materi->guru_id != Auth::user()->guru->id) {
            abort(403, 'Akses Ditolak.');
        }

        return view('guru.elearning.edit_materi', compact('materi'));
    }

    // 6. Memproses Perubahan Data Materi
    public function updateMateri(Request $request, $id)
    {
        $request->validate([
            'judul_materi' => 'required|string|max:255',
            'deskripsi' => 'required',
            'link_materi' => 'nullable|url'
        ]);

        $materi = Materi::findOrFail($id);
        
        if ($materi->guru_id != Auth::user()->guru->id) {
            abort(403, 'Akses Ditolak.');
        }

        $is_tugas = $request->has('is_tugas') ? 1 : 0;

        $materi->update([
            'judul_materi' => $request->judul_materi,
            'deskripsi' => $request->deskripsi,
            'link_materi' => $request->link_materi,
            'is_tugas' => $is_tugas,
        ]);

        return redirect()->route('guru.elearning.materi.list', [$materi->kelas_id, $materi->mapel_id])
                         ->with('success', 'Materi/Tugas berhasil diperbarui!');
    }

    // 7. Menghapus Materi
    public function destroyMateri($id)
    {
        $materi = Materi::findOrFail($id);
        
        if ($materi->guru_id != Auth::user()->guru->id) {
            abort(403, 'Akses Ditolak.');
        }

        $kelas_id = $materi->kelas_id;
        $mapel_id = $materi->mapel_id;

        $materi->delete();

        return redirect()->route('guru.elearning.materi.list', [$kelas_id, $mapel_id])
                         ->with('success', 'Materi berhasil dihapus!');
    }

    // 8. Menampilkan Rekap Absensi Siswa yang Membaca Materi
    public function rekapMateri($id)
    {
        $materi = Materi::with(['kelas', 'mapel'])->findOrFail($id);
        
        if ($materi->guru_id != Auth::user()->guru->id) {
            abort(403, 'Akses Ditolak.');
        }

        $semua_siswa = \App\Models\Siswa::where('kelas_id', $materi->kelas_id)->orderBy('nama', 'asc')->get();
        $log_baca = \App\Models\MateriLog::where('materi_id', $id)->get()->keyBy('siswa_id');

        return view('guru.elearning.rekap_materi', compact('materi', 'semua_siswa', 'log_baca'));
    }

    // 9. Rekap Progres Belajar Seluruh Siswa dalam Satu Kelas
    public function rekapKelas($kelas_id, $mapel_id)
    {
        $guru_id = Auth::user()->guru->id;
        
        $kelas = \App\Models\Kelas::findOrFail($kelas_id);
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        $total_materi = Materi::where('guru_id', $guru_id)
                            ->where('kelas_id', $kelas_id)
                            ->where('mapel_id', $mapel_id)
                            ->count();

        $materi_ids = Materi::where('guru_id', $guru_id)
                            ->where('kelas_id', $kelas_id)
                            ->where('mapel_id', $mapel_id)
                            ->pluck('id');

        $semua_siswa = \App\Models\Siswa::where('kelas_id', $kelas_id)->orderBy('nama', 'asc')->get();

        $progres_siswa = [];
        foreach ($semua_siswa as $siswa) {
            $jumlah_dibaca = \App\Models\MateriLog::whereIn('materi_id', $materi_ids)
                                ->where('siswa_id', $siswa->id)
                                ->count();
            
            $progres_siswa[] = [
                'siswa' => $siswa,
                'jumlah_dibaca' => $jumlah_dibaca,
                'persentase' => $total_materi > 0 ? round(($jumlah_dibaca / $total_materi) * 100) : 0
            ];
        }

        return view('guru.elearning.rekap_kelas', compact('kelas', 'mapel', 'total_materi', 'progres_siswa', 'kelas_id', 'mapel_id'));
    }

    // 4. Proses Simpan / Kirim Tugas oleh Siswa
    public function submitTugas(Request $request, $materi_id)
    {
        $request->validate([
            'link_jawaban' => 'required|url',
            'catatan_siswa' => 'nullable|string|max:500'
        ]);

        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            abort(403, 'Akses Ditolak.');
        }

        \App\Models\PengumpulanTugas::updateOrCreate(
            [
                'tugas_id' => $materi_id,
                'siswa_id' => $siswa->id,
            ],
            [
                'link_jawaban' => $request->link_jawaban,
                'catatan_siswa' => $request->catatan_siswa,
            ]
        );

        return redirect()->back()->with('success', 'Tugas berhasil dikirimkan!');
    }

    // 10. Menampilkan Daftar Pengumpulan Tugas oleh Siswa
    public function cekTugas($id)
    {
        $materi = Materi::with(['kelas', 'mapel'])->findOrFail($id);
        
        if ($materi->guru_id != Auth::user()->guru->id) {
            abort(403, 'Akses Ditolak.');
        }

        $semua_siswa = \App\Models\Siswa::where('kelas_id', $materi->kelas_id)->orderBy('nama', 'asc')->get();
        $pengumpulan = \App\Models\PengumpulanTugas::where('tugas_id', $id)->get()->keyBy('siswa_id');

        return view('guru.elearning.cek_tugas', compact('materi', 'semua_siswa', 'pengumpulan'));
    }
}