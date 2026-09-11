<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIAKAD Panel')</title>
    <link rel="icon" href="{{ asset('logo-smk.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Transisi mulus untuk animasi hide/unhide */
        #sidebar {
            min-height: 100vh;
            background-color: #343a40;
            width: 250px;
            transition: margin 0.3s ease-in-out; /* Kunci animasinya di sini */
        }
        
        /* Class ini ditambahkan oleh Javascript saat tombol diklik */
        #sidebar.collapsed {
            margin-left: -250px; /* Menggeser sidebar keluar layar ke kiri */
        }

        #sidebar a { 
            color: #cfd8dc; 
            text-decoration: none; 
            padding: 12px 20px; 
            display: block; 
            border-left: 4px solid transparent; 
            transition: all 0.2s;
        }
        #sidebar a:hover, #sidebar a.active { 
            background-color: #495057; 
            color: white; 
            border-left-color: #0d6efd; 
        }

        /* Area Konten Utama */
        #main-content {
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-light">

<div class="d-flex overflow-hidden">
    
    <div id="sidebar" class="text-white py-3 flex-shrink-0 shadow-lg z-3">
        <h4 class="text-center mb-4 border-bottom pb-3">Sistem Akademik</h4>

        @if(auth()->check())
            @if(auth()->user()->role == 'admin')
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->is('admin/dashboard') ? 'active' : '' }}"><i class="fas fa-home me-2"></i> Dashboard</a>
                <a href="{{ route('admin.guru.index') }}" class="{{ request()->is('admin/guru*') ? 'active' : '' }}"><i class="fas fa-chalkboard-teacher me-2"></i> Data Guru</a>
                <a href="{{ route('admin.kelas.index') }}" class="{{ request()->is('admin/kelas*') ? 'active' : '' }}"><i class="fas fa-door-open me-2"></i> Data Kelas</a>
                <a href="{{ route('admin.siswa.index') }}" class="{{ request()->is('admin/siswa*') ? 'active' : '' }}"><i class="fas fa-user-graduate me-2"></i> Data Siswa</a>
                <a href="{{ route('admin.mapel.index') }}" class="{{ request()->is('admin/mapel*') ? 'active' : '' }}"><i class="fas fa-book me-2"></i> Data Mapel</a>
                <a href="{{ route('pembelajaran.index') }}" class="{{ request()->is('admin/pembelajaran*') ? 'active' : '' }}"><i class="fas fa-layer-group me-2"></i> Set Pembelajaran</a>
                <a href="{{ route('admin.jadwal.index') }}" class="{{ request()->is('admin/jadwal*') ? 'active' : '' }}"><i class="fas fa-calendar-alt me-2"></i> Jadwal Pelajaran</a>
                <a href="{{ route('admin.nilai.index') }}" class="{{ request()->is('admin/nilai*') ? 'active' : '' }}"><i class="fas fa-file-signature me-2"></i> Input Nilai</a>
                <a href="{{ route('admin.ujian.index') }}" class="{{ request()->is('admin/ujian*') ? 'active' : '' }}"><i class="fas fa-laptop-code me-2"></i> Portal Ujian</a>
            @endif

            @if(auth()->user()->role == 'guru')
                <a href="{{ route('guru.dashboard') }}" class="{{ request()->is('guru/dashboard') ? 'active' : '' }}"><i class="fas fa-chalkboard me-2"></i> Portal Guru Mapel</a>
                
                @php
                    $guru_id = \App\Models\Guru::where('user_id', auth()->id())->value('id');
                    $is_wali = \App\Models\Kelas::where('guru_id', $guru_id)->exists();
                @endphp

                @if($is_wali)
                    <hr class="text-white my-2" style="opacity: 0.2;">
                    <small class="text-white px-3 d-block mb-1" style="opacity: 0.5; font-size: 11px; letter-spacing: 1px;">TUGAS TAMBAHAN</small>
                    <a href="{{ route('wali.dashboard') }}" class="{{ request()->is('wali-kelas*') ? 'active' : '' }}"><i class="fas fa-users-cog me-2"></i> Wali Kelas</a>
                @endif
            @endif

            @if(auth()->user()->role == 'siswa')
                <a href="{{ route('siswa.dashboard') }}" class="{{ request()->is('siswa/dashboard') ? 'active' : '' }}"><i class="fas fa-user-graduate me-2"></i> Portal Siswa</a>
            @endif

            <div class="mt-5 px-3">
                <form action="{{ url('/logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100"><i class="fas fa-sign-out-alt me-1"></i> Keluar</button>
                </form>
            </div>
        @endif
    </div>

    <div id="main-content" class="flex-grow-1 d-flex flex-column bg-light">
        
        <div class="bg-white shadow-sm p-3 d-flex align-items-center justify-content-between mb-4 z-2">
            <button id="toggleSidebar" class="btn btn-light border">
                <i class="fas fa-bars"></i>
            </button>

            <div class="fw-bold text-secondary">
                <i class="fas fa-user-circle me-1 text-primary"></i> 
                {{ auth()->check() ? auth()->user()->name : 'Guest' }} 
                <span class="badge bg-primary ms-2">{{ auth()->check() ? strtoupper(auth()->user()->role) : '' }}</span>
            </div>
        </div>

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

        // Saat tombol diklik, tambah/hapus class 'collapsed' di sidebar
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
        });

        // Fitur Tambahan Cerdas: 
        // Jika web dibuka di HP atau layar kecil, otomatis sembunyikan sidebar di awal
        if (window.innerWidth < 768) {
            sidebar.classList.add('collapsed');
        }
    });
</script>

</body>
</html>