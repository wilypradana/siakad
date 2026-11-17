@extends('layouts.admin')

@section('title', 'Portal E-Learning Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1 text-dark">
            <i class="fas fa-graduation-cap me-2 text-primary"></i> Portal E-learning
        </h4>
        <p class="text-muted small mb-0">Pilih mata pelajaran untuk mulai mengakses materi dan tugas pembelajaran.</p>
    </div>
</div>

<div class="card border shadow-sm rounded-3 overflow-hidden" style="border-color: #e2e8f0 !important;">
    <div class="position-absolute top-0 start-0 w-100" style="height: 4px; background-color: #2563eb;"></div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary" style="border-bottom: 2px solid #e2e8f0; font-size: 0.85rem;">
                    <tr>
                        <th class="ps-4 py-3" width="5%">NO</th>
                        <th class="py-3" width="48%">MATA PELAJARAN</th>
                        <th class="py-3" width="32%">GURU PENGAJAR</th>
                        <th class="text-center pe-4 py-3" width="15%">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwal_mapel as $index => $jadwal)
                        @php
                            $notif = $notif_per_mapel[$jadwal->mapel_id] ?? ['belum_dibaca' => 0, 'tugas_pending' => 0, 'total_notif' => 0];
                            $adaNotif = $notif['total_notif'] > 0;
                        @endphp

                        <tr class="transition-all">
                            <td class="ps-4 fw-bold text-muted small">{{ $index + 1 }}</td>
                            
                            <td>
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    @if($adaNotif)
                                        {{-- IKON HIJAU JIKA ADA MATERI / TUGAS BARU --}}
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-2" 
                                              style="width: 30px; height: 30px; background-color: #dcfce7; color: #16a34a;">
                                            <i class="fas fa-book-open"></i>
                                        </span>
                                        <span class="fw-bold text-dark">{{ $jadwal->mapel->nama_mapel ?? 'Mapel' }}</span>

                                        @if($notif['belum_dibaca'] > 0)
                                            <span class="badge rounded-pill px-2.5 py-1 fw-semibold pulse-notif" 
                                                  style="background-color: #16a34a; color: #ffffff; font-size: 0.7rem;">
                                                <i class="fas fa-bell me-1"></i> {{ $notif['belum_dibaca'] }} Materi Baru
                                            </span>
                                        @endif

                                        @if($notif['tugas_pending'] > 0)
                                            <span class="badge rounded-pill px-2.5 py-1 fw-semibold" 
                                                  style="background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-size: 0.7rem;">
                                                <i class="fas fa-tasks me-1"></i> {{ $notif['tugas_pending'] }} Tugas
                                            </span>
                                        @endif
                                    @else
                                        {{-- IKON BIRU STANDAR JIKA SEMUA SUDAH DIBACA --}}
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-2" 
                                              style="width: 30px; height: 30px; background-color: #eff6ff; color: #2563eb;">
                                            <i class="fas fa-book"></i>
                                        </span>
                                        <span class="fw-bold text-dark">{{ $jadwal->mapel->nama_mapel ?? 'Mapel' }}</span>
                                    @endif
                                </div>
                            </td>

                            <td class="text-muted small">
                                <i class="fas fa-chalkboard-teacher me-1 text-secondary"></i> {{ $jadwal->guru->nama ?? 'Guru Pengampu' }}
                            </td>

                            <td class="text-center pe-4">
                                @if($adaNotif)
                                    <a href="{{ route('siswa.elearning.materi.list', $jadwal->mapel_id) }}" 
                                       class="btn btn-success btn-sm rounded-pill px-3 fw-semibold shadow-sm position-relative">
                                        <i class="fas fa-folder-open me-1"></i> Buka
                                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                                            <span class="visually-hidden">Notifikasi Baru</span>
                                        </span>
                                    </a>
                                @else
                                    <a href="{{ route('siswa.elearning.materi.list', $jadwal->mapel_id) }}" 
                                       class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold shadow-sm">
                                        <i class="fas fa-folder me-1"></i> Buka
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted small">
                                <i class="fas fa-box-open fa-3x opacity-25 mb-2 text-primary"></i>
                                <p class="mb-0">Belum ada jadwal pelajaran untuk kelas Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .transition-all { transition: all 0.2s ease; }
    .table-hover tbody tr:hover td { background-color: #f8fafc; }
    .pulse-notif {
        animation: pulse-green 2s infinite;
    }
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.45); }
        70% { box-shadow: 0 0 0 6px rgba(22, 163, 74, 0); }
        100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
    }
</style>
@endsection