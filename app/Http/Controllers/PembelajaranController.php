<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Guru;
use App\Models\Pembelajaran; // Pastikan Anda memiliki model/tabel ini
use Illuminate\Support\Facades\DB;

class PembelajaranController extends Controller
{
    public function index(Request $request)
    {
        $data_kelas = Kelas::all();
        $data_guru = Guru::orderBy('nama', 'asc')->get();
        
        $kelas_terpilih = null;
        $pembelajaran_aktif = [];
        $data_mapel = collect(); // Kosongkan mapel saat pertama kali halaman dibuka

        // Jika admin memilih kelas tertentu di dropdown
        if ($request->filled('kelas_id')) {
            $kelas_terpilih = Kelas::find($request->kelas_id);
            
            // 1. Tangkap jurusan dari kelas tersebut (contoh: 'MPLB')
            $jurusan_kelas = strtoupper($kelas_terpilih->jurusan);
            
            // 2. Tentukan mapel apa saja yang boleh tampil
            $kategori_diizinkan = ['UMUM', $jurusan_kelas];

            // Tambahan rumpun mapel sesuai jurusan
            if (in_array($jurusan_kelas, ['AKL', 'MPLB'])) {
                $kategori_diizinkan[] = 'BISM'; // Bisnis Manajemen
            } elseif (in_array($jurusan_kelas, ['TKJ', 'TAV'])) {
                $kategori_diizinkan[] = 'TEKNIK'; // Dasar Teknik
            }

            // 3. Panggil data mapel yang sudah difilter
            $data_mapel = Mapel::whereIn('jurusan_mapel', $kategori_diizinkan)
                               ->orderBy('kelompok', 'asc')
                               ->orderBy('nama_mapel', 'asc')
                               ->get();

            // Ambil data guru yang sudah diset di kelas ini sebelumnya
            $pembelajaran_aktif = DB::table('pembelajarans')
                                    ->where('kelas_id', $request->kelas_id)
                                    ->pluck('guru_id', 'mapel_id')
                                    ->toArray();
        }

        return view('admin.pembelajaran.index', compact('data_kelas', 'data_mapel', 'data_guru', 'kelas_terpilih', 'pembelajaran_aktif'));
    }

    public function storeMassal(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required',
            'guru_id' => 'array' // Menerima input array dari dropdown guru
        ]);

        DB::beginTransaction();
        try {
            // Bersihkan data lama untuk kelas ini agar tidak ganda
            DB::table('pembelajarans')->where('kelas_id', $request->kelas_id)->delete();

            $data_insert = [];
            foreach ($request->guru_id as $mapel_id => $guru_id) {
                if (!empty($guru_id)) {
                    $data_insert[] = [
                        'kelas_id' => $request->kelas_id,
                        'mapel_id' => $mapel_id,
                        'guru_id'  => $guru_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Simpan massal
            DB::table('pembelajarans')->insert($data_insert);
            
            DB::commit();
            return back()->with('success', 'Pemetaan guru untuk kelas berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function copyData(Request $request)
    {
        $request->validate([
            'kelas_tujuan_id' => 'required',
            'kelas_sumber_id' => 'required|different:kelas_tujuan_id'
        ]);

        DB::beginTransaction();
        try {
            // Bersihkan data kelas tujuan terlebih dahulu
            DB::table('pembelajarans')->where('kelas_id', $request->kelas_tujuan_id)->delete();

            // Ambil data pengajar dari kelas sumber
            $data_sumber = DB::table('pembelajarans')->where('kelas_id', $request->kelas_sumber_id)->get();

            // Persiapkan data baru untuk disisipkan
            $data_insert = [];
            foreach ($data_sumber as $item) {
                $data_insert[] = [
                    'kelas_id'   => $request->kelas_tujuan_id,
                    'mapel_id'   => $item->mapel_id,
                    'guru_id'    => $item->guru_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Masukkan data hasil copy ke database
            if(count($data_insert) > 0) {
                DB::table('pembelajarans')->insert($data_insert);
            }

            DB::commit();
            return back()->with('success', 'Data pengajar berhasil disalin dari kelas sumber!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyalin data: ' . $e->getMessage());
        }
    }

    // Menampilkan form input berdasarkan Guru
    public function createByGuru()
    {
        $data_guru = Guru::orderBy('nama', 'asc')->get();
        // Mengambil mapel dan mengurutkan berdasarkan kelompok dan nama
        $data_mapel = Mapel::orderBy('kelompok', 'asc')->orderBy('nama_mapel', 'asc')->get();
        // Mengambil kelas dan mengurutkan agar rapi saat ditampilkan
        $data_kelas = Kelas::orderBy('nama_kelas', 'asc')->get();

        return view('admin.pembelajaran.by_guru', compact('data_guru', 'data_mapel', 'data_kelas'));
    }

    // Memproses form saat tombol Simpan ditekan
    public function storeByGuru(Request $request)
    {
        $request->validate([
            'guru_id' => 'required',
            'mapel_id' => 'required',
            'kelas_id' => 'required|array|min:1', // Pastikan minimal 1 kelas diceklis
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $data_insert = [];
            
            foreach ($request->kelas_id as $kelas_id) {
                // Hapus data guru lama jika mapel ini sudah pernah diset di kelas tersebut
                \Illuminate\Support\Facades\DB::table('pembelajarans')
                    ->where('kelas_id', $kelas_id)
                    ->where('mapel_id', $request->mapel_id)
                    ->delete();

                // Siapkan data baru untuk dimasukkan
                $data_insert[] = [
                    'kelas_id'   => $kelas_id,
                    'mapel_id'   => $request->mapel_id,
                    'guru_id'    => $request->guru_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Simpan massal
            \Illuminate\Support\Facades\DB::table('pembelajarans')->insert($data_insert);
            
            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Berhasil menugaskan Guru ke ' . count($request->kelas_id) . ' kelas sekaligus!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}