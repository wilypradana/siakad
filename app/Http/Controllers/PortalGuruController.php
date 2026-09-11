<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guru;
use App\Models\Pembelajaran; // UBAH: Gunakan model Pembelajaran
use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\Mapel;

class PortalGuruController extends Controller
{
    public function dashboard()
    {
        $guru = Guru::where('user_id', Auth::id())->first();

        if (!$guru) {
            return redirect('/')->with('error', 'Profil guru tidak ditemukan.');
        }

        // UBAH: Tarik data dari Pembelajaran, bukan Jadwal
        $jadwal_mengajar = Pembelajaran::with(['mapel', 'kelas'])
                                 ->where('guru_id', $guru->id)
                                 ->select('mapel_id', 'kelas_id')
                                 ->distinct()
                                 ->get();

        return view('guru.dashboard', compact('guru', 'jadwal_mengajar'));
    }

    // Menampilkan Form Input Nilai
    public function inputNilai(Request $request, $kelas_id, $mapel_id)
    {
        $guru = auth()->user()->guru;
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);
        $data_siswa = \App\Models\Siswa::where('kelas_id', $kelas_id)->orderBy('nama', 'asc')->get();
        
        // Ambil semua Jenis Ujian untuk pilihan dropdown
        $data_jenis = \App\Models\JenisUjian::all();
        
        // Tangkap jenis ujian yang dipilih (default ke yang pertama jika belum dipilih)
        $jenis_terpilih = $request->jenis_ujian_id ?? ($data_jenis->first()->id ?? null);

        // Ambil nilai yang sudah ada berdasarkan jenis ujian tersebut
        $nilai_existing = \App\Models\Nilai::where('jenis_ujian_id', $jenis_terpilih)
                            ->where('mapel_id', $mapel_id)
                            ->get()
                            ->keyBy('siswa_id');

        return view('guru.input_nilai', compact('guru', 'mapel', 'data_siswa', 'data_jenis', 'jenis_terpilih', 'kelas_id', 'nilai_existing'));
    }

    // Menyimpan Nilai Kurikulum Merdeka
    public function simpanNilai(Request $request, $kelas_id, $mapel_id)
    {
        foreach ($request->nilai as $siswa_id => $skor) {
            // Hitung Nilai Akhir otomatis
            $jml_sumatif = (($skor['s1'] ?? 0) + ($skor['s2'] ?? 0) + ($skor['s3'] ?? 0)) / 3;
            $nilai_akhir = ($jml_sumatif + ($skor['ujian'] ?? 0)) / 2;

            \App\Models\Nilai::updateOrCreate(
                [
                    'siswa_id' => $siswa_id,
                    'mapel_id' => $mapel_id,
                    'jenis_ujian_id' => $request->jenis_ujian_id
                ],
                [
                    'sumatif_1' => $skor['s1'],
                    'sumatif_2' => $skor['s2'],
                    'sumatif_3' => $skor['s3'],
                    'nilai_ujian' => $skor['ujian'],
                    'nilai_akhir' => round($nilai_akhir)
                ]
            );
        }

        return back()->with('success', 'Nilai berhasil disimpan!');
    }
}