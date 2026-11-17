<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pembelajaran;
use App\Models\Materi;
use App\Models\MateriLog;
use App\Models\PengumpulanTugas;
use Illuminate\Support\Facades\Auth;

class ElearningController extends Controller
{
    // 1. Menampilkan Daftar Mata Pelajaran dengan Indikator Notifikasi Materi/Tugas Baru
    public function index()
    {
        if (!Auth::user()->siswa) {
            abort(403, 'Akses Ditolak: Akun Anda bukan akun Siswa.');
        }

        $siswa = Auth::user()->siswa;
        $kelas_id = $siswa->kelas_id;

        // Ambil seluruh materi/tugas untuk kelas siswa ini
        $semua_materi = Materi::where('kelas_id', $kelas_id)->get();

        // Ambil daftar ID materi yang sudah pernah dibuka oleh siswa ini
        $materi_sudah_dibaca = MateriLog::where('siswa_id', $siswa->id)
                                ->pluck('materi_id')
                                ->toArray();

        // Ambil daftar ID tugas yang sudah dikumpulkan oleh siswa ini
        $tugas_sudah_dikumpul = PengumpulanTugas::where('siswa_id', $siswa->id)
                                ->pluck('tugas_id')
                                ->toArray();

        // Hitung jumlah materi baru & tugas belum dikerjakan per mapel_id
        $notif_per_mapel = [];
        foreach ($semua_materi->groupBy('mapel_id') as $mapel_id => $items) {
            $belum_dibaca = $items->whereNotIn('id', $materi_sudah_dibaca)->count();
            $tugas_pending = $items->where('is_tugas', 1)
                                   ->whereNotIn('id', $tugas_sudah_dikumpul)
                                   ->count();

            $notif_per_mapel[$mapel_id] = [
                'belum_dibaca'  => $belum_dibaca,
                'tugas_pending' => $tugas_pending,
                'total_notif'   => max($belum_dibaca, $tugas_pending), // Memastikan tetap menyala jika tugas belum dikumpul
            ];
        }

        $jadwal_mapel = Pembelajaran::with(['mapel', 'guru'])
                            ->where('kelas_id', $kelas_id)
                            ->get();

        return view('siswa.elearning.index', compact('jadwal_mapel', 'siswa', 'notif_per_mapel'));
    }

    // 2. Menampilkan Daftar Materi Berdasarkan Mapel Tertentu di Kelas Siswa
    public function listMateri($mapel_id)
    {
        $siswa = Auth::user()->siswa;
        $kelas_id = $siswa->kelas_id;

        $mapel = \App\Models\Mapel::findOrFail($mapel_id);

        $materis = Materi::with(['guru'])
                    ->where('kelas_id', $kelas_id)
                    ->where('mapel_id', $mapel_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('siswa.elearning.list_materi', compact('materis', 'mapel', 'kelas_id'));
    }

    // 3. Halaman Detail Materi & Auto-Log Kehadiran Membaca
    public function show($id)
    {
        $materi = Materi::with(['guru', 'mapel'])->findOrFail($id);
        $siswa = Auth::user()->siswa;

        if ($siswa) {
            MateriLog::firstOrCreate(
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

        PengumpulanTugas::updateOrCreate(
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