@extends('layouts.admin')

@section('title', 'Legger Nilai Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ url('/wali-kelas/dashboard') }}" class="btn btn-sm btn-outline-secondary mb-2"><i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard</a>
        <h2>Legger Nilai: {{ $kelas_wali->nama_kelas }}</h2>
        <p class="text-muted mb-0">Ujian: <strong class="text-primary">{{ $jenis_ujian->nama_jenis }}</strong></p>
    </div>
    
    <form action="{{ route('wali.export_rekap') }}" method="POST">
        @csrf
        <input type="hidden" name="jenis_ujian_id" value="{{ $jenis_ujian->id }}">
        <button type="submit" class="btn btn-success shadow-sm fw-bold">
            <i class="fas fa-download me-1"></i> Download Legger (CSV)
        </button>
    </form>
</div>

@php
    // 1. Ambil ID semua siswa di kelas ini
    $siswa_ids = $data_siswa->pluck('id')->toArray();

    // 2. Ambil semua nilai yang dimiliki oleh siswa di kelas ini
    $semua_nilai = \App\Models\Nilai::whereIn('siswa_id', $siswa_ids)
                                    ->where('jenis_ujian_id', $jenis_ujian->id)
                                    ->get();

    // 3. CARI MAPEL KHUSUS KELAS INI (Berdasarkan Set Pembelajaran)
    $mapel_ids_kelas = \Illuminate\Support\Facades\DB::table('pembelajarans')
                            ->where('kelas_id', $kelas_wali->id)
                            ->pluck('mapel_id')
                            ->toArray();

    // Fallback: Jika Set Pembelajaran belum dibuat admin, cari lewat Jadwal Ujian
    if (empty($mapel_ids_kelas)) {
        $mapel_ids_kelas = \App\Models\Ujian::where('kelas_id', $kelas_wali->id)
                                            ->pluck('mapel_id')
                                            ->toArray();
    }

    // 4. Tarik data mapel yang HANYA terdaftar untuk kelas ini (walaupun belum dinilai)
    $semua_mapel = \App\Models\Mapel::whereIn('id', $mapel_ids_kelas)
                                    ->orderBy('kelompok', 'asc')
                                    ->orderBy('nama_mapel', 'asc')
                                    ->get();
@endphp

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.85rem; white-space: nowrap;">
                <thead class="table-dark text-center">
                    <tr>
                        <th width="5%" class="align-middle">No</th>
                        <th width="10%" class="align-middle">NIS</th>
                        <th width="20%" class="align-middle">Nama Siswa</th>
                        
                        @foreach($semua_mapel as $mapel)
                            <th class="align-middle th-vertical">
                                {{ $mapel->nama_mapel }}
                            </th>
                        @endforeach
                        
                        <th class="align-middle text-warning">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data_siswa as $index => $siswa)
                        @php
                            $total_nilai = 0;
                            $jumlah_mapel = 0;
                            $nilai_siswa_ini = $semua_nilai->where('siswa_id', $siswa->id);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-center fw-bold">{{ $siswa->nis }}</td>
                            <td>{{ $siswa->nama }}</td>
                            
                            @foreach($semua_mapel as $mapel)
                                @php
                                    $data_n = $nilai_siswa_ini->firstWhere('mapel_id', $mapel->id);
                                    $nilai_akhir = $data_n ? $data_n->nilai_akhir : null;
                                @endphp
                                
                                <td class="text-center {{ $nilai_akhir === null ? 'bg-danger text-white fw-bold' : 'fw-bold' }}">
                                    @if($nilai_akhir !== null)
                                        @php
                                            $total_nilai += (float)$nilai_akhir;
                                            $jumlah_mapel++;
                                        @endphp
                                        {{ $nilai_akhir }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endforeach
                            
                            <td class="text-center bg-light fw-bold text-primary">
                                {{ $jumlah_mapel > 0 ? round($total_nilai / $jumlah_mapel, 2) : 0 }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($semua_mapel) + 4 }}" class="text-center py-4 text-muted">Belum ada data siswa di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .table-responsive { max-height: 70vh; overflow-y: auto; overflow-x: auto; }
    thead th { position: sticky; top: 0; z-index: 1; }
    
    .th-vertical {
        writing-mode: vertical-rl; 
        transform: rotate(180deg); 
        padding: 10px 4px !important;
        height: 150px; 
        max-height: 150px;
        font-size: 0.75rem; 
        white-space: nowrap;
        vertical-align: middle;
    }
</style>
@endsection