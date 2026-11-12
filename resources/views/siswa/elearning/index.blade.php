@extends('layouts.admin')

@section('title', 'Portal E-Learning Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: #2d3142;">
            <i class="fas fa-graduation-cap me-2 text-primary"></i> Portal E-learning
        </h4>
        <p class="text-muted small mb-0">Pilih mata pelajaran untuk mulai mengakses materi dan tugas pembelajaran.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-radius: 22px !important;">
    <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background-color: #2563eb;"></div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #eff6ff; border-bottom: 2px solid #bfdbfe;">
                    <tr>
                        <th class="ps-4 py-3 text-secondary" width="5%">NO</th>
                        <th class="py-3 text-secondary" width="45%">MATA PELAJARAN</th>
                        <th class="py-3 text-secondary" width="35%">GURU PENGAJAR</th>
                        <th class="text-center pe-4 py-3 text-secondary" width="15%">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwal_mapel as $index => $jadwal)
                        <tr class="transition-all">
                            <td class="ps-4 fw-bold text-muted small">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark">
                                <i class="fas fa-book text-primary me-2"></i> {{ $jadwal->mapel->nama_mapel ?? 'Mapel' }}
                            </td>
                            <td class="text-muted small">
                                <i class="fas fa-chalkboard-teacher me-1 text-secondary"></i> {{ $jadwal->guru->nama ?? 'Guru Pengampu' }}
                            </td>
                            <td class="text-center pe-4">
                                <a href="{{ route('siswa.elearning.materi.list', $jadwal->mapel_id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-semibold shadow-sm">
                                    <i class="fas fa-folder-open me-1"></i> Buka
                                </a>
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
    .transition-all { transition: all 0.3s ease; }
    .table-hover tbody tr:hover td { background-color: #f8fafc; }
</style>
@endsection