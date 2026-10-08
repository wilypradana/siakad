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

                    <p class="mb-3 text-white-80">
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
                        <span class="badge rounded-pill px-3 py-2"
                              style="background:rgba(255,255,255,.10);">
                            <i class="fas fa-book me-1"></i>
                            Absensi Siswa
                        </span>
                        <span class="badge rounded-pill px-3 py-2"
                              style="background:rgba(255,255,255,.10);">
                            <i class="fas fa-book me-1"></i>
                            Raport SAS & SAT
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

                                <span class="badge rounded-pill bg-light text-dark border px-3 py-2">
                                    <i class="fas fa-users me-1 text-primary"></i>
                                    {{ $jadwal->kelas->nama_kelas }}
                                </span>

                            </td>


                            <td class="text-center">
                            <!-- Tombol Absensi -->
                                    <a href="{{ route('guru.absensi', ['kelas_id' => $jadwal->kelas_id, 'mapel_id' => $jadwal->mapel_id]) }}" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                        <i class="fas fa-user-check me-1"></i> Absensi
                                    </a>
                                <a href="{{ route('guru.input_nilai', [
                                        'kelas_id' => $jadwal->kelas_id,
                                        'mapel_id' => $jadwal->mapel_id
                                    ]) }}"
                                    class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm">
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
{{-- ============================================================
    MODAL UPDATE FITUR BARU
============================================================ --}}
<div
    id="featureModal"
    class="modal fade"
    tabindex="-1"
    aria-labelledby="featureModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 overflow-hidden shadow-lg"
             style="border-radius: 24px;">

            {{-- HEADER --}}
            <div
                class="position-relative text-white p-4 p-md-5 overflow-hidden"
                style="
                    background:
                        radial-gradient(circle at 90% 10%, rgba(255,255,255,.15), transparent 25%),
                        radial-gradient(circle at 10% 100%, rgba(129,140,248,.25), transparent 30%),
                        linear-gradient(135deg,#111827,#312e81 55%,#6d28d9);
                "
            >

                {{-- Ornamen --}}
                <div
                    class="position-absolute rounded-circle"
                    style="
                        width:180px;
                        height:180px;
                        right:-70px;
                        top:-90px;
                        background:rgba(255,255,255,.06);
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
                    class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>

                <div class="position-relative pe-5">

                    <div
                        class="d-inline-flex align-items-center gap-2 rounded-pill px-3 py-2 mb-3"
                        style="background:rgba(255,255,255,.12);"
                    >
                        <span
                            class="d-inline-block rounded-circle bg-success"
                            style="width:8px;height:8px;"
                        ></span>

                        <span class="small fw-bold">
                            FITUR BARU
                        </span>
                    </div>

                    <h2
                        id="featureModalLabel"
                        class="fw-bold mb-2"
                        style="font-size:clamp(1.5rem,3vw,2.3rem);"
                    >
                        Pengelolaan Ujian Kini Lebih Cerdas 🚀
                    </h2>

                    <p class="text-white-50 mb-0" style="max-width:720px;">
                        Berbagai fitur baru telah hadir untuk membuat
                        pengelolaan soal, ujian, timer, nilai, dan rekap
                        menjadi lebih cepat dan praktis.
                    </p>

                </div>
            </div>


            {{-- ISI --}}
            <div class="modal-body bg-light p-3 p-md-4">

                <div class="row g-3">

                    {{-- BANK SOAL --}}
                    <div class="col-md-6 col-lg-4">
                        <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                            <div
                                class="d-flex align-items-center justify-content-center rounded-4 mb-3"
                                style="
                                    width:52px;
                                    height:52px;
                                    background:#eef2ff;
                                    color:#4f46e5;
                                "
                            >
                                <i class="fas fa-database fs-5"></i>
                            </div>

                            <h6 class="fw-bold mb-2">
                                Bank Soal Terpusat
                            </h6>

                            <p class="text-muted small mb-0 lh-lg">
                                Buat atau unggah soal sekali, lalu
                                <strong>Salin Soal</strong> ke kelas lain
                                tanpa membuat database membengkak.
                            </p>
                        </div>
                    </div>


                    {{-- EXCEL --}}
                    <div class="col-md-6 col-lg-4">
                        <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                            <div
                                class="d-flex align-items-center justify-content-center rounded-4 mb-3"
                                style="
                                    width:52px;
                                    height:52px;
                                    background:#ecfdf5;
                                    color:#059669;
                                "
                            >
                                <i class="fas fa-file-excel fs-5"></i>
                            </div>

                            <h6 class="fw-bold mb-2">
                                Import Soal via Excel
                            </h6>

                            <p class="text-muted small mb-0 lh-lg">
                                Import soal melalui template Excel.
                                Mendukung <strong>MathJax/LaTeX</strong>
                                untuk rumus matematika.
                            </p>
                        </div>
                    </div>


                    {{-- TIMER --}}
                    <div class="col-md-6 col-lg-4">
                        <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                            <div
                                class="d-flex align-items-center justify-content-center rounded-4 mb-3"
                                style="
                                    width:52px;
                                    height:52px;
                                    background:#fffbeb;
                                    color:#d97706;
                                "
                            >
                                <i class="fas fa-stopwatch fs-5"></i>
                            </div>

                            <h6 class="fw-bold mb-2">
                                Smart Timer
                            </h6>

                            <p class="text-muted small mb-0 lh-lg">
                                Timer tetap berjalan meskipun siswa
                                refresh, menutup browser, atau berpindah tab.
                            </p>
                        </div>
                    </div>


                    {{-- TUTUP PAKSA --}}
                    <div class="col-md-6 col-lg-4">
                        <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                            <div
                                class="d-flex align-items-center justify-content-center rounded-4 mb-3"
                                style="
                                    width:52px;
                                    height:52px;
                                    background:#fff7ed;
                                    color:#ea580c;
                                "
                            >
                                <i class="fas fa-lock fs-5"></i>
                            </div>

                            <h6 class="fw-bold mb-2">
                                Tutup Paksa
                            </h6>

                            <p class="text-muted small mb-0 lh-lg">
                                Tutup ujian siswa yang menggantung,
                                nilai otomatis diproses dan portal kelas
                                dapat dikunci.
                            </p>
                        </div>
                    </div>


                    {{-- RESET --}}
                    <div class="col-md-6 col-lg-4">
                        <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                            <div
                                class="d-flex align-items-center justify-content-center rounded-4 mb-3"
                                style="
                                    width:52px;
                                    height:52px;
                                    background:#fef2f2;
                                    color:#dc2626;
                                "
                            >
                                <i class="fas fa-rotate-left fs-5"></i>
                            </div>

                            <h6 class="fw-bold mb-2">
                                Reset Semua
                            </h6>

                            <p class="text-muted small mb-0 lh-lg">
                                Reset nilai dan riwayat jawaban
                                satu kelas ketika terjadi kendala massal.
                            </p>
                        </div>
                    </div>


                    {{-- LEGGER --}}
                    <div class="col-md-6 col-lg-4">
                        <div class="h-100 bg-white border rounded-4 p-4 shadow-sm">
                            <div
                                class="d-flex align-items-center justify-content-center rounded-4 mb-3"
                                style="
                                    width:52px;
                                    height:52px;
                                    background:#eff6ff;
                                    color:#2563eb;
                                "
                            >
                                <i class="fas fa-file-download fs-5"></i>
                            </div>

                            <h6 class="fw-bold mb-2">
                                Legger Excel .xlsx
                            </h6>

                            <p class="text-muted small mb-0 lh-lg">
                                Download legger dalam Excel asli dengan
                                header rapi dan kolom rata-rata siswa.
                            </p>
                        </div>
                    </div>


                    {{-- PRINT A4 --}}
                    <div class="col-12">
                        <div
                            class="bg-white border rounded-4 p-4 shadow-sm d-flex align-items-center"
                        >
                            <div
                                class="d-flex align-items-center justify-content-center rounded-4 flex-shrink-0 me-3"
                                style="
                                    width:58px;
                                    height:58px;
                                    background:#f5f3ff;
                                    color:#7c3aed;
                                "
                            >
                                <i class="fas fa-print fs-5"></i>
                            </div>

                            <div>
                                <h6 class="fw-bold mb-1">
                                    Cetak Rekap 1 Lembar A4
                                </h6>

                                <p class="text-muted small mb-0">
                                    Rekap nilai sudah dioptimalkan agar
                                    sekitar 35–40 nama siswa dapat tercetak
                                    dalam satu lembar A4.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


            {{-- FOOTER --}}
            <div class="modal-footer bg-white border-top px-4 py-3">

                <div class="form-check me-auto">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="dontShowToday"
                    >

                    <label
                        class="form-check-label small text-muted"
                        for="dontShowToday"
                    >
                        Jangan tampilkan lagi hari ini
                    </label>
                </div>

                <button
                    type="button"
                    class="btn btn-primary rounded-pill px-4 fw-semibold"
                    data-bs-dismiss="modal"
                >
                    <i class="fas fa-check me-1"></i>
                    Mengerti
                </button>

            </div>

        </div>
    </div>
</div>


{{-- ============================================================
    JAVASCRIPT MODAL
============================================================ --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const modalElement = document.getElementById('featureModal');

    if (!modalElement) {
        return;
    }

    const storageKey = 'feature_modal_hide_until';

    // Ambil tanggal hari ini
    const today = new Date().toISOString().split('T')[0];

    // Cek apakah user memilih "jangan tampilkan hari ini"
    const hideUntil = localStorage.getItem(storageKey);

    if (hideUntil !== today) {

        const modal = new bootstrap.Modal(modalElement);

        // Tampilkan modal
        modal.show();

    }


    // Checkbox
    const checkbox = document.getElementById('dontShowToday');

    if (checkbox) {

        checkbox.addEventListener('change', function () {

            if (this.checked) {

                // Simpan tanggal hari ini
                localStorage.setItem(storageKey, today);

            } else {

                // Hapus jika checkbox dibatalkan
                localStorage.removeItem(storageKey);

            }

        });

    }


    // Ketika modal ditutup tanpa checkbox,
    // besok tetap akan muncul kembali.
});
</script>


@endsection
