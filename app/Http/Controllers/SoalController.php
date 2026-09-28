<?php

namespace App\Http\Controllers;

use App\Imports\SoalImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SoalController extends Controller
{
    // 1. Menampilkan daftar soal di suatu ujian
   // Menampilkan daftar soal berdasarkan ID Ujian
   public function index($ujian_id)
    {
        $ujian = \App\Models\Ujian::with(['mapel', 'kelas'])->findOrFail($ujian_id);
        $soals = \App\Models\Soal::where('ujian_id', $ujian_id)->get();

        // Ambil daftar ujian lain dengan mata pelajaran yang sama untuk fitur Salin Soal
        $query_ujian_lain = \App\Models\Ujian::with('kelas')
            ->where('mapel_id', $ujian->mapel_id)
            ->where('id', '!=', $ujian_id);
            
        // Jika yang login guru, hanya tampilkan ujian miliknya
        if (auth()->user()->role == 'guru') {
            $guru_id = \App\Models\Guru::where('user_id', auth()->id())->value('id');
            $query_ujian_lain->where('guru_id', $guru_id);
        }
        
        $ujian_lain = $query_ujian_lain->orderBy('created_at', 'desc')->get();

        return view('admin.soal.index', compact('ujian', 'soals', 'ujian_lain'));
    }

    // Menampilkan halaman form tambah soal
    public function create($ujian_id)
    {
        $ujian = Ujian::findOrFail($ujian_id);
        
        // UBAH 'guru.soal.create' menjadi 'admin.soal.create'
        return view('admin.soal.create', compact('ujian'));
    }

    // 3. Menyimpan soal ke database
   public function store(Request $request, $ujian_id)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            'opsi_c' => 'required',
            'opsi_d' => 'required',
            'kunci_jawaban' => 'required|in:A,B,C,D,E',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except('gambar');

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('uploads/soal'), $nama_file);
            $data['gambar'] = $nama_file;
        }

        $data['ujian_id'] = $ujian_id;
        
        $soal = \App\Models\Soal::create($data);

        // 2. Hubungkan soal ke jadwal ujian melalui tabel pivot
        $ujian = \App\Models\Ujian::findOrFail($ujian_id);
        $ujian->soals()->attach($soal->id);

        return redirect()->route('admin.soal.index', $ujian_id)->with('success', 'Soal baru berhasil ditambahkan dan dihubungkan!');
    }

    // Menampilkan daftar soal berdasarkan ID Ujian
    
    // Menghapus soal
    // PERBAIKAN: Fungsi hapus untuk arsitektur Many-to-Many
    public function destroy($ujian_id, $soal_id)
    {
        $ujian = \App\Models\Ujian::findOrFail($ujian_id);
        
        // Memutuskan relasi soal dari ujian ini di tabel pivot (soal_ujian)
        $ujian->soals()->detach($soal_id);
        
        // Opsional: Jika Anda ingin menghapus fisik soal JIKA DAN HANYA JIKA 
        // soal tersebut sudah tidak terpakai sama sekali di ujian manapun.
        $soal = \App\Models\Soal::find($soal_id);
        if ($soal && $soal->ujian()->count() == 0) {
            // Hapus file gambar dari folder
            if ($soal->gambar && file_exists(public_path('uploads/soal/' . $soal->gambar))) {
                unlink(public_path('uploads/soal/' . $soal->gambar));
            }
            $soal->delete(); 
        }

        return redirect()->back()->with('success', 'Soal berhasil dilepas dari jadwal ujian ini!');
    }

    // Logika Salin Soal yang Baru (Jauh lebih ringan)
  public function importSoal(Request $request, $ujian_id)
    {
        $request->validate([
            'sumber_ujian_id' => 'required|exists:ujians,id'
        ]);

        $ujian_tujuan = \App\Models\Ujian::findOrFail($ujian_id);
        
        // 1. Coba ambil ID soal dari relasi Many-to-Many (Pivot)
        $id_soal_sumber = \App\Models\Ujian::findOrFail($request->sumber_ujian_id)
                            ->soals()
                            ->pluck('soals.id')
                            ->toArray();

        // 2. JIKA KOSONG (karena itu soal lama), ambil dari kolom ujian_id
        if (empty($id_soal_sumber)) {
            $id_soal_sumber = \App\Models\Soal::where('ujian_id', $request->sumber_ujian_id)
                                ->pluck('id')
                                ->toArray();
        }

        if (empty($id_soal_sumber)) {
            return back()->with('error', 'Ujian sumber yang dipilih tidak memiliki soal.');
        }

        // Hubungkan ke kelas yang baru
        $ujian_tujuan->soals()->syncWithoutDetaching($id_soal_sumber);

        return back()->with('success', 'Soal berhasil direferensikan dari kelas lain!');
    }

    public function importExcel(Request $request, $ujian_id)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            // Jalankan proses import
            Excel::import(new SoalImport($ujian_id), $request->file('file_excel'));
            
            return back()->with('success', 'Soal dari Excel berhasil diimpor!');
        } catch (\Exception $e) {
            // Menangkap error jika format Excel tidak sesuai
            return back()->with('error', 'Gagal mengimpor data! Pastikan judul kolom (Header) Excel sudah benar (pertanyaan, opsi_a, kunci_jawaban, dll). Detail: ' . $e->getMessage());
        }
    }

     public function edit($id)
    {
        $soal = \App\Models\Soal::findOrFail($id);
        
        // Pengecekan keamanan: jika yang login guru, pastikan ini soal miliknya
        if (auth()->user()->role == 'guru') {
            $guru_id = \App\Models\Guru::where('user_id', auth()->id())->value('id');
            $ujian = \App\Models\Ujian::find($soal->ujian_id);
            
            if ($ujian && $ujian->guru_id != $guru_id) {
                return back()->with('error', 'Anda tidak berhak mengedit soal ini.');
            }
        }

        return view('admin.soal.edit', compact('soal'));
    }

    public function update(Request $request, $id)
    {
        $soal = \App\Models\Soal::findOrFail($id);
        
        $request->validate([
            'pertanyaan' => 'required',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            'opsi_c' => 'required',
            'opsi_d' => 'required',
            'kunci_jawaban' => 'required|in:A,B,C,D,E',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->except('gambar');

        // Proses jika guru mengupload gambar baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama dari folder jika ada
            if ($soal->gambar && file_exists(public_path('uploads/soal/' . $soal->gambar))) {
                unlink(public_path('uploads/soal/' . $soal->gambar));
            }
            
            $file = $request->file('gambar');
            $nama_file = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('uploads/soal'), $nama_file);
            $data['gambar'] = $nama_file;
        }

        $soal->update($data);

        return redirect()->route('admin.soal.index', $soal->ujian_id)->with('success', 'Soal berhasil diperbarui!');
    }
}