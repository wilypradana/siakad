@extends('layouts.admin')

@section('title', 'Input Catatan Massal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><a href="{{ route('wali.dashboard') }}" class="text-decoration-none text-dark"><i class="fas fa-arrow-left me-2"></i></a> Input Catatan Massal</h2>
        <p class="text-muted mb-0">Kelas: <strong class="text-primary">{{ $kelas_wali->nama_kelas }}</strong></p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Form Filter Ujian -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
    <div class="card-body">
        <form action="{{ route('wali.input_catatan_massal') }}" method="GET" class="d-flex align-items-end gap-3">
            <div>
                <label class="fw-bold mb-1">Pilih Jenis Ujian / Semester:</label>
                <select name="jenis_ujian_id" class="form-select" style="min-width: 300px;" required>
                    <option value="">-- Pilih --</option>
                    @foreach($jenis_ujian_tersedia as $jenis)
                        <option value="{{ $jenis->id }}" {{ $jenis_ujian_id == $jenis->id ? 'selected' : '' }}>
                            {{ $jenis->nama_jenis }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Tampilkan Form</button>
        </form>
    </div>
</div>

<!-- Form Input Massal (Hanya Muncul Jika Ujian Sudah Dipilih) -->
@if($jenis_ujian_id)
<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <form action="{{ route('wali.simpan_catatan_massal') }}" method="POST">
        @csrf
        <input type="hidden" name="jenis_ujian_id" value="{{ $jenis_ujian_id }}">
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th width="5%" rowspan="2">No</th>
                            <th width="25%" rowspan="2">Nama Siswa</th>
                            <th width="30%" colspan="3">Ketidakhadiran (Hari)</th>
                            <th width="40%" rowspan="2">Catatan Wali Kelas</th>
                        </tr>
                        <tr>
                            <th width="10%">Sakit</th>
                            <th width="10%">Izin</th>
                            <th width="10%">Alfa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data_siswa as $index => $siswa)
                            @php
                                // Ambil data tersimpan (jika ada) untuk siswa ini
                                $catatan = $catatan_tersimpan[$siswa->id] ?? null;
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="fw-bold">
                                    {{ $siswa->nama }}
                                    <!-- WAJIB: Kirim array siswa_ids agar terbaca di controller -->
                                    <input type="hidden" name="siswa_ids[]" value="{{ $siswa->id }}">
                                </td>
                                <td>
                                    <input type="number" name="sakit[{{ $siswa->id }}]" class="form-control text-center" min="0" value="{{ $catatan->sakit ?? '' }}">
                                </td>
                                <td>
                                    <input type="number" name="izin[{{ $siswa->id }}]" class="form-control text-center" min="0" value="{{ $catatan->izin ?? '' }}">
                                </td>
                                <td>
                                    <input type="number" name="alfa[{{ $siswa->id }}]" class="form-control text-center" min="0" value="{{ $catatan->alfa ?? '' }}">
                                </td>
                                <td>
                                    <input type="text" name="catatan_wali_kelas[{{ $siswa->id }}]" class="form-control" value="{{ $catatan->catatan_wali_kelas ?? '' }}" placeholder="Ketik catatan...">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Belum ada siswa di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white text-end p-3" style="border-radius: 0 0 15px 15px;">
            <button type="submit" class="btn btn-success fw-bold px-4"><i class="fas fa-save me-2"></i> Simpan Semua Catatan</button>
        </div>
    </form>
</div>
@endif

@endsection