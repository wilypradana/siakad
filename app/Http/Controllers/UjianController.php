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
        $data = $request->validate([
            'judul_ujian' => 'required',
            'jenis_ujian_id' => 'required',
            'kelas_id' => 'required|array',
            'mapel_id' => 'required',
            'link_gform' => 'required|url',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'guru_id' => 'nullable'
        ]);

        $guru_id = $request->guru_id;
        if (auth()->user()->role == 'guru') {
            $guru_id = \App\Models\Guru::where('user_id', auth()->id())->value('id');
        }

        foreach ($request->kelas_id as $kelas) {
            \App\Models\Ujian::create([
                'judul_ujian' => $request->judul_ujian,
                'jenis_ujian_id' => $request->jenis_ujian_id,
                'kelas_id' => $kelas,
                'mapel_id' => $request->mapel_id,
                'link_gform' => $request->link_gform,
                'waktu_mulai' => $request->waktu_mulai,
                'waktu_selesai' => $request->waktu_selesai,
                'guru_id' => $guru_id
            ]);
        }

        return back()->with('success', 'Jadwal Ujian berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'judul_ujian' => 'required',
            'jenis_ujian_id' => 'required',
            'kelas_id' => 'required',
            'mapel_id' => 'required',
            'link_gform' => 'required|url',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'guru_id' => 'nullable'
        ]);

        $ujian = Ujian::findOrFail($id);
        
        if (auth()->user()->role == 'guru') {
            $guru_id = \App\Models\Guru::where('user_id', auth()->id())->value('id');
            if ($ujian->guru_id != $guru_id) {
                return back()->with('error', 'Anda tidak berhak mengedit ujian ini.');
            }
        }

        $ujian->update($data);
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

        DB::table('ujian_siswas')
            ->where('ujian_id', $ujian_id)
            ->where('siswa_id', $request->siswa_id)
            ->delete();

        return back()->with('success', 'Akses ujian siswa berhasil di-reset.');
    }

    public function bulkDelete(Request $request)
    {
        if ($request->ids) {
            \App\Models\Ujian::whereIn('id', $request->ids)->delete();
            return back()->with('success', count($request->ids) . ' Jadwal ujian berhasil dihapus sekaligus!');
        }
        return back()->with('error', 'Pilih minimal satu jadwal ujian untuk dihapus!');
    }

    // FITUR BARU: Ambil Kelas berdasarkan Mapel (AJAX)
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
}