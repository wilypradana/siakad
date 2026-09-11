@extends('layouts.admin')

@section('title', 'Portal Guru')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-1 fw-bold">Selamat Datang, {{ $guru->nama }}! 👋</h3>
                    <p class="mb-0"><i class="fas fa-id-badge me-2"></i>NIP: {{ $guru->nip ?? '-' }} | Portal E-Rapor & Ujian</p>
                </div>
                <div class="d-none d-md-block opacity-50">
                    <i class="fas fa-chalkboard-teacher fa-4x"></i>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger"><i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}</div>
@endif

<h5 class="fw-bold text-secondary mb-3"><i class="fas fa-bolt text-warning me-2"></i> Menu Akses Cepat</h5>
<div class="row g-3">
    <div class="col-12">
        <a href="{{ route('ujian.index') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm" style="border-left: 5px solid #198754; border-radius: 10px;">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-laptop-code fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">Portal Ujian (G-Form / CBT)</h5>
                        <small class="text-muted">Buat jadwal, link Google Form, dan kelola ujian CBT Anda di sini.</small>
                    </div>
                    <i class="fas fa-chevron-right ms-auto text-muted"></i>
                </div>
            </div>
        </a>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-left: 5px solid #0d6efd; border-radius: 10px;">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                    <i class="fas fa-edit fa-lg"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-dark">Input Nilai E-Rapor</h5>
                    <small class="text-muted">Pilih kelas di bawah untuk menginput Nilai Formatif (Sumatif 1, 2, 3) dan Nilai Akhir (STS/SAS/SAT).</small>
                </div>
                <div class="ms-auto">
                    <i class="fas fa-check-double text-primary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<hr class="mb-4" style="opacity: 0.1;">

<h5 class="fw-bold text-secondary mb-3" id="mapel-bawah"><i class="fas fa-chalkboard me-2 text-primary"></i> Daftar Kelas & Mata Pelajaran Anda</h5>

<!-- PERUBAHAN MENJADI LIST TABEL -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 15px; overflow: hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary">
                    <tr>
                        <th width="5%" class="text-center py-3">No</th>
                        <th width="45%" class="py-3">Mata Pelajaran</th>
                        <th width="25%" class="py-3">Kelas</th>
                        <th width="25%" class="text-center py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwal_mengajar as $index => $jadwal)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">
                            <i class="fas fa-book-open text-primary me-2"></i> {{ $jadwal->mapel->nama_mapel }}
                        </td>
                        <td>
                            <span class="badge bg-secondary fs-6">{{ $jadwal->kelas->nama_kelas }}</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('guru.input_nilai', ['kelas_id' => $jadwal->kelas_id, 'mapel_id' => $jadwal->mapel_id]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-4 shadow-sm">
                                <i class="fas fa-edit me-1"></i> Buka Form Nilai
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-coffee fa-3x mb-3 text-warning d-block"></i>
                            <h5 class="text-dark">Anda belum memiliki jadwal mengajar.</h5>
                            <p class="mb-0">Silakan hubungi Admin Kurikulum/Tata Usaha untuk mengatur jadwal Anda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection