@extends('layouts.admin')

@section('title', 'Portal Guru')

@section('content')

{{-- ============================================================
    PORTAL GURU — CLEAN CORPORATE DASHBOARD
============================================================ --}}

<div class="min-vh-100 pb-5">

    {{-- ========================================================
        WELCOME BANNER
    ========================================================= --}}
    <div class="mb-4 overflow-hidden rounded-4 border shadow-sm"
         style="
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-color: #bfdbfe !important;
            border-radius: 16px !important;
         ">

        <div class="position-relative p-4 p-md-5 text-dark">

            <div class="row align-items-center position-relative z-1">

                <div class="col-lg-8">

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="badge rounded-pill px-3 py-2 fw-semibold"
                              style="background: #dbeafe; color: #1e40af;">
                            <i class="fas fa-chalkboard-teacher me-1"></i>
                            PORTAL GURU
                        </span>

                        <span class="badge rounded-pill px-3 py-2 fw-semibold"
                              style="background: #d1fae5; color: #065f46;">
                            <span class="me-1">●</span>
                            Sistem Aktif
                        </span>
                    </div>

                    <h3 class="fw-bold mb-2 text-dark" style="letter-spacing: -0.5px;">
                        Selamat Datang,
                        {{ $guru->nama }} 👋
                    </h3>

                    <p class="mb-4 text-muted" style="font-size: 0.95rem;">
                        Kelola ujian, materi e-learning, absen, dan nilai Anda dengan mudah dalam satu ruang kerja terpadu.
                    </p>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="px-3 py-1.5 rounded-pill shadow-sm bg-white border"
                              style="font-size: 0.85rem; border-color: #cbd5e1 !important; color: #475569;">
                            <i class="fas fa-id-badge me-2 text-primary"></i>
                            NIP: {{ $guru->nip ?? '-' }}
                        </span>

                        <span class="px-3 py-1.5 rounded-pill shadow-sm bg-white border"
                              style="font-size: 0.85rem; border-color: #cbd5e1 !important; color: #475569;">
                            <i class="fas fa-layer-group me-2 text-primary"></i>
                            SIMUBUN Terintegrasi
                        </span>
                    </div>

                </div>

                <div class="col-lg-4 d-none d-lg-flex justify-content-end">
                    <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm border"
                        style="width:140px; height:140px; background: #ffffff; border-color: #bfdbfe !important; color: #2563eb;">
                        <i class="fas fa-chalkboard-teacher" style="font-size:3.5rem; opacity:0.85;"></i>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- ========================================================
        ALERT NOTIFIKASI
    ========================================================= --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-4 p-3" style="background-color: #d1e7dd; color: #0f5132;">
            <i class="fas fa-check-circle fs-5 me-3 text-success"></i>
            <div class="fw-medium small">{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center mb-4 p-3" style="background-color: #f8d7da; color: #842029;">
            <i class="fas fa-exclamation-circle fs-5 me-3 text-danger"></i>
            <div class="fw-medium small">{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ========================================================
        QUICK ACCESS (Modul Utama)
    ========================================================= --}}
    <div class="d-flex align-items-center justify-content-between mb-3 mt-2">
        <div>
            <div class="text-uppercase fw-bold mb-1" style="font-size:.7rem; letter-spacing:.1em; color: #2563eb;">
                Akses Cepat
            </div>
            <h5 class="fw-bold mb-0 text-dark">Modul Utama</h5>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- PORTAL UJIAN (WARNA BIRU KORPORAT) --}}
        <div class="col-xl-6">
            <a href="{{ route('ujian.index') }}" class="text-decoration-none">
                <div class="h-100 rounded-3 border bg-white shadow-sm p-3.5 position-relative overflow-hidden transition-all"
                    style="border-color: #086aeb !important;">
                    
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0 shadow-sm"
                            style="width:52px; height:52px; background: #f1f3f1; color: #2563eb;">
                            <i class="fas fa-laptop-code fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <div class="small fw-bold text-primary mb-0.5" style="font-size: 0.75rem; letter-spacing: 0.5px;">MODUL UJIAN & CBT</div>
                            <h6 class="fw-bold text-dark mb-1">Portal Ujian Siswa</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.8rem;">Kelola jadwal, bank soal, dan pengawasan ujian.</p>
                        </div>
                        <div class="ms-auto">
                            <div class="d-flex align-items-center justify-content-center rounded-circle border" style="width:32px; height:32px; background:#f8fafc; border-color: #e2e8f0 !important;">
                                <i class="fas fa-chevron-right text-muted small"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- PORTAL E-LEARNING (WARNA BIRU KORPORAT) --}}
        <div class="col-xl-6">
            <a href="{{ route('guru.elearning.index') }}" class="text-decoration-none">
                <div class="h-100 rounded-3 border bg-white shadow-sm p-3.5 position-relative overflow-hidden transition-all"
                    style="border-color: #086aeb !important;">
                    
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0 shadow-sm"
                            style="width:52px; height:52px; background: #eff6ff; color: #2563eb;">
                            <i class="fas fa-book-reader fs-5"></i>
                        </div>
                        <div class="ms-3">
                            <div class="small fw-bold text-primary mb-0.5" style="font-size: 0.75rem; letter-spacing: 0.5px;">MODUL BELAJAR DIGITAL</div>
                            <h6 class="fw-bold text-dark mb-1">Portal E-Learning</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.8rem;">Bagikan materi, tugas mandiri, dan rekap belajar.</p>
                        </div>
                        <div class="ms-auto">
                            <div class="d-flex align-items-center justify-content-center rounded-circle border" style="width:32px; height:32px; background:#f8fafc; border-color: #e2e8f0 !important;">
                                <i class="fas fa-chevron-right text-muted small"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ========================================================
        DAFTAR KELAS & PEMBELAJARAN
    ========================================================= --}}
    <div class="d-flex align-items-end justify-content-between mb-3">
        <div>
            <div class="text-uppercase fw-bold mb-1" style="font-size:.7rem; letter-spacing:.1em; color: #2563eb;">
                Pembelajaran
            </div>
            <h5 id="mapel-bawah" class="fw-bold mb-1 text-dark">Kelas & Mata Pelajaran</h5>
            <p class="text-muted small mb-0">Daftar kelas yang menjadi tanggung jawab mengajar Anda.</p>
        </div>
        <div class="d-none d-md-block">
            <span class="badge rounded-pill bg-white text-secondary border shadow-sm px-3 py-1.5 fw-semibold" style="border-color: #e2e8f0 !important;">
                <i class="fas fa-layer-group me-1 text-primary"></i> Total: {{ count($jadwal_mengajar) }} Kelas
            </span>
        </div>
    </div>

    {{-- TABLE KELAS & MAPEL --}}
    <div class="card border shadow-sm rounded-3 overflow-hidden mb-4" style="border-color: #e2e8f0 !important;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary" style="border-bottom: 2px solid #e2e8f0; font-size: 0.85rem;">
                    <tr>
                        <th class="border-0 text-center py-3" width="6%">#</th>
                        <th class="border-0 py-3" width="42%">Mata Pelajaran</th>
                        <th class="border-0 py-3" width="22%">Kelas Tujuan</th>
                        <th class="border-0 text-center py-3" width="30%">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwal_mengajar as $index => $jadwal)
                        <tr>
                            <td class="text-center py-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-secondary fw-bold border" style="width:28px;height:28px;font-size:.8rem; border-color: #cbd5e1 !important;">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center justify-content-center rounded-2 me-3 shadow-sm border" style="width:38px; height:38px; background:#f8fafc; color:#2563eb; border-color: #e2e8f0 !important;">
                                        <i class="fas fa-book small"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $jadwal->mapel->nama_mapel }}</div>
                                        <small class="text-muted" style="font-size: 0.75rem;"><i class="fas fa-tag me-1" style="font-size: 9px;"></i> Mapel Akademik</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="badge rounded-pill border px-2.5 py-1 fw-medium" style="background-color: #eff6ff; color: #1e40af; border-color: #bfdbfe !important;">
                                    <i class="fas fa-users me-1"></i> {{ $jadwal->kelas->nama_kelas }}
                                </span>
                            </td>
                            <td class="text-center py-3">
                                <div class="d-flex justify-content-center gap-1.5">
                                    <!-- Tombol Absensi -->
                                    <a href="{{ route('guru.absensi', ['kelas_id' => $jadwal->kelas_id, 'mapel_id' => $jadwal->mapel_id]) }}" class="btn btn-sm btn-outline-success px-2.5 py-1 fw-semibold d-flex align-items-center" style="font-size: 0.8rem;">
                                        <i class="fas fa-user-check me-1"></i> Absensi
                                    </a>
                                    <!-- Tombol Input Nilai -->
                                    <a href="{{ route('guru.input_nilai', ['kelas_id' => $jadwal->kelas_id, 'mapel_id' => $jadwal->mapel_id]) }}" class="btn btn-sm btn-primary px-2.5 py-1 fw-semibold d-flex align-items-center" style="font-size: 0.8rem;">
                                        <i class="fas fa-edit me-1"></i> Input Nilai
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-5 text-center">
                                <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle border shadow-sm" style="width:60px; height:60px; background:#f8fafc; color:#94a3b8; border-color: #e2e8f0 !important;">
                                    <i class="fas fa-calendar-times fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Belum Ada Jadwal Mengajar</h6>
                                <p class="text-muted small mb-0">Silakan hubungi Admin Kurikulum/Tata Usaha untuk mengatur jadwal.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========================================================
        FOOTER INFO
    ========================================================= --}}
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 rounded-3 border bg-white px-4 py-3 shadow-sm mb-3" style="border-color: #e2e8f0 !important;">
        <div class="d-flex align-items-center">
            <div class="d-flex align-items-center justify-content-center rounded-circle me-3 shadow-sm border" style="width:36px; height:36px; background:#f8fafc; color:#10b981; border-color: #e2e8f0 !important;">
                <i class="fas fa-shield-alt small"></i>
            </div>
            <div>
                <div class="fw-bold text-dark small">Portal Guru SIMUBUN</div>
                <div class="text-muted" style="font-size: 0.75rem;">Sistem pengelolaan pembelajaran & akademik berbasis Cloud.</div>
            </div>
        </div>
        <div class="text-muted small fw-medium bg-light rounded-2 px-3 py-1.5 border" style="border-color: #e2e8f0 !important; font-size: 0.75rem;">
            <i class="fas fa-lock text-success me-1"></i> Secured Connection
        </div>
    </div>

</div>

<style>
    .transition-all { transition: all 0.2s ease-in-out; }
    .transition-all:hover { transform: translateY(-2px); }
    .table-hover tbody tr:hover td { background-color: #f8fafc; }
</style>

@endsection