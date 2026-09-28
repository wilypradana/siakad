@extends('layouts.admin')

@section('title', 'Portal Guru')

@section('content')

{{-- ============================================================
    PORTAL GURU — MODERN DASHBOARD
============================================================ --}}

<div class="min-vh-100">

    {{-- ========================================================
        WELCOME HEADER
    ========================================================= --}}
    <div class="mb-4 overflow-hidden rounded-4 border-0 shadow-sm"
         style="
            background:
                radial-gradient(circle at 85% 20%, rgba(255,255,255,.16), transparent 25%),
                radial-gradient(circle at 70% 100%, rgba(99,102,241,.35), transparent 30%),
                linear-gradient(135deg,#1d4ed8 0%,#4338ca 48%,#6d28d9 100%);
         ">

        <div class="position-relative p-4 p-md-5 text-white">

            {{-- Decorative --}}
            <div
                class="position-absolute rounded-circle"
                style="
                    width:180px;
                    height:180px;
                    right:-60px;
                    top:-80px;
                    background:rgba(255,255,255,.06);
                ">
            </div>

            <div
                class="position-absolute rounded-circle"
                style="
                    width:120px;
                    height:120px;
                    right:160px;
                    bottom:-80px;
                    background:rgba(255,255,255,.05);
                ">
            </div>

            <div class="row align-items-center position-relative">

                <div class="col-lg-8">

                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span
                            class="badge rounded-pill px-3 py-2"
                            style="background:rgba(255,255,255,.14); color:#fff;"
                        >
                            <i class="fas fa-shield-alt me-1"></i>
                            PORTAL GURU
                        </span>

                        <span
                            class="badge rounded-pill px-3 py-2"
                            style="background:rgba(16,185,129,.18); color:#d1fae5;"
                        >
                            <span class="me-1">●</span>
                            Sistem Aktif
                        </span>
                    </div>

                    <h1 class="fw-bold mb-2" style="font-size:clamp(1.7rem,3vw,2.5rem);">
                        Selamat Datang,
                        {{ $guru->nama }} 👋
                    </h1>

                    <p class="mb-3 text-white-50">
                        Kelola ujian, soal, nilai, dan aktivitas pembelajaran
                        Anda dari satu tempat.
                    </p>

                    <div class="d-flex flex-wrap gap-2">

                        <span class="px-3 py-2 rounded-3"
                              style="background:rgba(255,255,255,.10);">
                            <i class="fas fa-id-badge me-2"></i>
                            NIP: {{ $guru->nip ?? '-' }}
                        </span>

                        <span class="px-3 py-2 rounded-3"
                              style="background:rgba(255,255,255,.10);">
                            <i class="fas fa-layer-group me-2"></i>
                            E-Rapor & Ujian
                        </span>

                    </div>

                </div>


                <div class="col-lg-4 d-none d-lg-flex justify-content-end">

                    <div
                        class="d-flex align-items-center justify-content-center rounded-4"
                        style="
                            width:150px;
                            height:150px;
                            background:rgba(255,255,255,.10);
                            border:1px solid rgba(255,255,255,.12);
                            backdrop-filter:blur(10px);
                        "
                    >
                        <i class="fas fa-chalkboard-teacher"
                           style="font-size:4.5rem; opacity:.75;"></i>
                    </div>

                </div>

            </div>
        </div>
    </div>


    {{-- ========================================================
        ALERT
    ========================================================= --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center">
            <i class="fas fa-check-circle fs-5 me-3"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 d-flex align-items-center">
            <i class="fas fa-exclamation-circle fs-5 me-3"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif


    {{-- ========================================================
        QUICK ACCESS
    ========================================================= --}}
    <div class="d-flex align-items-center justify-content-between mb-3">

        <div>
            <div class="text-uppercase fw-bold text-primary"
                 style="font-size:.7rem; letter-spacing:.12em;">
                Akses Cepat
            </div>

            <h5 class="fw-bold mb-0 text-dark">
                Menu Utama
            </h5>
        </div>

    </div>


    <div class="row g-3 mb-4">

        {{-- PORTAL UJIAN --}}
        <div class="">

            <a
                href="{{ route('ujian.index') }}"
                class="text-decoration-none"
            >

                <div
                    class="h-100 rounded-4 border bg-white shadow-sm p-4 position-relative overflow-hidden"
                    style="transition:.2s;"
                    onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 30px rgba(15,23,42,.10)'"
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow=''"
                >

                    <div
                        class="position-absolute"
                        style="
                            width:120px;
                            height:120px;
                            right:-45px;
                            top:-45px;
                            background:#ecfdf5;
                            border-radius:50%;
                        "
                    ></div>

                    <div class="d-flex align-items-center position-relative">

                        <div
                            class="d-flex align-items-center justify-content-center rounded-4 flex-shrink-0"
                            style="
                                width:58px;
                                height:58px;
                                background:#ecfdf5;
                                color:#059669;
                            "
                        >
                            <i class="fas fa-laptop-code fs-4"></i>
                        </div>

                        <div class="ms-3">

                            <div class="small fw-bold text-success mb-1">
                                PORTAL UJIAN
                            </div>

                            <h5 class="fw-bold text-dark mb-1">
                                Ujian G-Form / CBT
                            </h5>

                            <p class="text-muted small mb-0">
                                Kelola jadwal, soal, kelas, dan aktivitas ujian.
                            </p>

                        </div>

                        <div class="ms-auto">
                            <div
                                class="d-flex align-items-center justify-content-center rounded-circle"
                                style="
                                    width:38px;
                                    height:38px;
                                    background:#f8fafc;
                                "
                            
                                <i class="fas fa-arrow-right text-muted"></i>
                            </div>
                        </div>

                    </div>

                </div>

            </a>

        </div>


        {{-- INPUT NILAI --}}
        <div class="col-xl-6">


        </div>

    </div>


    {{-- ========================================================
        FEATURE UPDATE BANNER
    ========================================================= --}}
    <div
        class="rounded-4 overflow-hidden mb-4 shadow-sm"
        style="
            background:
                radial-gradient(circle at 90% 20%, rgba(255,255,255,.12), transparent 20%),
                linear-gradient(135deg,#111827,#312e81 55%,#4c1d95);
        "
    >

        <div class="p-4 p-lg-5 text-white">

            <div class="row align-items-center">

                <div class="col-lg-8">

                    <div
                        class="d-inline-flex align-items-center gap-2 rounded-pill px-3 py-2 mb-3"
                        style="background:rgba(255,255,255,.10);"
                    >
                        <span class="text-warning">
                            <i class="fas fa-sparkles"></i>
                        </span>

                        <span class="small fw-bold">
                            UPDATE TERBARU
                        </span>
                    </div>

                    <h3 class="fw-bold mb-2">
                        Pengelolaan Ujian Kini Lebih Cerdas 🚀
                    </h3>

                    <p class="text-white-50 mb-4" style="max-width:650px;">
                        Berbagai fitur baru telah hadir untuk membantu
                        Bapak/Ibu mengelola soal, timer, nilai, dan rekap
                        dengan lebih cepat dan praktis.
                    </p>

                    <div class="d-flex flex-wrap gap-2">

                        <span class="badge rounded-pill px-3 py-2"
                              style="background:rgba(255,255,255,.10);">
                            <i class="fas fa-database me-1"></i>
                            Bank Soal
                        </span>

                        <span class="badge rounded-pill px-3 py-2"
                              style="background:rgba(255,255,255,.10);">
                            <i class="fas fa-file-excel me-1"></i>
                            Import Excel
                        </span>

                        <span class="badge rounded-pill px-3 py-2"
                              style="background:rgba(255,255,255,.10);">
                            <i class="fas fa-stopwatch me-1"></i>
                            Smart Timer
                        </span>

                        <span class="badge rounded-pill px-3 py-2"
                              style="background:rgba(255,255,255,.10);">
                            <i class="fas fa-print me-1"></i>
                            Cetak A4
                        </span>

                    </div>

                </div>


                <div class="col-lg-4 d-none d-lg-flex justify-content-end">

                    <div
                        class="text-center rounded-4 p-4"
                        style="
                            width:190px;
                            background:rgba(255,255,255,.07);
                            border:1px solid rgba(255,255,255,.10);
                        "
                    >
                        <i class="fas fa-rocket mb-3"
                           style="font-size:3rem;"></i>

                        <div class="fw-bold">
                            7 Fitur Baru
                        </div>

                        <small class="text-white-50">
                            Siap digunakan
                        </small>
                    </div>

                </div>

            </div>

        </div>
    </div>


    {{-- ========================================================
        DAFTAR KELAS
    ========================================================= --}}
    <div class="d-flex align-items-end justify-content-between mb-3">

        <div>

            <div class="text-uppercase fw-bold text-primary"
                 style="font-size:.7rem; letter-spacing:.12em;">
                Pembelajaran
            </div>

            <h5
                id="mapel-bawah"
                class="fw-bold mb-1 text-dark"
            >
                Kelas & Mata Pelajaran
            </h5>

            <p class="text-muted small mb-0">
                Daftar kelas yang menjadi tanggung jawab Anda.
            </p>

        </div>

        <div class="d-none d-md-block">
            <span class="badge rounded-pill bg-light text-secondary border px-3 py-2">
                <i class="fas fa-layer-group me-1"></i>
                {{ count($jadwal_mengajar) }} Kelas
            </span>
        </div>

    </div>


    {{-- TABLE --}}
    <div
        class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4"
    >

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead style="background:#f8fafc;">

                    <tr>

                        <th
                            class="border-0 text-center text-secondary small fw-bold py-3"
                            width="6%"
                        >
                            #
                        </th>

                        <th
                            class="border-0 text-secondary small fw-bold py-3"
                            width="42%"
                        >
                            MATA PELAJARAN
                        </th>

                        <th
                            class="border-0 text-secondary small fw-bold py-3"
                            width="22%"
                        >
                            KELAS
                        </th>

                        <th
                            class="border-0 text-center text-secondary small fw-bold py-3"
                            width="30%"
                        >
                            AKSI
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($jadwal_mengajar as $index => $jadwal)

                        <tr>

                            <td class="text-center">

                                <span
                                    class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-secondary fw-bold"
                                    style="width:32px;height:32px;font-size:.8rem;"
                                >
                                    {{ $index + 1 }}
                                </span>

                            </td>


                            <td>

                                <div class="d-flex align-items-center">

                                    <div
                                        class="d-flex align-items-center justify-content-center rounded-3 me-3"
                                        style="
                                            width:42px;
                                            height:42px;
                                            background:#eff6ff;
                                            color:#2563eb;
                                        "
                                    >
                                        <i class="fas fa-book-open"></i>
                                    </div>

                                    <div>

                                        <div class="fw-bold text-dark">
                                            {{ $jadwal->mapel->nama_mapel }}
                                        </div>

                                        <small class="text-muted">
                                            Mata Pelajaran
                                        </small>

                                    </div>

                                </div>

                            </td>


                            <td>

                                <span
                                    class="badge rounded-pill bg-light text-dark border px-3 py-2"
                                >
                                    <i class="fas fa-users me-1 text-primary"></i>
                                    {{ $jadwal->kelas->nama_kelas }}
                                </span>

                            </td>


                            <td class="text-center">

                                <a
                                    href="{{ route('guru.input_nilai', [
                                        'kelas_id' => $jadwal->kelas_id,
                                        'mapel_id' => $jadwal->mapel_id
                                    ]) }}"
                                    class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm"
                                >
                                    <i class="fas fa-edit me-1"></i>
                                    Input Nilai
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="py-5 text-center">

                                <div
                                    class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                                    style="
                                        width:70px;
                                        height:70px;
                                        background:#fff7ed;
                                        color:#f59e0b;
                                    "
                                >
                                    <i class="fas fa-calendar-times fs-3"></i>
                                </div>

                                <h5 class="fw-bold text-dark">
                                    Belum Ada Jadwal Mengajar
                                </h5>

                                <p class="text-muted small mb-0">
                                    Silakan hubungi Admin Kurikulum/Tata Usaha
                                    untuk mengatur jadwal mengajar Anda.
                                </p>

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
    <div
        class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-2 rounded-4 border bg-white px-4 py-3 shadow-sm mb-4"
    >

        <div class="d-flex align-items-center">

            <div
                class="d-flex align-items-center justify-content-center rounded-circle me-3"
                style="
                    width:36px;
                    height:36px;
                    background:#ecfdf5;
                    color:#059669;
                "
            >
                <i class="fas fa-shield-check"></i>
            </div>

            <div>

                <div class="small fw-bold text-dark">
                    Portal Guru
                </div>

                <div class="text-muted" style="font-size:.75rem;">
                    Sistem pengelolaan pembelajaran terintegrasi.
                </div>

            </div>

        </div>

        <div class="text-muted" style="font-size:.75rem;">
            <i class="fas fa-lock me-1"></i>
            Sistem aman & terkelola
        </div>

    </div>

