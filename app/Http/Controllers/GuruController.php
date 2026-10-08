<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GuruImport;
use App\Exports\GuruTemplateExport;
use Exception;

class GuruController extends Controller
{
    /**
     * Menampilkan daftar guru (READ)
     */
    public function index()
    {
        $data_guru = Guru::with('user')->latest()->get(); 
        return view('admin.guru.index', compact('data_guru'));
    }

    /**
     * Menyimpan guru baru (CREATE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nip'    => 'required|string|max:50|unique:users,username|unique:gurus,nip',
            'nama'   => 'required|string|max:255',
            'no_hp'  => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique'   => 'Gagal! NIP/Username sudah terdaftar.',
            'nama.required' => 'Nama lengkap wajib diisi.',
        ]);

            try {
                DB::beginTransaction();

                // 1. Buat Akun User Login
                $user = User::create([
                    'name'     => $request->nama,
                    'username' => $request->nip,
                    'email'    => $request->nip . '@muliabuana.com',
                    'password' => Hash::make($request->nip),
                    'role'     => 'guru',
                ]);

            // 2. Buat Data Profil Guru
            Guru::create([
                'user_id'      => $user->id,
                'nip'          => $request->nip,
                'nama'         => $request->nama,
                'no_hp'        => $request->no_hp,
                'alamat'       => $request->alamat,
                'status_aktif' => 1,
            ]);

            DB::commit();
            return back()->with('success', 'Data guru dan akun login berhasil ditambahkan!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal simpan data: ' . $e->getMessage());
        }
    }

    /**
     * Mengupdate data guru (UPDATE)
     */
    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'nama'   => 'required|string|max:255',
            'no_hp'  => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Update profil guru
            $guru->update([
                'nama'   => $request->nama,
                'no_hp'  => $request->no_hp,
                'alamat' => $request->alamat,
            ]);

            // 2. Update nama di akun user jika akunnya masih ada
            if ($guru->user) {
                $guru->user->update([
                    'name' => $request->nama,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Data guru berhasil diperbarui!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus guru (DELETE)
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $guru = Guru::findOrFail($id);
            $userId = $guru->user_id;

            // 1. Hapus data profil guru terlebih dahulu
            $guru->delete();

            // 2. Hapus akun user terkait agar NIP/Username bisa dipakai kembali
            if ($userId) {
                User::where('id', $userId)->delete();
            }

            DB::commit();
            return back()->with('success', 'Data guru dan akun login berhasil dihapus!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ], [
            'file_excel.required' => 'Pilih file Excel terlebih dahulu.',
            'file_excel.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv.'
        ]);

        try {
            Excel::import(new GuruImport, $request->file('file_excel'));
            return back()->with('success', 'Data guru massal berhasil di-import!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:6'
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.'
        ]);

        try {
            $guru = Guru::findOrFail($id);
            
            if ($guru->user) {
                $guru->user->update([
                    'password' => Hash::make($request->password)
                ]);
                return back()->with('success', 'Password guru ' . $guru->nama . ' berhasil diubah!');
            }
            
            return back()->with('error', 'Gagal: Akun login (User) untuk guru ini tidak ditemukan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cetakKartu($id)
    {
        $data_guru = Guru::where('id', $id)->get();
        return view('admin.guru.cetak_kartu', compact('data_guru'));
    }

    public function cetakKartuMassal()
    {
        $data_guru = Guru::orderBy('nama', 'asc')->get();
        return view('admin.guru.cetak_kartu', compact('data_guru'));
    }

    // TAMBAHKAN FUNGSI INI AGAR ROUTE SYNC PASSWORD BERJALAN
    public function syncPasswordToNip()
    {
        $semua_guru = Guru::with('user')->get();
        $jumlah_terupdate = 0;

        foreach ($semua_guru as $guru) {
            if ($guru->user) {
                $guru->user->update([
                    'password' => Hash::make($guru->nip)
                ]);
                $jumlah_terupdate++;
            }
        }

        return back()->with('success', $jumlah_terupdate . ' password guru berhasil diseragamkan dan disinkronkan menjadi NIP masing-masing!');
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
        $guru = auth()->user()->guru;
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