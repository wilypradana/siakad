<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;   // Panggil Model Guru
use App\Models\Siswa;  // Panggil Model Siswa
use App\Models\Kelas;  // Panggil Model Kelas
use App\Models\Mapel; 

class AdminController extends Controller
{
    public function dashboard()
    {
        // Menghitung jumlah data langsung dari database
        $jumlah_guru = Guru::count();
        $jumlah_siswa = Siswa::count();
        $jumlah_kelas = Kelas::count();
        $jumlah_mapel = Mapel::count(); // Sudah ada sebelumnya

        // Untuk siswa bermasalah, kita set 0 dulu karena tabel Pelanggaran belum dibuat
        $siswa_bermasalah = 0; 

        // PERBAIKAN: Masukkan 'jumlah_mapel' ke dalam compact
        return view('admin.dashboard', compact('jumlah_guru', 'jumlah_siswa', 'jumlah_kelas', 'jumlah_mapel', 'siswa_bermasalah'));
    }
}