</div>
{{-- ========================================================
    MODAL FITUR BARU
========================================================= --}}
<div
    id="featureModal"
    class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-3"
    style="
        z-index:99999;
        background:rgba(15,23,42,.70);
        backdrop-filter:blur(7px);
        opacity:0;
        visibility:hidden;
        transition:all .25s ease;
    "
>

    <div
        id="featureModalBox"
        class="bg-white rounded-4 shadow-lg overflow-hidden w-100"
        style="
            max-width:900px;
            max-height:90vh;
            transform:translateY(25px) scale(.97);
            transition:all .3s ease;
        "
    >

        {{-- ==================================================
            HEADER
        ================================================== --}}
        <div
            class="position-relative overflow-hidden text-white p-4 p-md-5"
            style="
                background:
                    radial-gradient(circle at 90% 10%,rgba(255,255,255,.18),transparent 22%),
                    radial-gradient(circle at 10% 100%,rgba(99,102,241,.25),transparent 30%),
                    linear-gradient(135deg,#1e3a8a,#4338ca 50%,#7e22ce);
            "
        >

            {{-- Dekorasi --}}
            <div
                class="position-absolute rounded-circle"
                style="
                    width:180px;
                    height:180px;
                    right:-70px;
                    top:-90px;
                    background:rgba(255,255,255,.07);
                "
            ></div>

            <div
                class="position-absolute rounded-circle"
                style="
                    width:120px;
                    height:120px;
                    left:35%;
                    bottom:-80px;
                    background:rgba(255,255,255,.05);
                "
            ></div>


            {{-- CLOSE --}}
            <button
                type="button"
                onclick="closeFeatureModal()"
                aria-label="Tutup"
                class="position-absolute top-0 end-0 m-3 d-flex align-items-center justify-content-center border-0 rounded-circle text-white"
                style="
                    width:42px;
                    height:42px;
                    background:rgba(255,255,255,.13);
                    font-size:25px;
                    transition:.2s;
                "
                onmouseover="this.style.background='rgba(255,255,255,.25)'"
                onmouseout="this.style.background='rgba(255,255,255,.13)'"
            >
                &times;
            </button>


            <div class="position-relative pe-5">

                {{-- LABEL --}}
                <div
                    class="d-inline-flex align-items-center gap-2 rounded-pill px-3 py-2 mb-3"
                    style="background:rgba(255,255,255,.12);"
                >
                    <span
                        class="rounded-circle bg-success"
                        style="
                            width:8px;
                            height:8px;
                            box-shadow:0 0 0 4px rgba(16,185,129,.15);
                        "
                    ></span>

                    <span
                        class="fw-bold"
                        style="font-size:.7rem;letter-spacing:.08em;"
                    >
                        FITUR BARU
                    </span>
                </div>


                <h2 class="fw-bold mb-2"
                    style="font-size:clamp(1.5rem,4vw,2.3rem);">
                    Ujian Lebih Mudah & Terpusat 🚀
                </h2>

                <p
                    class="text-white-50 mb-0"
                    style="max-width:650px;line-height:1.7;"
                >
                    Nikmati berbagai pembaruan untuk membantu Bapak/Ibu
                    mengelola soal, ujian, timer, nilai, dan rekap
                    dengan lebih cepat dan praktis.
                </p>

            </div>

        </div>


        {{-- ==================================================
            CONTENT
        ================================================== --}}
        <div
            style="
                max-height:48vh;
                overflow-y:auto;
            "
        >

            <div class="p-3 p-md-4">

                <div class="row g-3">


                    {{-- BANK SOAL --}}
                    <div class="col-md-6">

                        <div
                            class="h-100 rounded-4 p-4 border"
                            style="
                                background:#eef2ff;
                                border-color:#c7d2fe !important;
                            "
                        >

                            <div
                                class="d-flex align-items-center justify-content-center rounded-3 mb-3"
                                style="
                                    width:46px;
                                    height:46px;
                                    background:#4f46e5;
                                    color:white;
                                "
                            >
                                <i class="fas fa-database"></i>
                            </div>

                            <h6 class="fw-bold text-dark mb-2">
                                Bank Soal Terpusat
                            </h6>

                            <p class="small text-muted mb-0"
                               style="line-height:1.65;">
                                Buat atau unggah soal sekali, kemudian
                                gunakan fitur <strong>Salin Soal</strong>
                                untuk kelas lain tanpa membuat database
                                menjadi penuh.
                            </p>

                        </div>

                    </div>


                    {{-- EXCEL --}}
                    <div class="col-md-6">

                        <div
                            class="h-100 rounded-4 p-4 border"
                            style="
                                background:#ecfdf5;
                                border-color:#a7f3d0 !important;
                            "
                        >

                            <div
                                class="d-flex align-items-center justify-content-center rounded-3 mb-3"
                                style="
                                    width:46px;
                                    height:46px;
                                    background:#059669;
                                    color:white;
                                "
                            >
                                <i class="fas fa-file-excel"></i>
                            </div>

                            <h6 class="fw-bold text-dark mb-2">
                                Import Soal via Excel
                            </h6>

                            <p class="small text-muted mb-0"
                               style="line-height:1.65;">
                                Unduh template, isi soal dan kunci jawaban,
                                lalu upload. Mendukung kode
                                <strong>MathJax / LaTeX</strong>.
                            </p>

                        </div>

                    </div>


                    {{-- TIMER --}}
                    <div class="col-md-6">

                        <div
                            class="h-100 rounded-4 p-4 border"
                            style="
                                background:#fffbeb;
                                border-color:#fde68a !important;
                            "
                        >

                            <div
                                class="d-flex align-items-center justify-content-center rounded-3 mb-3"
                                style="
                                    width:46px;
                                    height:46px;
                                    background:#f59e0b;
                                    color:white;
                                "
                            >
                                <i class="fas fa-stopwatch"></i>
                            </div>

                            <h6 class="fw-bold text-dark mb-2">
                                Smart Timer
                            </h6>

                            <p class="small text-muted mb-0"
                               style="line-height:1.65;">
                                Durasi ujian tetap berjalan meskipun siswa
                                melakukan refresh, menutup browser,
                                atau berpindah tab.
                            </p>

                        </div>

                    </div>


                    {{-- TUTUP PAKSA --}}
                    <div class="col-md-6">

                        <div
                            class="h-100 rounded-4 p-4 border"
                            style="
                                background:#fff7ed;
                                border-color:#fed7aa !important;
                            "
                        >

                            <div
                                class="d-flex align-items-center justify-content-center rounded-3 mb-3"
                                style="
                                    width:46px;
                                    height:46px;
                                    background:#f97316;
                                    color:white;
                                "
                            >
                                <i class="fas fa-lock"></i>
                            </div>

                            <h6 class="fw-bold text-dark mb-2">
                                Tutup Paksa
                            </h6>

                            <p class="small text-muted mb-0"
                               style="line-height:1.65;">
                                Tutup ujian siswa yang masih menggantung,
                                nilai jawaban terakhir secara otomatis,
                                dan kunci portal kelas.
                            </p>

                        </div>

                    </div>


                    {{-- RESET --}}
                    <div class="col-md-6">

                        <div
                            class="h-100 rounded-4 p-4 border"
                            style="
                                background:#fef2f2;
                                border-color:#fecaca !important;
                            "
                        >

                            <div
                                class="d-flex align-items-center justify-content-center rounded-3 mb-3"
                                style="
                                    width:46px;
                                    height:46px;
                                    background:#dc2626;
                                    color:white;
                                "
                            >
                                <i class="fas fa-rotate-left"></i>
                            </div>

                            <h6 class="fw-bold text-dark mb-2">
                                Reset Semua
                            </h6>

                            <p class="small text-muted mb-0"
                               style="line-height:1.65;">
                                Reset nilai dan riwayat jawaban satu kelas
                                ketika terjadi kendala massal.
                            </p>

                        </div>

                    </div>


                    {{-- LEGGER --}}
                    <div class="col-md-6">

                        <div
                            class="h-100 rounded-4 p-4 border"
                            style="
                                background:#f0f9ff;
                                border-color:#bae6fd !important;
                            "
                        >

                            <div
                                class="d-flex align-items-center justify-content-center rounded-3 mb-3"
                                style="
                                    width:46px;
                                    height:46px;
                                    background:#0284c7;
                                    color:white;
                                "
                            >
                                <i class="fas fa-download"></i>
                            </div>

                            <h6 class="fw-bold text-dark mb-2">
                                Download Legger Excel
                            </h6>

                            <p class="small text-muted mb-0"
                               style="line-height:1.65;">
                                Download nilai dalam format
                                <strong>.xlsx</strong> dengan header,
                                data siswa, dan rata-rata yang rapi.
                            </p>

                        </div>

                    </div>


                    {{-- PRINT A4 --}}
                    <div class="col-12">

                        <div
                            class="rounded-4 p-4 border d-flex align-items-center"
                            style="
                                background:#faf5ff;
                                border-color:#e9d5ff !important;
                            "
                        >

                            <div
                                class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0 me-3"
                                style="
                                    width:52px;
                                    height:52px;
                                    background:#9333ea;
                                    color:white;
                                "
                            >
                                <i class="fas fa-print"></i>
                            </div>

                            <div>

                                <h6 class="fw-bold text-dark mb-1">
                                    Cetak Rekap 1 Lembar A4
                                </h6>

                                <p class="small text-muted mb-0"
                                   style="line-height:1.6;">
                                    Rekap nilai telah dioptimalkan untuk
                                    dicetak dalam satu lembar A4,
                                    lengkap dengan kop identitas ujian.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ==================================================
            FOOTER
        ================================================== --}}
        <div
            class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 px-4 py-3 border-top"
            style="background:#f8fafc;"
        >

            <div class="text-center text-sm-start">

                <div class="fw-bold text-dark small">
                    Sistem ujian semakin lengkap.
                </div>

                <div class="text-muted"
                     style="font-size:.75rem;">
                    Semua fitur baru siap digunakan.
                </div>

            </div>


            <button
                type="button"
                onclick="closeFeatureModal()"
                class="btn btn-dark rounded-pill px-4 py-2 fw-bold shadow-sm"
            >
                <i class="fas fa-check me-2"></i>
                Mengerti, Tutup
            </button>

        </div>

    </div>

