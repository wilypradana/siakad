<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Mapel;
use App\Models\JenisUjian;
use App\Models\Kelas; // Tambahkan model Kelas

class NilaiController extends Controller
{
    public function index(Request $request)
    {
        $data_siswa = Siswa::all();
        $data_mapel = Mapel::all();
        $data_jenis = JenisUjian::all();
        $data_kelas = Kelas::all(); 

        // Hanya proses pencarian jika admin sudah memilih filter
        if ($request->filled('kelas_id') || $request->filled('mapel_id')) {
            $query = Nilai::with(['siswa', 'mapel', 'jenisUjian']);

            if ($request->filled('kelas_id')) {
                $query->whereHas('siswa', function($q) use ($request) {
                    $q->where('kelas_id', $request->kelas_id);
                });
            }

            if ($request->filled('mapel_id')) {
                $query->where('mapel_id', $request->mapel_id);
            }

            $data_nilai = $query->get();
        } else {
            // Kosongkan data jika halaman baru saja dibuka
            $data_nilai = collect(); 
        }

        return view('admin.nilai.index', compact('data_nilai', 'data_siswa', 'data_mapel', 'data_jenis', 'data_kelas'));
    }

    public function store(Request $request)
    {
        // Rumus Kurikulum Merdeka
        $s1 = $request->sumatif_1 ?? 0;
        $s2 = $request->sumatif_2 ?? 0;
        $s3 = $request->sumatif_3 ?? 0;
        $ujian = $request->nilai_ujian ?? 0;

        $jml_sumatif = ($s1 + $s2 + $s3) / 3;
        $nilai_akhir = round(($jml_sumatif + $ujian) / 2);

        Nilai::create([
            'siswa_id' => $request->siswa_id,
            'mapel_id' => $request->mapel_id,
            'jenis_ujian_id' => $request->jenis_ujian_id,
            'sumatif_1' => $s1,
            'sumatif_2' => $s2,
            'sumatif_3' => $s3,
            'nilai_ujian' => $ujian,
            'nilai_akhir' => $nilai_akhir
        ]);

        return back()->with('success', 'Data Nilai berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $nilai = Nilai::findOrFail($id);

        $s1 = $request->sumatif_1 ?? 0;
        $s2 = $request->sumatif_2 ?? 0;
        $s3 = $request->sumatif_3 ?? 0;
        $ujian = $request->nilai_ujian ?? 0;

        $jml_sumatif = ($s1 + $s2 + $s3) / 3;
        $nilai_akhir = round(($jml_sumatif + $ujian) / 2);

        $nilai->update([
            'siswa_id' => $request->siswa_id,
            'mapel_id' => $request->mapel_id,
            'jenis_ujian_id' => $request->jenis_ujian_id,
            'sumatif_1' => $s1,
            'sumatif_2' => $s2,
            'sumatif_3' => $s3,
            'nilai_ujian' => $ujian,
            'nilai_akhir' => $nilai_akhir
        ]);

        return back()->with('success', 'Data Nilai berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Nilai::destroy($id);
        return back()->with('success', 'Data Nilai berhasil dihapus!');
    }
}
