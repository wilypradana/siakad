@extends('layouts.admin')

@section('title', 'Input Catatan Massal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <a href="{{ route('wali.dashboard') }}" class="text-decoration-none text-dark me-2"><i class="fas fa-arrow-left"></i></a> Input Catatan Massal
        </h4>
        <p class="text-muted small mb-0">Kelas: <strong class="text-primary">{{ $kelas_wali->nama_kelas }}</strong></p>
    </div>
</div>

<style>
    /* Merapikan ukuran font dan padding tabel agar proporsional dengan menu */
    .table-input-massal th, .table-input-massal td {
        padding: 0.6rem 0.75rem !important;
        font-size: 0.9rem !important;
        vertical-align: middle;
    }
    .table-input-massal input.form-control, 
    .table-input-massal textarea.form-control {
        font-size: 0.88rem !important;
        padding: 0.4rem 0.6rem !important;
        border-radius: 8px !important;
    }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Form Filter Ujian -->
<div class="card shadow-sm border mb-4" style="border-radius: 12px; border-color: #e2e8f0 !important;">
    <div class="card-body p-3.5">
        <form action="{{ route('wali.input_catatan_massal') }}" method="GET" class="d-flex align-items-end gap-3 flex-wrap">
            <div>
                <label class="fw-bold mb-1 text-dark small">Pilih Jenis Ujian / Semester:</label>
                <select name="jenis_ujian_id" class="form-select form-select-sm" style="min-width: 280px;" required>
                    <option value="">-- Pilih --</option>
                    @foreach($jenis_ujian_tersedia as $jenis)
                        <option value="{{ $jenis->id }}" {{ $jenis_ujian_id == $jenis->id ? 'selected' : '' }}>
                            {{ $jenis->nama_jenis }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-sm btn-primary fw-semibold"><i class="fas fa-search me-1"></i> Tampilkan Form</button>
        </form>
    </div>
</div>

<!-- Form Input Massal (Hanya Muncul Jika Ujian Sudah Dipilih) -->
@if($jenis_ujian_id)
<div class="card shadow-sm border" style="border-radius: 12px; border-color: #e2e8f0 !important;">
    <form action="{{ route('wali.simpan_catatan_massal') }}" method="POST">
        @csrf
        <input type="hidden" name="jenis_ujian_id" value="{{ $jenis_ujian_id }}">
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <!-- TAMBAHKAN KELAS table-input-massal DI SINI -->
                <table class="table table-hover table-bordered table-input-massal align-middle mb-0">
                    <thead class="table-light text-center align-middle text-secondary" style="font-size: 0.85rem;">
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
                                $catatan = $catatan_tersimpan[$siswa->id] ?? null;
                            @endphp
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">
                                    {{ $siswa->nama }}
                                    <input type="hidden" name="siswa_ids[]" value="{{ $siswa->id }}">
                                </td>
                                <td>
                                    <input type="number" name="sakit[{{ $siswa->id }}]" class="form-control text-center shadow-none" min="0" value="{{ $catatan->sakit ?? '' }}">
                                </td>
                                <td>
                                    <input type="number" name="izin[{{ $siswa->id }}]" class="form-control text-center shadow-none" min="0" value="{{ $catatan->izin ?? '' }}">
                                </td>
                                <td>
                                    <input type="number" name="alfa[{{ $siswa->id }}]" class="form-control text-center shadow-none" min="0" value="{{ $catatan->alfa ?? '' }}">
                                </td>
                                <td>
                                    <input type="text" name="catatan_wali_kelas[{{ $siswa->id }}]" class="form-control shadow-none" value="{{ $catatan->catatan_wali_kelas ?? '' }}" placeholder="Ketik catatan...">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted small">Belum ada siswa di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white text-end p-3 border-top" style="border-radius: 0 0 12px 12px;">
            <button type="submit" class="btn btn-sm btn-success fw-semibold px-4"><i class="fas fa-save me-2"></i> Simpan Semua Catatan</button>
        </div>
    </form>
</div>
@endif

@endsection