</div>


{{-- ========================================================
    JAVASCRIPT MODAL
========================================================= --}}
<script>

    function openFeatureModal() {

        const modal = document.getElementById('featureModal');
        const box   = document.getElementById('featureModalBox');

        if (!modal || !box) return;

        modal.style.opacity = '1';
        modal.style.visibility = 'visible';

        setTimeout(() => {
            box.style.transform = 'translateY(0) scale(1)';
        }, 20);

        document.body.style.overflow = 'hidden';
    }


    function closeFeatureModal() {

        const modal = document.getElementById('featureModal');
        const box   = document.getElementById('featureModalBox');

        if (!modal || !box) return;

        box.style.transform = 'translateY(25px) scale(.97)';
        modal.style.opacity = '0';
        modal.style.visibility = 'hidden';

        document.body.style.overflow = '';

    }


    // Tutup menggunakan tombol ESC
    document.addEventListener('keydown', function(event) {

        if (event.key === 'Escape') {
            closeFeatureModal();
        }

    });


    // Tutup jika klik area gelap di luar modal
    document.getElementById('featureModal')?.addEventListener(
        'click',
        function(event) {

            if (event.target === this) {
                closeFeatureModal();
            }

        }
    );


    // Tampilkan otomatis ketika halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', function() {

        setTimeout(function() {
            openFeatureModal();
        }, 250);

    });

</script>

@endsection
