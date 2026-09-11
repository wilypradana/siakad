@extends('layouts.admin')

@section('title', 'Beranda')

@section('content')
<!-- Banner Profil Sekolah ala Dapodik -->
<div class="card bg-primary text-white shadow-sm border-0 mb-4" style="border-radius: 10px; background: linear-gradient(135deg, #0052D4, #4364F7, #6FB1FC);">
    <div class="card-body p-4 d-flex align-items-center">
        <div class="me-4 d-none d-md-block">
            <i class="fas fa-school fa-4x text-white-50"></i>
        </div>
        <div>
            <h3 class="fw-bold mb-1">SMA-SMK MULIA BUANA</h3>
            <p class="mb-0 fs-6"><i class="fas fa-map-marker-alt me-2"></i>Kec. Parung Panjang, Kab. Bogor</p>
            <div class="mt-2">
            </div>
        </div>
    </div>
</div>

<!-- Grid Rekapitulasi Data Utama -->
<h5 class="fw-bold text-secondary mb-3"><i class="fas fa-database me-2"></i>Rekapitulasi Data Utama</h5>
<div class="row g-3 mb-4">
    <!-- Box Guru -->
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 text-center py-3 rounded-3" style="background-color: #f8f9fa;">
            <div class="card-body p-2">
                <div class="text-primary mb-2"><i class="fas fa-chalkboard-teacher fa-2x"></i></div>
                <h3 class="fw-bold mb-0 text-dark">{{ $jumlah_guru }}</h3>
                <div class="text-muted small fw-bold text-uppercase">Guru & Tendik</div>
            </div>
        </div>
    </div>
    <!-- Box Siswa -->
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 text-center py-3 rounded-3" style="background-color: #f8f9fa;">
            <div class="card-body p-2">
                <div class="text-success mb-2"><i class="fas fa-users fa-2x"></i></div>
                <h3 class="fw-bold mb-0 text-dark">{{ $jumlah_siswa }}</h3>
                <div class="text-muted small fw-bold text-uppercase">Peserta Didik</div>
            </div>
        </div>
    </div>
    <!-- Box Kelas -->
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 text-center py-3 rounded-3" style="background-color: #f8f9fa;">
            <div class="card-body p-2">
                <div class="text-warning mb-2"><i class="fas fa-door-open fa-2x"></i></div>
                <h3 class="fw-bold mb-0 text-dark">{{ $jumlah_kelas }}</h3>
                <div class="text-muted small fw-bold text-uppercase">Rombongan Belajar</div>
            </div>
        </div>
    </div>
    <!-- Box Mapel -->
    <div class="col-md-3 col-6">
        <div class="card shadow-sm border-0 text-center py-3 rounded-3" style="background-color: #f8f9fa;">
            <div class="card-body p-2">
                <div class="text-info mb-2"><i class="fas fa-book fa-2x"></i></div>
                <h3 class="fw-bold mb-0 text-dark">{{ $jumlah_mapel }}</h3>
                <div class="text-muted small fw-bold text-uppercase">Mata Pelajaran</div>
            </div>
        </div>
    </div>
</div>


@endsection