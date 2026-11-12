<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMUBUN Panel')</title>
    <link rel="icon" href="{{ asset('logo-smk.png') }}" type="image/png">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
    :root {
        --bg-main: #f8fafc; /* Latar belakang bersih dan terang (slate very light) */
        --sidebar-bg: #ffffff; /* Sidebar putih bersih */
        --card-bg: #ffffff;
        --primary-blue: #2563eb; /* Biru utama korporat */
        --text-dark: #1e293b; /* Teks utama gelap agar kontras dan jelas */
        --border-color: #e2e8f0; /* Garis pembatas tipis yang rapi */
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: var(--bg-main);
        color: var(--text-dark);
    }

    /* --- SIDEBAR CLEAN CORPORATE --- */
    #sidebar {
        min-height: 100vh;
        background-color: var(--sidebar-bg);
        width: 270px;
        transition: margin 0.3s ease-in-out;
        z-index: 1040;
        border-right: 1px solid var(--border-color);
        padding: 1.5rem 1rem;
    }
    
    #sidebar.collapsed {
        margin-left: -270px;
    }

    .sidebar-brand-box {
        background: #f1f5f9;
        padding: 1rem 1.25rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 2rem;
        border: 1px solid var(--border-color);
    }

    .sidebar-menu a { 
        color: #64748b; 
        text-decoration: none; 
        padding: 11px 16px; 
        display: flex;
        align-items: center;
        border-radius: 10px;
        margin-bottom: 4px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.15s ease-in-out;
    }
    
    .sidebar-menu a i {
        width: 24px;
        font-size: 1.05rem;
        margin-right: 12px;
        text-align: center;
    }

    .sidebar-menu a:hover { 
        background-color: #f1f5f9; 
        color: var(--primary-blue); 
    }

    .sidebar-menu a.active { 
        background-color: #eff6ff; 
        color: var(--primary-blue); 
        font-weight: 700;
    }

    .sidebar-heading {
        color: #94a3b8;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin: 1.5rem 0 0.5rem 14px;
    }

    /* --- KONTEN UTAMA & NAVBAR --- */
    #main-content {
        height: 100vh;
        overflow-y: auto;
        background-color: var(--bg-main);
    }

    .top-navbar {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        border-bottom: 1px solid var(--border-color);
        padding: 1rem 2rem;
        position: sticky;
        top: 0;
        z-index: 1030;
    }

    #toggleSidebar {
        border: 1px solid var(--border-color);
        background: #ffffff;
        color: #475569;
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
    }
    #toggleSidebar:hover {
        background-color: #f8fafc;
        color: var(--primary-blue);
    }

    .user-profile-badge {
        background: #ffffff;
        border: 1px solid var(--border-color);
        padding: 5px 16px 5px 6px;
        border-radius: 30px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* --- KARTU CLEAN CORPORATE (Minimalis, Sudut Standar, Garis Tipis) --- */
    .card {
        background-color: var(--card-bg) !important;
        border-radius: 16px !important;
        border: 1px solid var(--border-color) !important;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
    }

    /* --- TOMBOL & FORM CLEAN CORPORATE --- */
    .btn {
        border-radius: 10px !important;
        font-weight: 600;
        padding: 0.5rem 1rem;
        transition: all 0.15s ease-in-out;
    }
    
    .form-control, .form-select {
        background-color: #ffffff !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 10px !important;
        color: var(--text-dark) !important;
        padding: 0.55rem 0.85rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-blue) !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
    }

    /* --- KUSTOMISASI SCROLLBAR --- */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>
    <!-- MATHJAX -->
    <script>
        MathJax = {
            tex: { inlineMath: [['\\(', '\\)']], displayMath: [['$$', '$$']] }
        };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</head>
<body>

