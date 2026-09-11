<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Ujian;
use App\Models\Nilai;
use Carbon\Carbon;
use App\Models\UjianSiswa;

class PortalSiswaController extends Controller
{
    public function dashboard()
    {
        // 1. Ambil data profil siswa yang sedang login berdasarkan user_id
        $profil = Siswa::with('kelas')->where('user_id', Auth::id())->first();

        if (!$profil) {
            return redirect('/')->with('error', 'Profil siswa tidak ditemukan. Hubungi Admin.');
        }

        // 2. Tentukan hari ini dalam bahasa Indonesia
        Carbon::setLocale('id');
        $hari_ini = Carbon::now()->isoFormat('dddd');

        // 3. Ambil Jadwal Pelajaran siswa sesuai kelasnya hari ini
        $jadwal_hari_ini = Jadwal::with(['mapel', 'guru'])
                                 ->where('kelas_id', $profil->kelas_id)
                                 ->where('hari', $hari_ini)
                                 ->orderBy('jam_mulai', 'asc')
                                 ->get();
                                 
        // 4. Ambil Ujian yang ditujukan untuk kelas siswa tersebut
        $ujian_aktif = Ujian::with(['mapel', 'jenisUjian', 'guru'])
                            ->where('kelas_id', $profil->kelas_id)
                            ->where('waktu_selesai', '>=', now())
                            ->orderBy('waktu_mulai', 'asc')
                            ->get();

        // 5. PERBAIKAN: Ambil semua mata pelajaran berdasarkan Set Pembelajaran (pembelajarans) kelas siswa
        $mapel_kelas = DB::table('pembelajarans')
                         ->join('mapels', 'pembelajarans.mapel_id', '=', 'mapels.id')
                         ->where('pembelajarans.kelas_id', $profil->kelas_id)
                         ->select('mapels.id', 'mapels.nama_mapel')
                         ->orderBy('mapels.nama_mapel', 'asc')
                         ->get();

        // 6. Ambil daftar ID ujian yang sudah diselesaikan oleh siswa ini
        $ujian_selesai_ids = UjianSiswa::where('siswa_id', $profil->id)
                                       ->where('is_selesai', true)
                                       ->pluck('ujian_id')
                                       ->toArray();

        // 7. Ambil seluruh data ujian di kelas ini beserta status mapel-nya
        $semua_ujian_kelas = Ujian::where('kelas_id', $profil->kelas_id)->get();

        // Lempar semua data ke tampilan (View) dashboard siswa
        return view('siswa.dashboard', compact(
            'profil', 
            'mapel_kelas', 
            'jadwal_hari_ini', 
            'ujian_aktif', 
            'hari_ini', 
            'ujian_selesai_ids', 
            'semua_ujian_kelas'
        ));
    }

   // Cari bagian ini di PortalSiswaController.php dan samakan namanya
    public function kerjakanUjian($id)
    {
        $siswa = \App\Models\Siswa::where('user_id', auth()->user()->id)->first();

        if (!$siswa) {
            return redirect()->route('siswa.dashboard')->with('error', 'Profil tidak ditemukan.');
        }

        // CATAT OTOMATIS: Begitu siswa masuk ke halaman ini, tandai sebagai sudah dikerjakan
        //\App\Models\UjianSiswa::updateOrCreate(
        //    ['ujian_id' => $id, 'siswa_id' => $siswa->id],
        //    ['is_selesai' => true] // Langsung kunci statusnya
      //);

        $ujian = \App\Models\Ujian::findOrFail($id);
        return view('siswa.kerjakan_ujian', compact('ujian', 'siswa'));
    }

    // FUNGSI BARU: Tandai Selesai
    public function selesaiUjian($id) {
        $siswa = Siswa::where('user_id', Auth::id())->first();
        
        // Simpan ke database bahwa siswa ini SUDAH SELESAI
        UjianSiswa::updateOrCreate(
            ['ujian_id' => $id, 'siswa_id' => $siswa->id],
            ['is_selesai' => true]
        );

        return redirect()->route('siswa.dashboard')->with('success', 'Ujian telah diselesaikan. Terima kasih!');
    }


   public function cetakKartu()
{
    // Mengambil data siswa berdasarkan User yang login
    $siswa = \App\Models\Siswa::where('user_id', auth()->user()->id)->first();

    // Cek 1: Jika data siswa tidak ditemukan di tabel siswas
    if (!$siswa) {
        return redirect()->route('siswa.dashboard')->with('error', 'Profil Siswa belum terhubung dengan akun ini di database.');
    }

    // Cek 2: Jika belum lunas (status_bayar == 0)
    if ($siswa->status_bayar == 0) {
        return redirect()->route('siswa.dashboard')->with('error', 'Kartu Ujian belum tersedia karena administrasi belum lunas.');
    }

    // Cek 3: Jika kode unik belum di-generate
    if (empty($siswa->nomor_kartu)) {
        return redirect()->route('siswa.dashboard')->with('error', 'Kode unik belum dibuat. Silakan hubungi Admin.');
    }

    // Jika semua OK, baru tampilkan kartu
    return view('siswa.cetak_kartu', compact('siswa'));
}

 public function verifikasiKode(Request $request, $id)
{
    // Gunakan query manual berdasarkan user_id yang sedang login
    $siswa = \App\Models\Siswa::where('user_id', auth()->user()->id)->first();

    // Pastikan siswa ditemukan
    if (!$siswa) {
        return back()->with('error', 'Profil tidak ditemukan. Hubungi Admin IT.');
    }

    // Cek apakah kode yang diinput sama dengan nomor_kartu di database
    if ($request->kode_input === $siswa->nomor_kartu) {
        // Simpan status verifikasi di session
        session()->put('ujian_verified_' . $id, true);
        return back();
    }

    return back()->with('error', 'Kode Unik Kartu Salah!');
}
}