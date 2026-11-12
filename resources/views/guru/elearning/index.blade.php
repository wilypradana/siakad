@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-3">
    
    {{-- KODE PENANGKAP NOTIFIKASI SUKSES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center justify-content-between p-3" role="alert" style="background-color: #d1e7dd; color: #0f5132; border-radius: 20px !important;">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                <div>
                    <strong class="fw-bold">Berhasil!</strong> 
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- HEADER BANNER MODERN (TEMA WARM PASTEL / SOFT PEACH-CREAM) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border-radius: 24px !important;">
                
                {{-- Dekorasi Bulatan Lembut di Background Banner --}}
                <div class="position-absolute rounded-circle" style="width: 150px; height: 150px; right: -40px; top: -50px; background: rgba(224, 122, 95, 0.08);"></div>

                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 position-relative">
                    <div>
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background: rgba(224, 122, 95, 0.12); color: #c05621; font-size: 0.78rem; font-weight: 700;">
                            <i class="fas fa-layer-group"></i> SIMUBUN E-LEARNING
                        </div>
                        <h3 class="fw-bold mb-1" style="color: #2d3142;">
                            Ruang Mengajar E-Learning
                        </h3>
                        <p class="mb-0 text-muted" style="font-size: 0.95rem;"><b>Kelola materi pembelajaran, penugasan, dan rekap progres seluruh kelas per mata pelajaran.</b></p>
                    </div>
                    <div class="badge bg-white px-3.5 py-2.5 rounded-pill shadow-sm fs-6 fw-bold" style="color: #e07a5f; border: 1px solid rgba(224, 122, 95, 0.2);">
                        <i class="fas fa-book-open me-1"></i> Modul Guru Mapel
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DAFTAR MATA PELAJARAN (1 KARTU PER MAPEL) --}}
    <div class="row g-4">
        @forelse($jadwal_mengajar as $mapel_id => $group)
            @php
                $mapel = $group->first()->mapel;
            @endphp
            <div class="col-xl-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden transition-all hover-shadow bg-white" style="border-radius: 22px !important;">
                    
                    {{-- Aksen Garis Atas Kartu (Warna Peach Khas SIMUBUN) --}}
                    <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background-color: #e07a5f;"></div>

                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            {{-- Badge Info Mapel --}}
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="badge fw-bold px-3 py-1.5 rounded-pill" style="background-color: #fdf2f8; color: #db2777; font-size: 0.75rem;">
                                    <i class="fas fa-book me-1"></i> Mata Pelajaran
                                </span>
                                <span class="text-muted small fw-medium">
                                    <i class="fas fa-users" style="color: #e07a5f;"></i> {{ $group->count() }} Kelas Diampu
                                </span>
                            </div>

                            {{-- Judul Mata Pelajaran --}}
                            <h6 class="fw-bold text-dark mb-3" style="letter-spacing: -0.3px;">{{ $mapel->nama_mapel ?? 'Mata Pelajaran' }}</h6>
                            
                            <hr class="text-muted opacity-10 my-3">

                            {{-- Daftar Kelas yang Diampu untuk Mapel Ini --}}
                            <p class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                                <i class="fas fa-school me-1" style="color: #e07a5f;"></i> Pilih Kelas Tujuan:
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                @foreach($group as $item)
                                    @if(isset($item->kelas))
                                        <div class="dropdown">
                                            <button class="btn btn-sm fw-medium px-3 py-1.5 rounded-3 border btn-kelas-hover" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #f8fafc; color: #475569; border-color: #e2e8f0 !important;">
                                                <i class="fas fa-graduation-cap me-1" style="color: #e07a5f;"></i> {{ $item->kelas->nama_kelas }}
                                            </button>
                                            <ul class="dropdown-menu shadow-sm border-0 py-2 rounded-4" style="box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;">
                                                <li><h6 class="dropdown-header text-dark fw-bold">{{ $item->kelas->nama_kelas }}</h6></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item py-1.5 small text-secondary" href="{{ route('guru.elearning.materi.list', [$item->kelas_id, $mapel_id]) }}"><i class="fas fa-folder-open text-warning me-2"></i> Lihat Materi & Tugas</a></li>
                                                <li><a class="dropdown-item py-1.5 small text-secondary" href="{{ route('guru.elearning.materi.rekap_kelas', [$item->kelas_id, $mapel_id]) }}"><i class="fas fa-chart-bar text-success me-2"></i> Rekap Progres Siswa</a></li>
                                            </ul>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- Tombol Aksi Utama per Mapel (Warna Tombol Peach Elegan) --}}
                        <div class="d-grid mt-auto">
                            <a href="{{ route('guru.elearning.materi.create', $mapel_id) }}" class="btn rounded-pill py-2.5 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 btn-buat-materi" style="background-color: #e07a5f; color: #ffffff; border: none; font-size: 0.9rem;">
                                <i class="fas fa-plus-circle"></i> Buat Materi & Tugas Baru
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 text-center py-5" style="border-radius: 22px !important;">
                <div class="card-body">
                    <div class="mb-3 opacity-50" style="color: #e07a5f;">
                        <i class="fas fa-folder-open fa-3x"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Belum Ada Jadwal Mengajar</h5>
                    <p class="text-muted mb-0">Anda belum memiliki penugasan jadwal mapel di SIMUBUN. Silakan hubungi Administrator.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

</div>

<style>
    .transition-all {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hover-shadow:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(224, 122, 95, 0.12)!important;
    }
    
    /* Efek hover untuk tombol dropdown kelas */
    .btn-kelas-hover {
        transition: all 0.2s ease;
    }
    .btn-kelas-hover:hover {
        border-color: #e07a5f !important;
        color: #e07a5f !important;
        background-color: #fff7ed !important;
    }

    /* Efek hover untuk tombol buat materi */
    .btn-buat-materi {
        transition: all 0.3s ease;
    }
    .btn-buat-materi:hover {
        background-color: #cc664a !important;
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(224, 122, 95, 0.35) !important;
    }
</style>
@endsection