<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\JenisUjian;
use App\Models\Guru;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UjianController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $data_jenis = \App\Models\JenisUjian::all();
        $data_kelas = \App\Models\Kelas::all(); 
        $data_guru  = \App\Models\Guru::all(); 

        if ($user->role == 'admin') {
            $data_ujian = \App\Models\Ujian::with(['mapel', 'kelas', 'jenisUjian', 'guru'])->orderBy('created_at', 'desc')->get();
            $data_mapel = \App\Models\Mapel::all();
            
        } elseif ($user->role == 'guru') {
            $guru_id = \App\Models\Guru::where('user_id', $user->id)->value('id');
            if (!$guru_id) $guru_id = 0; 

            $data_ujian = \App\Models\Ujian::with(['mapel', 'kelas', 'jenisUjian', 'guru'])
                                           ->where('guru_id', $guru_id)
                                           ->orderBy('created_at', 'desc')
                                           ->get();

            $mapel_diampu_ids = DB::table('pembelajarans')
                                  ->where('guru_id', $guru_id)
                                  ->pluck('mapel_id')
                                  ->unique();

            $data_mapel = \App\Models\Mapel::whereIn('id', $mapel_diampu_ids)->get();
        }

        return view('admin.ujian.index', compact('data_ujian', 'data_jenis', 'data_kelas', 'data_mapel', 'data_guru'));
    }

    public function storeJenis(Request $request)
    {
        $request->validate(['nama_jenis' => 'required']);
        JenisUjian::create($request->all());
        return back()->with('success', 'Jenis Ujian baru berhasil ditambahkan!');
    }

    public function store(Request $request)
    {
        // Validasi diperbarui untuk mengakomodasi metode ujian[cite: 6]
        $data = $request->validate([
            'judul_ujian' => 'required',
            'jenis_ujian_id' => 'required',
            'kelas_id' => 'required|array',
            'mapel_id' => 'required',
            'metode_ujian' => 'required|in:gform,cbt', 
            'link_gform' => 'nullable|url', // Tidak lagi required
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'durasi' => 'required|numeric|min:10',
            'guru_id' => 'nullable'
        ]);

        $guru_id = $request->guru_id;
        if (auth()->user()->role == 'guru') {
            $guru_id = \App\Models\Guru::where('user_id', auth()->id())->value('id');
        }

        // Paksa link_gform menjadi null jika metode yang dipilih adalah cbt
        $link_gform = $request->metode_ujian == 'cbt' ? null : $request->link_gform;

        foreach ($request->kelas_id as $kelas) {
            \App\Models\Ujian::create([
                'judul_ujian' => $request->judul_ujian,
                'jenis_ujian_id' => $request->jenis_ujian_id,
                'kelas_id' => $kelas,
                'mapel_id' => $request->mapel_id,
                'metode_ujian' => $request->metode_ujian,
                'link_gform' => $link_gform, // Variabel yang sudah difilter
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'durasi' => $request->durasi,
                'guru_id' => $guru_id
            ]);
        }

        return back()->with('success', 'Jadwal Ujian berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        // Validasi diperbarui[cite: 6]
        $data = $request->validate([
            'judul_ujian' => 'required',
            'jenis_ujian_id' => 'required',
            'kelas_id' => 'required',
            'mapel_id' => 'required',
            'metode_ujian' => 'required|in:gform,cbt',
            'link_gform' => 'nullable|url', // Tidak lagi required
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'durasi' => 'required|numeric|min:10',
            'guru_id' => 'nullable'
        ]);

        $ujian = Ujian::findOrFail($id);
        
        if (auth()->user()->role == 'guru') {
            $guru_id = \App\Models\Guru::where('user_id', auth()->id())->value('id');
            if ($ujian->guru_id != $guru_id) {
                return back()->with('error', 'Anda tidak berhak mengedit ujian ini.');
            }
        }

        // Hapus link_gform jika guru mengubah metode dari gform menjadi cbt
        $link_gform = $request->metode_ujian == 'cbt' ? null : $request->link_gform;

        $ujian->update([
            'judul_ujian' => $request->judul_ujian,
            'jenis_ujian_id' => $request->jenis_ujian_id,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'metode_ujian' => $request->metode_ujian,
            'link_gform' => $link_gform,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'durasi' => $request->durasi,
            'guru_id' => $request->guru_id ?? $ujian->guru_id
        ]);
        
        return back()->with('success', 'Jadwal Ujian berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Ujian::findOrFail($id)->delete();
        return back()->with('success', 'Jadwal ujian berhasil dihapus!');
    }

    public function updateJenis(Request $request, $id)
    {
        $request->validate(['nama_jenis' => 'required']);
        JenisUjian::findOrFail($id)->update($request->all());
        return back()->with('success', 'Jenis Ujian berhasil diperbarui!');
    }

    public function destroyJenis($id)
    {
        \App\Models\JenisUjian::findOrFail($id)->delete();
        return back()->with('success', 'Jenis ujian berhasil dihapus!');
    }

    public function resetStatusUjian(Request $request, $ujian_id)
    {
        $request->validate(['siswa_id' => 'required']);

        // 1. Hapus status selesai & nilai akhir di dashboard
        DB::table('hasil_ujians')
            ->where('ujian_id', $ujian_id)
            ->where('siswa_id', $request->siswa_id)
            ->delete();

        // 2. Hapus rekam jejak jawaban (khusus jika metode CBT)
        DB::table('jawaban_siswas')
            ->where('ujian_id', $ujian_id)
            ->where('siswa_id', $request->siswa_id)
            ->delete();

        // 3. (Opsional) Hapus tabel lama untuk berjaga-jaga
        DB::table('ujian_siswas')
            ->where('ujian_id', $ujian_id)
            ->where('siswa_id', $request->siswa_id)
            ->delete();

        return back()->with('success', 'Akses ujian berhasil di-reset. Siswa kini bisa mengerjakan ulang dari awal!');
    }

    public function bulkDelete(Request $request)
    {
        if ($request->ids) {
            \App\Models\Ujian::whereIn('id', $request->ids)->delete();
            return back()->with('success', count($request->ids) . ' Jadwal ujian berhasil dihapus sekaligus!');
        }
        return back()->with('error', 'Pilih minimal satu jadwal ujian untuk dihapus!');
    }

    public function getKelasByMapel(Request $request)
    {
        $mapel_id = $request->mapel_id;
        $user = auth()->user();

        if ($user->role == 'admin') {
            $kelas = \App\Models\Kelas::orderBy('nama_kelas', 'asc')->get();
        } else {
            $guru_id = \App\Models\Guru::where('user_id', $user->id)->value('id');
            $kelas = \App\Models\Kelas::whereIn('id', function($query) use ($guru_id, $mapel_id) {
                $query->select('kelas_id')
                      ->from('pembelajarans')
                      ->where('guru_id', $guru_id)
                      ->where('mapel_id', $mapel_id);
            })->orderBy('nama_kelas', 'asc')->get();
        }

        return response()->json($kelas);
    }

    public function hasilUjian($id)
    {
        // Ambil data ujian beserta relasi mapel dan kelas
        $ujian = \App\Models\Ujian::with(['mapel', 'kelas'])->findOrFail($id);
        
        // Ambil daftar seluruh siswa di kelas tersebut
        $siswas = \App\Models\Siswa::where('kelas_id', $ujian->kelas_id)->orderBy('nama', 'asc')->get();
        
        // Ambil data hasil ujian dari database, lalu jadikan array dengan key siswa_id agar mudah dicocokkan
        $hasil = \App\Models\HasilUjian::where('ujian_id', $id)->get()->keyBy('siswa_id');

        return view('admin.ujian.hasil', compact('ujian', 'siswas', 'hasil'));
    }
    
    public function toggleStatus($id)
    {
        $ujian = \App\Models\Ujian::findOrFail($id);
        $ujian->is_active = !$ujian->is_active; // Membalikkan status 0/1
        $ujian->save();
        
        $pesan = $ujian->is_active ? 'Akses ujian DIBUKA! Siswa sekarang bisa masuk.' : 'Akses ujian DITUTUP (Dikunci)!';
        return back()->with('success', $pesan);
    }

    public function bulkToggle(Request $request)
    {
        if (!$request->ids) {
            return back()->with('error', 'Pilih minimal satu jadwal ujian dengan mencentang kotaknya!');
        }

        // Ambil status dari parameter URL (1 untuk Buka, 0 untuk Tutup)
        $status = $request->query('status'); 

        \App\Models\Ujian::whereIn('id', $request->ids)->update(['is_active' => $status]);

        $kata = $status == 1 ? 'DIBUKA' : 'DITUTUP';
        return back()->with('success', count($request->ids) . " jadwal ujian berhasil $kata serentak!");
    }

    public function resetSemua($ujian_id)
    {
        // 1. Hapus semua data hasil ujian
        DB::table('hasil_ujians')->where('ujian_id', $ujian_id)->delete();
        // 2. Hapus rekam jejak klik jawaban CBT
        DB::table('jawaban_siswas')->where('ujian_id', $ujian_id)->delete();
        // 3. (Opsional) Bersihkan jejak ujian siswas
        DB::table('ujian_siswas')->where('ujian_id', $ujian_id)->delete();

        return back()->with('success', 'Seluruh data ujian berhasil di-reset. Semua siswa di kelas ini kembali berstatus Belum Mengerjakan!');
    }

    public function tutupPaksa($ujian_id)
    {
        $ujian = \App\Models\Ujian::findOrFail($ujian_id);
        $siswas = \App\Models\Siswa::where('kelas_id', $ujian->kelas_id)->get();
        $total_soal = \App\Models\Soal::where('ujian_id', $ujian_id)->count();
        $hitung_ditutup = 0;

        if ($ujian->metode_ujian == 'cbt' && $total_soal > 0) {
            foreach ($siswas as $siswa) {
                // Cek apakah siswa belum submit (belum punya nilai akhir)
                $sudah_selesai = \App\Models\HasilUjian::where('ujian_id', $ujian_id)
                                    ->where('siswa_id', $siswa->id)->exists();

                if (!$sudah_selesai) {
                    // Tarik paksa jawaban yang ada dan hitung nilainya
                    $jumlah_benar = \App\Models\JawabanSiswa::where('ujian_id', $ujian_id)
                                        ->where('siswa_id', $siswa->id)
                                        ->where('is_benar', 1)->count();
                                        
                    $jumlah_dijawab = \App\Models\JawabanSiswa::where('ujian_id', $ujian_id)
                                        ->where('siswa_id', $siswa->id)->count();

                    $jumlah_salah = $jumlah_dijawab - $jumlah_benar;
                    $nilai_akhir = ($jumlah_benar / $total_soal) * 100;

                    \App\Models\HasilUjian::create([
                        'ujian_id' => $ujian_id, 
                        'siswa_id' => $siswa->id,
                        'jumlah_benar'  => $jumlah_benar,
                        'jumlah_salah'  => $jumlah_salah,
                        'nilai_akhir'   => round($nilai_akhir)
                    ]);
                    $hitung_ditutup++;
                }
            }
        }

        // Kunci (gembok) portal ujian ini dari dashboard
        $ujian->update(['is_active' => 0]);

        return back()->with('success', "Ujian berhasil ditutup paksa! Sebanyak $hitung_ditutup siswa yang masih terjebak/belum klik Selesai telah dinilai secara otomatis.");
    }
}