@extends('layouts.admin')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Dashboard Wali Kelas</h2>
        <p class="text-muted mb-0">Kelas: <strong class="text-primary">{{ $kelas_wali->nama_kelas }} ({{ $kelas_wali->jurusan }})</strong></p>
    </div>
</div>
    <!-- Form Pratinjau Legger -->
    <div class="bg-white p-2 rounded shadow-sm border">
        <form action="{{ route('wali.lihat_legger') }}" method="GET" class="d-flex gap-2 mb-0">
            <select name="jenis_ujian_id" class="form-select form-select-sm border-primary" required>
                <option value="">-- Legger Nilai --</option>
                @foreach(App\Models\JenisUjian::all() as $jenis)
                    <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary text-nowrap fw-bold">
                <i class="fas fa-table me-1"></i> Lihat Legger
            </button>
        </form>
    </div>



<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-header bg-white pt-4 pb-0 border-0">
        <h5 class="fw-bold"><i class="fas fa-users text-primary me-2"></i> Daftar Siswa & Cetak Rapor</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Total Nilai Input</th>
                        <th width="280" class="text-center">Aksi Cetak</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data_siswa as $index => $siswa)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $siswa->nis }}</td>
                        <td>{{ $siswa->nama }}</td>
                        <td>
                            <span class="badge bg-info text-dark">
                                {{ $siswa->nilai ? $siswa->nilai->count() : 0 }} Mapel Selesai
                            </span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('wali.cetak_rapor') }}" method="POST" target="_blank" class="m-0">
                                @csrf
                                <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                
                                <div class="input-group input-group-sm">
                                    <select name="jenis_ujian_id" class="form-select" required>
                                        <option value="">Pilih Jenis Ujian...</option>
                                        @foreach(App\Models\JenisUjian::all() as $jenis)
                                            <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-success"><i class="fas fa-print"></i> Rapor</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada siswa di kelas ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection