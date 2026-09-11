<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\Guru; // <-- Ini kunci perbaikannya
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        // Mengambil semua kelas beserta relasi guru wali dan jumlah siswanya
        $data_kelas = Kelas::with('guru')->withCount('siswa')->get();
        
        // Mengambil semua data guru untuk dropdown pemilihan wali kelas
        $data_guru = Guru::all();

        return view('admin.kelas.index', compact('data_kelas', 'data_guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas,nama_kelas',
            'jurusan' => 'required'
            // guru_id tidak wajib divalidasi ketat karena opsional
        ]);

        Kelas::create($request->all());
        return back()->with('success', 'Kelas baru berhasil ditambahkan!');
    }

    // Fitur untuk melihat daftar siswa di kelas tertentu
    public function show($id)
    {
        $kelas = Kelas::findOrFail($id);
        $data_siswa = Siswa::where('kelas_id', $id)->get();
        return view('admin.kelas.show', compact('kelas', 'data_siswa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // Validasi agar nama kelas unik, kecuali untuk ID kelas ini sendiri
            'nama_kelas' => 'required|unique:kelas,nama_kelas,' . $id,
            'jurusan' => 'required'
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->all());

        return back()->with('success', 'Data kelas dan Wali Kelas berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        
        // Cek jika masih ada siswa, jangan bolehkan hapus kelas
        if ($kelas->siswa()->count() > 0) {
            return back()->with('error', 'Kelas tidak bisa dihapus karena masih ada siswanya!');
        }
        
        $kelas->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }
}