<div class="d-flex overflow-hidden">
    
    <!-- SIDEBAR -->
    <div id="sidebar" class="flex-shrink-0 z-3">
        
        <!-- Brand Box -->
        <div class="sidebar-brand-box">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 40px; height: 40px; background: #2563eb;">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div>
                <div class="fw-bold text-blue" style="font-size: 0.95rem; line-height: 1.1;">SIMUBUN</div>
                <div class= style="font-size: 0.7rem;">Portal Akademik</div>
            </div>
        </div>

        <div class="sidebar-menu">
            @if(auth()->check())
                <!-- MENU ADMIN -->
                @if(auth()->user()->role == 'admin')
                    <div class="sidebar-heading">Menu Utama</div>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i> Dashboard</a>
                    <a href="{{ route('admin.guru.index') }}" class="{{ request()->is('admin/guru*') ? 'active' : '' }}"><i class="fas fa-chalkboard-teacher"></i> Data Guru</a>
                    <a href="{{ route('admin.kelas.index') }}" class="{{ request()->is('admin/kelas*') ? 'active' : '' }}"><i class="fas fa-door-open"></i> Data Kelas</a>
                    <a href="{{ route('admin.siswa.index') }}" class="{{ request()->is('admin/siswa*') ? 'active' : '' }}"><i class="fas fa-user-graduate"></i> Data Siswa</a>
                    <a href="{{ route('admin.mapel.index') }}" class="{{ request()->is('admin/mapel*') ? 'active' : '' }}"><i class="fas fa-book"></i> Data Mapel</a>
                    
                    <div class="sidebar-heading">Akademik & Ujian</div>
                    <a href="{{ route('pembelajaran.index') }}" class="{{ request()->is('admin/pembelajaran*') ? 'active' : '' }}"><i class="fas fa-layer-group"></i> Set Pembelajaran</a>
                    <a href="{{ route('admin.jadwal.index') }}" class="{{ request()->is('admin/jadwal*') ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Jadwal Pelajaran</a>
                    <a href="{{ route('admin.nilai.index') }}" class="{{ request()->is('admin/nilai*') ? 'active' : '' }}"><i class="fas fa-file-signature"></i> Input Nilai</a>
                    <a href="{{ route('admin.ujian.index') }}" class="{{ request()->is('admin/ujian*') ? 'active' : '' }}"><i class="fas fa-laptop-code"></i> Portal Ujian</a>
                @endif

                <!-- MENU GURU -->
                @if(auth()->user()->role == 'guru')
                    <div class="sidebar-heading">Navigasi Guru</div>
                    <a href="{{ route('guru.dashboard') }}" class="{{ request()->is('guru/dashboard') ? 'active' : '' }}"><i class="fas fa-chalkboard"></i> Portal Guru Mapel</a>
                    <a href="{{ route('guru.elearning.index') }}" class="{{ request()->is('guru/elearning*') ? 'active' : '' }}"><i class="fas fa-book-reader"></i> Portal E-Learning</a>
                    
                    @php
                        $guru_id = \App\Models\Guru::where('user_id', auth()->id())->value('id');
                        $is_wali = \App\Models\Kelas::where('guru_id', $guru_id)->exists();
                    @endphp

                    @if($is_wali)
                        <div class="sidebar-heading mt-4">Tugas Tambahan</div>
                        <a href="{{ route('wali.dashboard') }}" class="{{ request()->is('wali-kelas*') ? 'active' : '' }}"><i class="fas fa-users-cog"></i> Wali Kelas</a>
                    @endif
                @endif

                <!-- MENU SISWA -->
                @if(auth()->user()->role == 'siswa')
                    <div class="sidebar-heading">Navigasi Siswa</div>
                    <a href="{{ route('siswa.dashboard') }}" class="{{ request()->is('siswa/dashboard') ? 'active' : '' }}"><i class="fas fa-user-graduate"></i> Portal Akademik</a>
                    <a href="{{ route('siswa.elearning.index') }}" class="{{ request()->is('siswa/elearning*') ? 'active' : '' }}">
                        <i class="fas fa-laptop-house"></i> Portal E-Learning
                    </a>
                @endif

                <!-- WIDGET KECIL DI BAWAH SIDEBAR -->
                <div class="mt-4 p-3 rounded-4" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="fas fa-info-circle text-warning"></i>
                        <span class="fw-bold text-black" style="font-size: 0.8rem;">Status SIMUBUN</span>
                    </div>
                    <p class="text-black-50 mb-2" style="font-size: 0.72rem; line-height: 1.4;">Sistem Aktif & Terintegrasi dengan E-Learning.</p>
                </div>

                <!-- LOGOUT BUTTON -->
                <div class="mt-3">
                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn w-100 py-2 d-flex align-items-center justify-content-center fw-semibold text-danger shadow-sm" style="background-color: rgba(239, 68, 68, 0.15); border: none; border-radius: 14px; font-size: 0.9rem;">
                            <i class="fas fa-power-off me-2"></i> Keluar Sistem
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <!-- KONTEN UTAMA -->
    <div id="main-content" class="flex-grow-1 d-flex flex-column">
        
        <!-- NAVBAR ATAS -->
        <div class="top-navbar d-flex align-items-center justify-content-between mb-4 z-2">
            <button id="toggleSidebar" class="btn">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Profil User Kanan Atas -->
            <div class="user-profile-badge">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width: 34px; height: 34px; background: #2563eb; font-size: 0.85rem;">
                    {{ auth()->check() ? strtoupper(substr(auth()->user()->name, 0, 1)) : 'G' }}
                </div>
                <div class="d-none d-md-block">
                    <div class="fw-bold text-dark" style="font-size: 0.82rem; line-height: 1.1;">
                        {{ auth()->check() ? auth()->user()->name : 'Guest' }}
                    </div>
                    <div class="text-muted" style="font-size: 0.7rem;">
                        {{ auth()->check() ? strtoupper(auth()->user()->role) : '' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- ISI KONTEN UTAMA (BLADE VIEW) -->
        <div class="px-4 pb-4">
            @yield('content')
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');

        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
        });

        if (window.innerWidth < 992) {
            sidebar.classList.add('collapsed');
        }
    });
</script>

</body>
</html>