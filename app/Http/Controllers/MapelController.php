<?php

namespace App\Http\Controllers;
use App\Models\Mapel;
use Illuminate\Http\Request;

class MapelController extends Controller {
    public function index() {
        $data_mapel = Mapel::all();
        return view('admin.mapel.index', compact('data_mapel'));
    }

    public function store(Request $request) {
        Mapel::create($request->all());
        return back()->with('success', 'Mata Pelajaran berhasil ditambah!');
    }

    public function update(Request $request, $id) {
        Mapel::findOrFail($id)->update($request->all());
        return back()->with('success', 'Mapel berhasil diupdate!');
    }

    public function destroy($id) {
        Mapel::findOrFail($id)->delete();
        return back()->with('success', 'Mapel berhasil dihapus!');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt|max:2048']);

        if ($request->hasFile('file')) {
            $fileData = fopen($request->file('file')->getPathname(), 'r');
            fgetcsv($fileData); // Lewati baris pertama (Judul/Header)

            while (($row = fgetcsv($fileData, 1000, ",")) !== false) {
                // Trik: Excel bahasa Indonesia sering pakai Titik Koma (;)
                if (count($row) == 1 && strpos($row[0], ';') !== false) {
                    $row = explode(';', $row[0]);
                }

                // Pastikan nama mapel di kolom A ada isinya
                if (isset($row[0]) && trim($row[0]) != '') {
                    
                    $nama_mapel = trim($row[0]);
                    
                    // Bersihkan titik/spasi agar kodenya cantik
                    $clean_name = preg_replace('/[^A-Za-z]/', '', $nama_mapel);
                    $kode_otomatis = strtoupper(substr($clean_name, 0, 3)) . '-' . rand(100, 999);

                    // firstOrCreate mencari dan membuat data
                    \App\Models\Mapel::firstOrCreate(
                        ['nama_mapel' => $nama_mapel],
                        [
                            'kode_mapel' => $kode_otomatis,
                            'kelompok'   => 'A' 
                        ]
                    );
                    
                } // Penutup pengecekan baris
            } // Penutup putaran (while) baca excel
            
            // Tutup file HARUS DI SINI (setelah semua baris selesai dibaca)
            fclose($fileData);
        }
        
        return back()->with('success', 'Data Mata Pelajaran berhasil diimport massal!');
    }
}
