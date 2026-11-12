<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembelajaran; // Mengambil jadwal mapel berdasarkan kelas
use App\Models\Materi;
use Illuminate\Support\Facades\Auth;

class ElearningController extends Controller
{
    // 1. Menampilkan Daftar Mata Pelajaran yang dipelajari oleh Kelas Siswa
    public function index()
    {
        if (!Auth::user()->siswa) {
            abort(403, 'Akses Ditolak: Akun Anda bukan akun Siswa.');
        }

        $siswa = Auth::user()->siswa;
        $kelas_id = $siswa->kelas_id;

        // OPSI: Ambil hanya mapel yang sudah memiliki materi/tugas yang diunggah oleh guru untuk kelas ini,
        // atau ambil dari tabel pembelajaran yang spesifik.
        $mapel_ids_dengan_materi = Materi::where('kelas_id', $kelas_id)->pluck('mapel_id')->unique();

        // Ambil jadwal/pembelajaran berdasarkan mapel yang aktif memiliki materi, 
        // atau jika ingin menampilkan semua jadwal mapel kelas tapi disaring:
        $jadwal_mapel = Pembelajaran::with(['mapel', 'guru'])
                            ->where('kelas_id', $kelas_id)
                            ->get();

        return view('siswa.elearning.index', compact('jadwal_mapel', 'siswa'));
    }

    // 2. Menampilkan Daftar Materi Berdasarkan Mapel Tertentu di Kelas Siswa
    public function listMateri($mapel_id)
    {
        $siswa = Auth::user()->siswa;
        $kelas_id = $siswa->kelas_id;

        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        // Ambil materi untuk mapel dan kelas tersebut
        $materis = Materi::with(['guru'])
                    ->where('kelas_id', $kelas_id)
                    ->where('mapel_id', $mapel_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('siswa.elearning.list_materi', compact('materis', 'mapel', 'kelas_id'));
    }

    // 3. Halaman Detail Materi
    // 3. Halaman Detail Materi & Auto-Log Kehadiran Membaca
    public function show($id)
    {
        $materi = Materi::with(['guru', 'mapel'])->findOrFail($id);
        $siswa = Auth::user()->siswa;

        // Cek apakah siswa ini sudah pernah tercatat membaca materi ini
        if ($siswa) {
            \App\Models\MateriLog::firstOrCreate(
                [
                    'materi_id' => $materi->id,
                    'siswa_id' => $siswa->id,
                ],
                [
                    'read_at' => now(),
                ]
            );
        }
        
        return view('siswa.elearning.show', compact('materi'));
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
}