@extends('layouts.admin')

@section('title', 'Portal Siswa')

@section('content')
@php 
    // Mengambil data siswa yang terhubung dengan akun login secara dinamis
    $siswa = \App\Models\Siswa::where('user_id', auth()->user()->id)->first(); 
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white shadow border-0" style="border-radius: 15px; background: linear-gradient(135deg, #0d6efd, #0dcaf0);">
            <div class="card-body d-flex align-items-center p-4">
                <div class="rounded-circle bg-white text-primary d-flex justify-content-center align-items-center me-4 shadow" style="width: 80px; height: 80px;">
                    <i class="fas fa-user-graduate fa-3x"></i>
                </div>
                <div>
                    @if($siswa)
                        <h3 class="mb-1 fw-bold">Halo, {{ $siswa->nama }}! 👋</h3>
                        <p class="mb-0 fs-6">
                            <i class="fas fa-id-card me-1"></i> NIS: {{ $siswa->nis }} | 
                            <i class="fas fa-door-open ms-2 me-1"></i> Kelas: <span class="badge bg-light text-primary">{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</span>
                        </p>
                    @else
                        <h3 class="mb-1 fw-bold">Halo, {{ auth()->user()->name }}! 👋</h3>
                        <span class="badge bg-danger">⚠️ Akun Belum Terhubung ke Profil Siswa (User ID: {{ auth()->user()->id }})</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- NOTIFIKASI ERROR/SUCCESS DARI CONTROLLER --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" style="border-radius: 10px;">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- GATEKEEPER ADMINISTRASI - INFO KARTU DI TU --}}
@if($siswa)
    <div class="row mb-4">
        <div class="col-12">
            @if($siswa->status_bayar == 1)
                <div class="alert alert-success shadow-sm border-0 py-3" style="border-radius: 10px;">
                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <i class="fas fa-check-circle fa-2x text-success me-2"></i>
                        <div>
                            <h5 class="fw-bold mb-1 text-success">Administrasi Lunas!</h5>
                            <p class="mb-0 small text-dark">
                                Status pembayaran Anda telah divalidasi. 
                                <br>Silakan mengambil <b>Kartu Peserta Ujian </b> Anda, yang berisi <b>Kode Unik Ujian</b> di bagian Tata Usaha (TU).
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning shadow-sm border-0 py-3" style="border-radius: 10px;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-money-check-alt fa-2x text-warning me-3"></i>
                        <div>
                            <h5 class="fw-bold mb-1 text-dark">Administrasi Belum Lengkap</h5>
                            <p class="mb-0 small text-dark">Mohon maaf, Anda belum menyelesaikan persyaratan administrasi. Silakan segera hubungi bagian <b>Tata Usaha (TU)</b> untuk mendapatkan Kartu Ujian Anda.</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif

<div class="row">
    {{-- BAGIAN UJIAN --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold text-dark"><i class="fas fa-edit text-primary me-2"></i>Daftar Ujian</h5>
            </div>
            <div class="card-body">
                @forelse($ujian_aktif as $ujian)
                    @php
                        $mulai = \Carbon\Carbon::parse($ujian->waktu_mulai);
                        $selesai = \Carbon\Carbon::parse($ujian->waktu_selesai);
                        $is_berjalan = now()->between($mulai, $selesai);
                        $lunas = $siswa && $siswa->status_bayar == 1;

                        // CEK STATUS: Pastikan hanya pakai tabel HasilUjian untuk CBT & GForm
                        $sudah_mengerjakan = \App\Models\HasilUjian::where('ujian_id', $ujian->id)
                            ->where('siswa_id', $siswa->id)
                            ->exists();
                    @endphp

                    <div class="card border-0 shadow-sm mb-3 {{ $is_berjalan ? 'bg-light border-start border-success border-4' : 'border-start border-secondary border-4' }}">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold">{{ $ujian->mapel->nama_mapel ?? 'Mapel Tidak Diketahui' }} {{ $ujian->judul_ujian != '-' ? '- ' . $ujian->judul_ujian : '' }}</h5>
                                <p class="mb-0 text-muted small">
                                    <i class="fas fa-calendar-alt text-info me-1"></i> {{ $mulai->isoFormat('dddd, D MMMM YYYY') }}
                                    <span class="ms-2">
                                        <i class="fas fa-clock text-warning me-1"></i> {{ $mulai->format('H:i') }} - {{ $selesai->format('H:i') }} WIB
                                    </span>
                                </p>
                            </div>
                            
                            {{-- LOGIKA TOMBOL DINAMIS --}}
                            @if($sudah_mengerjakan)
                                <button class="btn btn-success btn-sm rounded-pill px-4 shadow-sm" disabled>
                                    <i class="fas fa-check-double me-1"></i> Selesai
                                </button>
                            @elseif($is_berjalan && $lunas)
                                <!-- CEK APAKAH GURU SUDAH KLIK MULAI -->
                                @if($ujian->is_active)
                                    <a href="{{ route('siswa.ujian.kerjakan', $ujian->id) }}" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm pulse-button">
                                        Kerjakan <i class="fas fa-chevron-right ms-1"></i>
                                    </a>
                                @else
                                    <button class="btn btn-warning btn-sm rounded-pill px-4 shadow-sm text-dark fw-bold" disabled>
                                        <i class="fas fa-hourglass-half me-1"></i> Menunggu Guru
                                    </button>
                                @endif
                            @else
                                <button class="btn btn-secondary btn-sm rounded-pill px-4" disabled>
                                    <i class="fas fa-lock me-1"></i> Terkunci
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted">Tidak ada ujian aktif.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- JADWAL HARI INI --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h5 class="fw-bold small text-muted">JADWAL HARI INI</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($jadwal_hari_ini as $jadwal)
                        <li class="list-group-item px-4 py-3 d-flex justify-content-between border-0 border-bottom">
                            <span class="fw-bold">{{ $jadwal->mapel->nama_mapel }}</span>
                            <span class="badge bg-light text-dark">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</span>
                        </li>
                    @empty
                        <li class="list-group-item px-4 py-4 text-center text-muted border-0 small italic">Belum ada jadwal KBM.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- TABEL REKAPITULASI STATUS UJIAN --}}
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fas fa-tasks text-primary me-2"></i> Rekapitulasi Status Ujian Mata Pelajaran
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">NO</th>
                                <th>MATA PELAJARAN</th>
                                <th class="text-center">STATUS UJIAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mapel_kelas as $index => $mapel)
                                @php
                                    // Kueri langsung ke Database untuk menghindari error Undefined Variable
                                    $ujianMapel = \App\Models\Ujian::where('mapel_id', $mapel->id)
                                                    ->where('kelas_id', $siswa->kelas_id)
                                                    ->first();
                                    
                                    $status_keterangan = 'Belum Ada Jadwal Ujian';
                                    $badge_class = 'bg-secondary text-white';

                                    if ($ujianMapel) {
                                        // Pengecekan aman di tabel HasilUjian
                                        $sudah_dikerjakan = \App\Models\HasilUjian::where('ujian_id', $ujianMapel->id)
                                                                ->where('siswa_id', $siswa->id)
                                                                ->exists();
                                        
                                        if ($sudah_dikerjakan) {
                                            $status_keterangan = 'Sudah Ujian (Selesai)';
                                            $badge_class = 'bg-success text-white';
                                        } else {
                                            $waktu_sekarang = now();
                                            $mulai = \Carbon\Carbon::parse($ujianMapel->waktu_mulai);
                                            $selesai = \Carbon\Carbon::parse($ujianMapel->waktu_selesai);

                                            if ($waktu_sekarang->between($mulai, $selesai)) {
                                                $status_keterangan = 'Belum Ujian (Ujian Sedang Berlangsung)';
                                                $badge_class = 'bg-primary text-white';
                                            } elseif ($waktu_sekarang->lt($mulai)) {
                                                $status_keterangan = 'Belum Ujian (Akan Datang)';
                                                $badge_class = 'bg-warning text-dark';
                                            } else {
                                                $status_keterangan = 'Belum Ujian (Waktu Telah Lewat)';
                                                $badge_class = 'bg-danger text-white';
                                            }
                                        }
                                    }
                                @endphp
                                
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ strtoupper($mapel->nama_mapel) }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $badge_class }} px-3 py-2 shadow-sm" style="font-size: 0.85rem;">
                                            {{ $status_keterangan }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Data mata pelajaran kelas tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABEL REKAPITULASI KEHADIRAN HARIAN --}}
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
            <div class="card-header bg-white border-0 pt-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="fas fa-user-check text-success me-2"></i> Rekap Kehadiran per Mata Pelajaran
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center py-3">NO</th>
                                <th class="py-3">MATA PELAJARAN</th>
                                <th width="10%" class="text-center py-3">HADIR</th>
                                <th width="10%" class="text-center py-3">SAKIT</th>
                                <th width="10%" class="text-center py-3">IZIN</th>
                                <th width="10%" class="text-center py-3">ALFA</th>
                                <th width="15%" class="text-center py-3">PERSENTASE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mapel_kelas as $index => $mapel)
                                @php
                                    // Ambil data absensi harian dari database (sesuaikan kolom mapel_id jika di database Anda bernama jadwal_id atau pembelajaran_id)
                                    $absen_mapel = \App\Models\AbsensiSiswa::where('siswa_id', $siswa->id)
                                                        ->where('mapel_id', $mapel->id)
                                                        ->get();
                                                        
                                    // Hitung jumlah masing-masing status (mendukung huruf kapital, huruf kecil, atau inisial H/S/I/A)
                                    $hadir = $absen_mapel->whereIn('status', ['Hadir', 'hadir', 'H', 'h'])->count();
                                    $sakit = $absen_mapel->whereIn('status', ['Sakit', 'sakit', 'S', 's'])->count();
                                    $izin  = $absen_mapel->whereIn('status', ['Izin', 'izin', 'I', 'i'])->count();
                                    $alfa  = $absen_mapel->whereIn('status', ['Alfa', 'alfa', 'A', 'a', 'Alpha'])->count();
                                    
                                    $total_pertemuan = $hadir + $sakit + $izin + $alfa;
                                    
                                    // Hitung Persentase Kehadiran
                                    $persentase = $total_pertemuan > 0 ? round(($hadir / $total_pertemuan) * 100) : 0;
                                    
                                    // Warna Bar Persentase
                                    $bar_color = 'bg-success';
                                    if($persentase < 75) $bar_color = 'bg-danger';
                                    elseif($persentase < 90) $bar_color = 'bg-warning';
                                @endphp
                                
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ strtoupper($mapel->nama_mapel) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-1 shadow-sm">{{ $hadir }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3 py-1 shadow-sm">{{ $sakit }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3 py-1 shadow-sm">{{ $izin }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3 py-1 shadow-sm">{{ $alfa }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($total_pertemuan > 0)
                                            <div class="progress shadow-sm mb-1" style="height: 8px; border-radius: 10px;">
                                                <div class="progress-bar {{ $bar_color }}" role="progressbar" style="width: {{ $persentase }}%;" aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <small class="fw-bold {{ $bar_color == 'bg-success' ? 'text-success' : ($bar_color == 'bg-danger' ? 'text-danger' : 'text-warning') }}">{{ $persentase }}%</small>
                                        @else
                                            <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 0.75rem;">Belum ada data</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Data mata pelajaran kelas tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .pulse-button { animation: pulse 2s infinite; }
    @keyframes pulse { 
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7); } 
        70% { transform: scale(1.03); box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); } 
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); } 
    }
</style>
@endsection