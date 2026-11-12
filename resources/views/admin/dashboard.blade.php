@extends('layouts.admin')

@section('title', 'Beranda')

@section('content')
<!-- Banner Profil Sekolah - Fresh & Energetic Blue Gradient -->
<div class="card text-white border-0 shadow-sm mb-4 position-relative overflow-hidden" 
     style="border-radius: 16px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);">
    
    {{-- Aksen Lingkaran Visual Transparan --}}
    <div class="position-absolute rounded-circle" 
         style="width: 220px; height: 220px; right: -50px; top: -60px; background: rgba(255, 255, 255, 0.1);"></div>
    <div class="position-absolute rounded-circle" 
         style="width: 140px; height: 140px; right: 120px; bottom: -50px; background: rgba(255, 255, 255, 0.08);"></div>

    <div class="card-body p-4 p-md-5 d-flex align-items-center position-relative z-1">
        <div class="me-4 d-none d-md-flex align-items-center justify-content-center rounded-circle p-3" 
             style="background: rgba(255, 255, 255, 0.18); backdrop-filter: blur(5px); width: 85px; height: 85px;">
            <i class="fas fa-school fa-3x text-white"></i>
        </div>
        <div>
            <span class="badge rounded-pill bg-white text-primary fw-bold px-3 py-1.5 mb-2 shadow-sm" style="font-size: 0.75rem;">
                <i class="fas fa-check-circle text-primary me-1"></i> PORTAL AKADEMIK TERINTEGRASI
            </span>
            <h2 class="fw-bold mb-1 text-white" style="letter-spacing: -0.5px;">SMA-SMK MULIA BUANA</h2>
            <p class="mb-0 text-white-75 small">
                <i class="fas fa-map-marker-alt me-1 text-warning"></i> Kec. Parung Panjang, Kab. Bogor
            </p>
        </div>
    </div>
</div>

<!-- Section Header -->
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <div class="text-uppercase fw-bold mb-0.5" style="font-size: 0.7rem; letter-spacing: 0.1em; color: #2563eb;">
            Ringkasan Sistem
        </div>
        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Rekapitulasi Data Utama</h5>
    </div>
</div>

<!-- Grid Rekapitulasi Data Utama (Sangat Berwarna & Fresh) -->
<div class="row g-3 mb-4">
    
    <!-- Box Guru & Tendik (Aksen Biru Indigo Fresh) -->
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm h-100 transition-all hover-up" 
             style="border-radius: 14px; background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%); border-left: 4px solid #2563eb !important;">
            <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase fw-bold text-muted mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Guru & Tendik</div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $jumlah_guru }}</h2>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" 
                     style="width: 48px; height: 48px; background: #dbeafe; color: #1d4ed8;">
                    <i class="fas fa-chalkboard-teacher fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Box Peserta Didik (Aksen Hijau Emerald Fresh) -->
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm h-100 transition-all hover-up" 
             style="border-radius: 14px; background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border-left: 4px solid #10b981 !important;">
            <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase fw-bold text-muted mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Peserta Didik</div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $jumlah_siswa }}</h2>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" 
                     style="width: 48px; height: 48px; background: #d1fae5; color: #047857;">
                    <i class="fas fa-user-graduate fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Box Rombongan Belajar (Aksen Amber / Kuning Emas Fresh) -->
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm h-100 transition-all hover-up" 
             style="border-radius: 14px; background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%); border-left: 4px solid #f59e0b !important;">
            <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase fw-bold text-muted mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Rombel / Kelas</div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $jumlah_kelas }}</h2>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" 
                     style="width: 48px; height: 48px; background: #fef3c7; color: #b45309;">
                    <i class="fas fa-door-open fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Box Mata Pelajaran (Aksen Ungu Violet / Cyan Fresh) -->
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm h-100 transition-all hover-up" 
             style="border-radius: 14px; background: linear-gradient(135deg, #ffffff 0%, #faf5ff 100%); border-left: 4px solid #8b5cf6 !important;">
            <div class="card-body p-3.5 d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-uppercase fw-bold text-muted mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Mata Pelajaran</div>
                    <h2 class="fw-bold mb-0 text-dark">{{ $jumlah_mapel }}</h2>
                </div>
                <div class="d-flex align-items-center justify-content-center rounded-3 shadow-sm" 
                     style="width: 48px; height: 48px; background: #ede9fe; color: #6d28d9;">
                    <i class="fas fa-book-open fs-4"></i>
                </div>
            </div>
        </div>
    </div>

</div>

<style>
    .transition-all { transition: all 0.2s ease-in-out; }
    .hover-up:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important; }
    .text-white-75 { color: rgba(255, 255, 255, 0.85); }
</style>
@endsection