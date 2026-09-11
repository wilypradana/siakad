<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with('kelas');

        // Filter berdasarkan request kelas_id
        if ($request->filled('filter_kelas')) {
            $query->where('kelas_id', $request->filter_kelas);
        }

        // UBAH: Gunakan paginate() alih-alih get()
        // Angka 50 menunjukkan jumlah data maksimal yang tampil per halaman
        $data_siswa = $query->paginate(50)->withQueryString();
        
        $data_kelas = Kelas::all();
        
        return view('admin.siswa.index', compact('data_siswa', 'data_kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswas,nis',
            'nama' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->nama,
                'username' => $request->nis,
                'email' => $request->nis . '@siswa.com',
                // Password diubah menjadi otomatis menggunakan NIS
                'password' => Hash::make($request->nis),
                'role' => 'siswa',
            ]);

            $nama_foto = null;
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $nama_foto = time() . '_' . $request->nis . '.' . $file->getClientOriginalExtension();
                
                // Perintah ini akan memaksa file masuk ke storage/app/public/siswa
                $file->storeAs('siswa', $nama_foto, 'public'); 
            }

            Siswa::create([
                'user_id' => $user->id,
                'kelas_id' => $request->kelas_id,
                'nis' => $request->nis,
                'nama' => $request->nama,
                'no_hp' => $request->no_hp,
                'nama_ortu' => $request->nama_ortu,
                'alamat' => $request->alamat,
                'foto' => $nama_foto,
                'status' => 'aktif',
                'status_bayar' => 0,    
                'nomor_kartu' => ''
            ]);

            DB::commit();
            return back()->with('success', 'Data Siswa berhasil ditambahkan!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal simpan: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $siswa = Siswa::findOrFail($id);
            
            // Cek ketersediaan user sebelum update untuk menghindari error
            if ($siswa->user) {
                $siswa->user->update(['name' => $request->nama]);
            }

            $nama_foto = $siswa->foto;
            if ($request->hasFile('foto')) {
                // Perbaikan path saat menghapus foto lama
                if ($siswa->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists('siswa/' . $siswa->foto)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete('siswa/' . $siswa->foto);
                }
                $file = $request->file('foto');
                $nama_foto = time() . '_' . $siswa->nis . '.' . $file->getClientOriginalExtension();
                $file->storeAs('siswa', $nama_foto, 'public'); 
            }

            // KUNCI PERBAIKAN: Gunakan "??" untuk mempertahankan data lama jika input kosong
            $siswa->update([
                'kelas_id'  => $request->kelas_id,
                'nama'      => $request->nama,
                'no_hp'     => $request->no_hp ?? $siswa->no_hp,
                'nama_ortu' => $request->nama_ortu ?? $siswa->nama_ortu,
                'alamat'    => $request->alamat ?? $siswa->alamat,
                'status'    => $request->status ?? $siswa->status,
                'foto'      => $nama_foto
            ]);

            DB::commit();
            return back()->with('success', 'Data siswa berhasil diupdate!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $siswa = Siswa::findOrFail($id);

            // 1. Simpan ID user sebelum data siswa dihapus
            $user_id = $siswa->user_id;

            // 2. Hapus file fisik dengan menyebutkan DISK-nya secara spesifik
            if ($siswa->foto) {
                if (Storage::disk('public')->exists('siswa/' . $siswa->foto)) {
                    Storage::disk('public')->delete('siswa/' . $siswa->foto);
                }
            }

            // 3. Hapus data siswa di tabel siswas
            $siswa->delete();

            // 4. Hapus juga data akun login di tabel users
            if ($user_id) {
                \App\Models\User::where('id', $user_id)->delete();
            }

            DB::commit();
            return back()->with('success', 'Data siswa beserta akun login berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal hapus: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048',
            'kelas_id' => 'required' 
        ]);

        $kelas_id_terpilih = $request->kelas_id;

        if ($request->hasFile('file')) {
            $fileData = fopen($request->file('file')->getPathname(), 'r');
            fgetcsv($fileData); 

            // MULAI TRANSAKSI DATABASE 
            \Illuminate\Support\Facades\DB::beginTransaction();

            try {
                while (($row = fgetcsv($fileData, 1000, ",")) !== false) {
                    if (count($row) == 1 && strpos($row[0], ';') !== false) {
                        $row = explode(';', $row[0]);
                    }

                    if (isset($row[0]) && isset($row[1]) && trim($row[0]) != '') {
                        $nis = trim($row[0]);
                        $nama = trim($row[1]);

                        $user = \App\Models\User::firstOrCreate(
                            ['username' => $nis], 
                            [
                                'name'     => $nama,
                                'email'    => $nis . '@smk.com', 
                                'password' => bcrypt($nis), 
                                'role'     => 'siswa'
                            ]
                        );

                        \App\Models\Siswa::updateOrCreate(
                            ['nis' => $nis], 
                            [
                                'nama'         => $nama,
                                'kelas_id'     => $kelas_id_terpilih,
                                'user_id'      => $user->id,
                                'status_bayar' => 0,
                                'nomor_kartu'  => ''
                            ]
                        );
                    }
                }
                
                // SIMPAN KE DATABASE SEKALIGUS
                \Illuminate\Support\Facades\DB::commit();
                
            } catch (\Exception $e) {
                // BATALKAN JIKA ADA ERROR
                \Illuminate\Support\Facades\DB::rollBack();
                fclose($fileData);
                return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }

            fclose($fileData);
        }
        
        return back()->with('success', 'Data Siswa dan Akun Login berhasil dibuat!');
    }

    public function export()
    {
        // 1. Nama file saat di-download
        $nama_file = 'Data_Siswa_SMK_Mulia_Buana.csv';
        
        // 2. Ambil semua data siswa (urutkan berdasarkan kelas dan nama)
        $data_siswa = \App\Models\Siswa::with('kelas')
            ->join('kelas', 'siswas.kelas_id', '=', 'kelas.id')
            ->orderBy('kelas.nama_kelas', 'asc')
            ->orderBy('siswas.nama', 'asc')
            ->select('siswas.*') // Pastikan hanya menyeleksi data siswa
            ->get();

        // 3. Atur format agar browser tahu ini file CSV yang harus didownload
        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$nama_file",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        // 4. Proses penulisan baris per baris ke dalam file
        $callback = function() use($data_siswa) {
            $file = fopen('php://output', 'w');
            
            // Tulis baris Judul (Header) di baris pertama Excel
            fputcsv($file, array('NIS', 'Nama Lengkap Siswa', 'Kelas'));

            // Looping data siswa dari database
            foreach ($data_siswa as $siswa) {
                fputcsv($file, array(
                    $siswa->nis, 
                    $siswa->nama, 
                    $siswa->kelas ? $siswa->kelas->nama_kelas : 'Belum Ada Kelas'
                ));
            }

            fclose($file);
        };

        // 5. Lempar file ke browser untuk di-download
        return response()->stream($callback, 200, $headers);
    }

    public function bulkDelete(Request $request)
    {
        // Cek apakah ada ID yang dikirim dari checkbox
        if ($request->ids) {
            
            // Tambahan kode untuk menghapus User ID dari bulk delete
            $siswas = \App\Models\Siswa::whereIn('id', $request->ids)->get();
            $userIds = $siswas->pluck('user_id')->filter()->toArray();

            // Hapus semua siswa yang dipilih
            \App\Models\Siswa::whereIn('id', $request->ids)->delete();
            
            // Hapus semua user yang terasosiasi
            if (!empty($userIds)) {
                \App\Models\User::whereIn('id', $userIds)->delete();
            }
            
            return back()->with('success', count($request->ids) . ' data siswa beserta akun login berhasil dihapus sekaligus!');
        }

        return back()->with('error', 'Pilih minimal satu siswa untuk dihapus!');
    }

    public function tandaiLunas($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        // Generate Kode Unik: MB-2026-ABCDE
        $kodeUnik = "MB" . "-" . strtoupper(Str::random(5));
        
        $siswa->update([
            'status_bayar' => 1,
            'nomor_kartu' => $kodeUnik
        ]);

        return back()->with('success', 'Pembayaran Lunas! Kode Kartu: ' . $kodeUnik);
    }

    public function batalLunas($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        // Gunakan petik kosong '' sebagai pengganti null agar tidak error NOT NULL
        $siswa->update([
            'status_bayar' => 0,
            'nomor_kartu' => '' 
        ]);

        return back()->with('success', 'Status pembayaran ' . $siswa->nama . ' berhasil dibatalkan.');
    }

    public function cetakMassal(Request $request)
    {
        $ids = explode(',', $request->ids);
        $jenis_cetak = $request->query('jenis', 'semua'); // Tangkap parameter 'jenis'
        
        $query = \App\Models\Siswa::with('kelas')->whereIn('id', $ids);

        // Jika opsi yang dipilih adalah 'lunas', aktifkan filter status_bayar
        if ($jenis_cetak == 'lunas') {
            $query->where('status_bayar', 1);
        }

        $data_siswa = $query->get();

        if ($data_siswa->isEmpty()) {
            return "Gagal: Tidak ada data siswa yang sesuai untuk dicetak (Mungkin yang Anda pilih belum lunas).";
        }

        return view('admin.siswa.cetak_massal', compact('data_siswa'));
    }

    public function bulkLunas(Request $request)
    {
        if ($request->ids) {
            $siswas = Siswa::whereIn('id', $request->ids)->get();
            $jumlah_terupdate = 0;

            foreach ($siswas as $siswa) {
                // Hanya proses yang belum lunas
                if ($siswa->status_bayar == 0) {
                    $kodeUnik = "MB" . "-" . strtoupper(Str::random(5));
                    $siswa->update([
                        'status_bayar' => 1,
                        'nomor_kartu' => $kodeUnik
                    ]);
                    $jumlah_terupdate++;
                }
            }

            return back()->with('success', $jumlah_terupdate . ' data siswa berhasil ditandai lunas secara massal!');
        }

        return back()->with('error', 'Pilih minimal satu siswa!');
    }

    // FITUR BARU: Sinkronisasi seluruh password siswa lama menjadi NIS
    public function syncPasswordToNis()
    {
        $semua_siswa = \App\Models\Siswa::with('user')->get();
        $jumlah_terupdate = 0;

        foreach ($semua_siswa as $siswa) {
            if ($siswa->user) {
                $siswa->user->update([
                    'password' => Hash::make($siswa->nis)
                ]);
                $jumlah_terupdate++;
            }
        }

        return back()->with('success', $jumlah_terupdate . ' password siswa berhasil disinkronkan menjadi NIS masing-masing!');
    }
}