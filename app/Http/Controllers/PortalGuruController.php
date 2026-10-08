<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Guru;
use App\Models\Pembelajaran; 
use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\AbsensiSiswa;
use App\Models\JenisUjian; // WAJIB DIPANGGIL

class PortalGuruController extends Controller
{
    public function dashboard()
    {
        $guru = Guru::where('user_id', Auth::id())->first();

        if (!$guru) {
            return redirect('/')->with('error', 'Profil guru tidak ditemukan.');
        }

        $jadwal_mengajar = Pembelajaran::with(['mapel', 'kelas'])
                                 ->where('guru_id', $guru->id)
                                 ->select('mapel_id', 'kelas_id')
                                 ->distinct()
                                 ->get();

        return view('guru.dashboard', compact('guru', 'jadwal_mengajar'));
    }

    public function inputNilai(Request $request, $kelas_id, $mapel_id)
    {
        $guru = auth()->user()->guru;
        $mapel = \App\Models\Mapel::findOrFail($mapel_id);
        $data_siswa = \App\Models\Siswa::where('kelas_id', $kelas_id)->orderBy('nama', 'asc')->get();
        
        $data_jenis = \App\Models\JenisUjian::all();
        $jenis_terpilih = $request->jenis_ujian_id ?? ($data_jenis->first()->id ?? null);

        $nilai_existing = \App\Models\Nilai::where('jenis_ujian_id', $jenis_terpilih)
                            ->where('mapel_id', $mapel_id)
                            ->get()
                            ->keyBy('siswa_id');

        return view('guru.input_nilai', compact('guru', 'mapel', 'data_siswa', 'data_jenis', 'jenis_terpilih', 'kelas_id', 'nilai_existing'));
    }

    public function simpanNilai(Request $request, $kelas_id, $mapel_id)
    {
        // 1. Validasi pastikan Jenis Ujian dipilih
        $request->validate([
            'jenis_ujian_id' => 'required',
            'nilai' => 'required|array'
        ]);

        // 2. Cari data Jenis Ujian untuk mengecek namanya
        $jenis_ujian = JenisUjian::findOrFail($request->jenis_ujian_id);
        $is_sts = strpos(strtolower($jenis_ujian->nama_jenis), 'tengah semester') !== false;

        // 3. Looping untuk setiap siswa dan simpan nilainya beserta deskripsi TP
        foreach ($request->nilai as $siswa_id => $skor) {
            
            $s1 = $skor['s1'] ?? 0;
            $s2 = $skor['s2'] ?? 0;
            $s3 = $skor['s3'] ?? 0;
            $ujian = $skor['ujian'] ?? 0;

            // Tangkap input deskripsi TP dari guru
            $deskripsi_tercapai = $skor['deskripsi_tercapai'] ?? null;
            $deskripsi_peningkatan = $skor['deskripsi_peningkatan'] ?? null;

            if ($is_sts) {
                // Jika STS, nilai akhir mutlak mengambil nilai ujian murni
                $nilai_akhir = $ujian;
            } else {
                // Jika SAS/SAT, hitung rata-rata
                $jml_sumatif = ($s1 + $s2 + $s3) / 3;
                $nilai_akhir = round(($jml_sumatif + $ujian) / 2);
            }

            // Simpan atau Update ke tabel 'nilais'
            Nilai::updateOrCreate(
                [
                    'siswa_id' => $siswa_id,
                    'mapel_id' => $mapel_id,
                    'jenis_ujian_id' => $request->jenis_ujian_id,
                ],
                [
                    'sumatif_1' => $s1,
                    'sumatif_2' => $s2,
                    'sumatif_3' => $s3,
                    'nilai_ujian' => $ujian,
                    'nilai_akhir' => $nilai_akhir,
                    'deskripsi_tercapai' => $deskripsi_tercapai,       // Tambahan kolom TP tercapai
                    'deskripsi_peningkatan' => $deskripsi_peningkatan // Tambahan kolom TP peningkatan
                ]
            );
        }

        return back()->with('success', 'Nilai dan Deskripsi TP ' . $jenis_ujian->nama_jenis . ' berhasil disimpan!');
    }
    public function inputAbsensi(Request $request, $kelas_id, $mapel_id)
    {
        $guru = auth()->user()->guru;
        $mapel = Mapel::findOrFail($mapel_id);
        $kelas = Kelas::findOrFail($kelas_id);
        $data_siswa = Siswa::where('kelas_id', $kelas_id)->orderBy('nama', 'asc')->get();
        
        // Tanggal default hari ini jika tidak dipilih
        $tanggal = $request->tanggal ?? date('Y-m-d');

        // Ambil data absensi yang sudah ada pada tanggal tersebut
        $absensi_existing = AbsensiSiswa::where('kelas_id', $kelas_id)
                            ->where('mapel_id', $mapel_id)
                            ->where('tanggal', $tanggal)
                            ->get()
                            ->keyBy('siswa_id');

        return view('guru.input_absensi', compact('guru', 'mapel', 'kelas', 'data_siswa', 'tanggal', 'absensi_existing'));
    }

    public function simpanAbsensi(Request $request, $kelas_id, $mapel_id)
    {
        // Ubah dari auth()->user()->guru menjadi query ini agar tidak error
        $guru = Guru::where('user_id', Auth::id())->first();

        if (!$guru) {
            return back()->with('error', 'Profil guru tidak ditemukan.');
        }

        $request->validate([
            'tanggal' => 'required|date',
            'status' => 'required|array'
        ]);

        foreach ($request->status as $siswa_id => $status_kehadiran) {
            AbsensiSiswa::updateOrCreate(
                [
                    'siswa_id' => $siswa_id,
                    'mapel_id' => $mapel_id,
                    'tanggal' => $request->tanggal,
                ],
                [
                    'kelas_id' => $kelas_id,
                    'guru_id' => $guru->id,
                    'status' => $status_kehadiran
                ]
            );
        }

        return back()->with('success', 'Absensi tanggal ' . $request->tanggal . ' berhasil disimpan!');
    }
}   