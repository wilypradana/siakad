@extends('layouts.admin')

@section('title', 'Absensi Siswa')

@section('content')
<div class="mb-4">
    <a href="{{ route('guru.dashboard') }}" class="btn bg-white border shadow-sm rounded-pill px-4 py-1.5 fw-semibold small text-muted mb-3 transition-all" style="border-color: #e2e8f0 !important;">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
    </a>
    <h3 class="fw-bold mb-1 text-dark">
        <i class="fas fa-user-check me-2 text-primary"></i> Absensi Siswa: {{ $mapel->nama_mapel }}
    </h3>
    <p class="text-muted small mb-0">Kelas: <b class="text-dark">{{ $kelas->nama_kelas }}</b></p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 p-3" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border shadow-sm rounded-3 overflow-hidden" style="border-color: #e2e8f0 !important;">
    <div class="card-header bg-white pt-4 pb-3 border-bottom px-4">
        <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-center">
            <div class="col-auto">
                <label class="fw-bold small text-dark">Pilih Tanggal Absen:</label>
            </div>
            <div class="col-md-3">
                <input type="date" name="tanggal" class="form-control form-control-sm border shadow-sm" value="{{ $tanggal }}" onchange="this.form.submit()" style="border-radius: 8px; border-color: #cbd5e1 !important;">
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <form action="{{ route('guru.simpan_absensi', ['kelas_id' => $kelas->id, 'mapel_id' => $mapel->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-secondary" style="border-bottom: 2px solid #e2e8f0; font-size: 0.85rem;">
                        <tr>
                            <th width="5%" class="py-3 text-center">No</th>
                            <th width="30%" class="py-3 text-start">Nama Siswa</th>
                            <th width="45%" class="py-3 text-center">Status Kehadiran (Tanggal: {{ $tanggal }})</th>
                            <th width="20%" class="py-3 text-center">Akumulasi Rekap (H / S / I / A)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data_siswa as $index => $siswa)
                        @php 
                            $absen = $absensi_existing->get($siswa->id);
                            $status_pilih = $absen->status ?? 'H';

                            $rekap_h = \App\Models\AbsensiSiswa::where('siswa_id', $siswa->id)->where('mapel_id', $mapel->id)->where('status', 'H')->count();
                            $rekap_s = \App\Models\AbsensiSiswa::where('siswa_id', $siswa->id)->where('mapel_id', $mapel->id)->where('status', 'S')->count();
                            $rekap_i = \App\Models\AbsensiSiswa::where('siswa_id', $siswa->id)->where('mapel_id', $mapel->id)->where('status', 'I')->count();
                            $rekap_a = \App\Models\AbsensiSiswa::where('siswa_id', $siswa->id)->where('mapel_id', $mapel->id)->where('status', 'A')->count();
                        @endphp
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $siswa->nama }}</td>
                            <td class="text-center py-3">
                                <div class="btn-group shadow-none rounded-pill overflow-hidden border" role="group" style="border-color: #cbd5e1 !important;">
                                    <input type="radio" class="btn-check" name="status[{{ $siswa->id }}]" value="H" id="hadir_{{ $siswa->id }}" {{ $status_pilih == 'H' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success btn-sm px-3 border-0" for="hadir_{{ $siswa->id }}">Hadir</label>

                                    <input type="radio" class="btn-check" name="status[{{ $siswa->id }}]" value="S" id="sakit_{{ $siswa->id }}" {{ $status_pilih == 'S' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-warning btn-sm px-3 border-0" for="sakit_{{ $siswa->id }}">Sakit</label>

                                    <input type="radio" class="btn-check" name="status[{{ $siswa->id }}]" value="I" id="izin_{{ $siswa->id }}" {{ $status_pilih == 'I' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info btn-sm px-3 border-0" for="izin_{{ $siswa->id }}">Izin</label>

                                    <input type="radio" class="btn-check" name="status[{{ $siswa->id }}]" value="A" id="alfa_{{ $siswa->id }}" {{ $status_pilih == 'A' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger btn-sm px-3 border-0" for="alfa_{{ $siswa->id }}">Alfa</label>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1">H: {{ $rekap_h }}</span>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2 py-1">S: {{ $rekap_s }}</span>
                                <span class="badge bg-info bg-opacity-10 text-info border border-info px-2 py-1">I: {{ $rekap_i }}</span>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-2 py-1">A: {{ $rekap_a }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-top p-4 text-end">
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2.5 fw-semibold shadow-sm">
                    <i class="fas fa-save me-2"></i> Simpan Absensi
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .table-hover tbody tr:hover td { background-color: #f8fafc; }
</style>
@endsection