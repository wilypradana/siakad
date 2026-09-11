<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Guru;

class JadwalController extends Controller
{
    public function index()
    {
        // Mengambil data jadwal dengan urutan hari kustom (Senin, Selasa, dst)
        $data_jadwal = Jadwal::with(['kelas', 'mapel', 'guru'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_mulai', 'asc')
            ->get();
        
        $data_kelas = Kelas::all();
        $data_mapel = Mapel::all();
        $data_guru = Guru::all();

        return view('admin.jadwal.index', compact('data_jadwal', 'data_kelas', 'data_mapel', 'data_guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'kelas_id' => 'required|array', // Harus array karena pakai Checkbox
            'mapel_id' => 'required',
            'guru_id' => 'required',
        ]);
        
        // Looping untuk menyimpan jadwal ke banyak kelas sekaligus
        foreach ($request->kelas_id as $kelas) {
            Jadwal::create([
                'hari' => $request->hari,
                'jam_mulai' => $request->jam_mulai, // Sesuai dengan nama input
                'jam_selesai' => $request->jam_selesai,
                'mapel_id' => $request->mapel_id,
                'guru_id' => $request->guru_id,
                'kelas_id' => $kelas
            ]);
        }

        return back()->with('success', 'Jadwal Ujian berhasil ditambahkan untuk kelas yang dipilih!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kelas_id' => 'required|array', // Wajib pilih minimal 1 kelas saat edit
        ]);

        // Pecah gabungan ID dari view menjadi array
        $ids = explode(',', $id);
        
        // Trik Cepat: Hapus jadwal lama di kelompok ini
        Jadwal::whereIn('id', $ids)->delete();
        
        // Insert jadwal baru dengan kelas-kelas hasil editan (Checkbox)
        foreach ($request->kelas_id as $kelas) {
            Jadwal::create([
                'hari' => $request->hari,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'mapel_id' => $request->mapel_id,
                'guru_id' => $request->guru_id,
                'kelas_id' => $kelas
            ]);
        }
        
        return back()->with('success', 'Jadwal Ujian massal berhasil diperbarui!');
    }

    public function destroy($id)
    {
        // Pecah gabungan ID dan hapus semuanya sekaligus
        $ids = explode(',', $id);
        Jadwal::whereIn('id', $ids)->delete();
        
        return back()->with('success', 'Jadwal Ujian untuk kelompok kelas tersebut berhasil dihapus!');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt|max:2048']);

        if ($request->hasFile('file')) {
            $fileData = fopen($request->file('file')->getPathname(), 'r');
            fgetcsv($fileData); // Lewati baris pertama (Header/Judul)

            $berhasil = 0; // Penghitung data yang sukses masuk

            while (($row = fgetcsv($fileData, 1000, ",")) !== false) {
                // Trik: Antisipasi jika Excel dipisah dengan Titik Koma (;)
                if (count($row) == 1 && strpos($row[0], ';') !== false) {
                    $row = explode(';', $row[0]);
                }

                // Pastikan ada 6 kolom di Excel (Kolom A sampai F)
                if (isset($row[0], $row[1], $row[2], $row[3], $row[4], $row[5])) {
                    
                    // Cari ID berdasarkan Nama yang diketik di Excel
                    $mapel = \App\Models\Mapel::where('nama_mapel', trim($row[3]))->first();
                    $guru  = \App\Models\Guru::where('nama', trim($row[4]))->first();
                    $kelas = \App\Models\Kelas::where('nama_kelas', trim($row[5]))->first();

                    // Jika Mapel, Guru, dan Kelas-nya cocok di database, baru simpan jadwalnya
                    if ($mapel && $guru && $kelas) {
                        \App\Models\Jadwal::create([
                            'hari' => trim($row[0]),
                            'jam_mulai' => trim($row[1]),
                            'jam_selesai' => trim($row[2]),
                            'mapel_id' => $mapel->id,
                            'guru_id' => $guru->id,
                            'kelas_id' => $kelas->id
                        ]);
                        $berhasil++;
                    }
                }
            }
            fclose($fileData);
            
            return back()->with('success', "Import selesai! Sebanyak $berhasil jadwal berhasil ditambahkan.");
        }
        
        return back()->with('error', 'Gagal membaca file!');
    }



    public function export()
    {
        $nama_file = 'Jadwal_Ujian_SMK_Mulia_Buana.csv';
        $data_jadwal = Jadwal::with(['kelas', 'mapel', 'guru'])
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$nama_file",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($data_jadwal) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Hari', 'Mulai', 'Selesai', 'Mata Pelajaran', 'Guru Pengawas', 'Kelas']);

            foreach ($data_jadwal as $j) {
                fputcsv($file, [
                    $j->hari, $j->jam_mulai, $j->jam_selesai, 
                    $j->mapel->nama_mapel ?? '-', 
                    $j->guru->nama ?? '-', 
                    $j->kelas->nama_kelas ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}