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
        if (!$siswa) return redirect()->route('siswa.dashboard')->with('error', 'Profil tidak ditemukan.');

        $ujian = \App\Models\Ujian::findOrFail($id);
        
        // PEMBLOKIR: Cek apakah guru sudah menekan tombol Mulai
        if (!$ujian->is_active) {
            return redirect()->route('siswa.dashboard')->with('error', 'Ujian belum dimulai! Silakan tunggu instruksi dari guru pengawas.');
        }

        return view('siswa.kerjakan_ujian', compact('ujian', 'siswa'));
    }
    public function selesaiUjian(Request $request, $id)
    {
        $ujian = \App\Models\Ujian::findOrFail($id);
        $siswa = \App\Models\Siswa::where('user_id', auth()->id())->first();

        // 1. JIKA UJIAN MENGGUNAKAN METODE CBT LOKAL
        if ($ujian->metode_ujian == 'cbt') {
            $total_soal = \App\Models\Soal::where('ujian_id', $id)->count();
            $total_soal = $ujian->soals()->count();
            if ($total_soal > 0) {
                $jumlah_benar = \App\Models\JawabanSiswa::where('ujian_id', $id)
                                    ->where('siswa_id', $siswa->id)
                                    ->where('is_benar', 1)
                                    ->count();
                                    
                $jumlah_dijawab = \App\Models\JawabanSiswa::where('ujian_id', $id)
                                    ->where('siswa_id', $siswa->id)
                                    ->count();

                $jumlah_salah = $jumlah_dijawab - $jumlah_benar;
                $nilai_akhir = ($jumlah_benar / $total_soal) * 100;

                \App\Models\HasilUjian::updateOrCreate(
                    ['ujian_id' => $id, 'siswa_id' => $siswa->id],
                    [
                        'jumlah_benar'  => $jumlah_benar,
                        'jumlah_salah'  => $jumlah_salah,
                        'nilai_akhir'   => round($nilai_akhir)
                    ]
                );
            }
        } 
        // 2. JIKA UJIAN MENGGUNAKAN G-FORM
        else {
            // Masukkan data penyelesaian dummy agar status di dashboard berubah menjadi "Selesai".
            // Nilai 0 ini nantinya bisa diperbarui secara manual oleh guru melalui rekap nilai.
            \App\Models\HasilUjian::updateOrCreate(
                ['ujian_id' => $id, 'siswa_id' => $siswa->id],
                [
                    'jumlah_benar'  => 0,
                    'jumlah_salah'  => 0,
                    'nilai_akhir'   => 0
                ]
            );
        }

        // Hapus sesi token agar siswa tidak bisa masuk lagi
        session()->forget('ujian_verified_' . $id);

        // Jika siswa disubmit paksa karena terdeteksi curang
        if ($request->has('pelanggaran') && $request->pelanggaran == '1') {
            return redirect()->route('siswa.dashboard')->with('error', 'UJIAN DIHENTIKAN PAKSA! Anda terdeteksi melakukan pelanggaran berulang kali.');
        }

        return redirect()->route('siswa.dashboard')->with('success', 'Ujian berhasil diselesaikan!');
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

// ========================================================
    // 1. MENGIRIM DATA SOAL KE BROWSER SISWA (TANPA KUNCI JAWABAN)
    // ========================================================
    public function getSoalCBT($ujian_id)
    {
        // Pastikan keamanan: Cek apakah siswa sudah verifikasi token ujian ini
    if (!session()->has('ujian_verified_' . $ujian_id)) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $siswa = \App\Models\Siswa::where('user_id', auth()->id())->first();
        
        // --- PERBAIKAN: Gunakan relasi pivot Many-to-Many ---
        $ujian = \App\Models\Ujian::findOrFail($ujian_id);
        $soals = $ujian->soals()->inRandomOrder()->get(); 
        // ----------------------------------------------------
        
        $jawaban_tersimpan = \App\Models\JawabanSiswa::where('ujian_id', $ujian_id)
                                ->where('siswa_id', $siswa->id)
                                ->pluck('jawaban', 'soal_id');
        $data_soal = [];
        
        foreach ($soals as $soal) {
            $data_soal[] = [
                'id'         => $soal->id,
                'pertanyaan' => $soal->pertanyaan,
                'gambar'     => $soal->gambar,
                'opsi_a'     => $soal->opsi_a,
                'opsi_b'     => $soal->opsi_b,
                'opsi_c'     => $soal->opsi_c,
                'opsi_d'     => $soal->opsi_d,
                'opsi_e'     => $soal->opsi_e,
                // PENTING: Kunci jawaban TIDAK DIKIRIM agar tidak bisa diretas lewat Inspect Element
                'jawaban_siswa' => $jawaban_tersimpan[$soal->id] ?? null 
            ];
        }

        return response()->json($data_soal);
    }

    // ========================================================
    // 2. MENANGKAP DAN MENYIMPAN KLIK JAWABAN SISWA
    // ========================================================
    public function simpanJawabanCBT(Request $request, $ujian_id)
    {
        if (!session()->has('ujian_verified_' . $ujian_id)) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $siswa = \App\Models\Siswa::where('user_id', auth()->id())->first();
        $soal = \App\Models\Soal::findOrFail($request->soal_id);

        // Langsung periksa apakah jawaban benar atau salah di sisi server
        $is_benar = ($soal->kunci_jawaban == $request->jawaban) ? 1 : 0;

        // updateOrCreate: Jika siswa mengubah jawaban, data lama akan ditimpa (bukan ditambah)
        \App\Models\JawabanSiswa::updateOrCreate(
            [
                'ujian_id' => $ujian_id,
                'siswa_id' => $siswa->id,
                'soal_id'  => $request->soal_id
            ],
            [
                'jawaban'  => $request->jawaban,
                'is_benar' => $is_benar
            ]
        );

        return response()->json(['status' => 'Berhasil disimpan']);
    }
}