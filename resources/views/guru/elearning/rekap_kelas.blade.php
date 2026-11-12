@extends('layouts.admin')

@section('title', 'Rekap Progres Belajar Siswa')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: #2d3142;">
            <i class="fas fa-chart-line me-2" style="color: #e07a5f;"></i> Rekap Progres Belajar Kelas
        </h4>
        <p class="text-muted small mb-0">
            Mata Pelajaran: <b class="text-dark">{{ $mapel->nama_mapel }}</b> | 
            Kelas: <b class="text-dark">{{ $kelas->nama_kelas }}</b> | 
            Total Materi: <b style="color: #e07a5f;">{{ $total_materi }} Topik</b>
        </p>
    </div>
    <a href="{{ route('guru.elearning.materi.list', [$kelas_id, $mapel_id]) }}" class="btn bg-white border shadow-sm rounded-pill px-4 fw-semibold small text-muted transition-all" style="border-color: #e2e8f0 !important;">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-radius: 22px !important;">
    <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background-color: #e07a5f;"></div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #fff7ed; border-bottom: 2px solid #ffedd5;">
                    <tr>
                        <th width="5%" class="py-3 text-center" style="color: #9a3412;">No</th>
                        <th width="20%" class="py-3" style="color: #9a3412;">NIS / NISN</th>
                        <th width="40%" class="py-3" style="color: #9a3412;">Nama Siswa</th>
                        <th width="35%" class="py-3 text-center" style="color: #9a3412;">Materi Diikuti & Progres</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($progres_siswa as $index => $item)
                        <tr class="transition-all">
                            <td class="fw-bold text-muted text-center">{{ $index + 1 }}</td>
                            <td class="text-secondary">{{ $item['siswa']->nis ?? '-' }}</td>
                            <td class="fw-bold" style="color: #2d3142;">{{ $item['siswa']->nama }}</td>
                            <td class="px-4">
                                <div class="d-flex align-items-center justify-content-between mb-1.5 small">
                                    <span class="fw-bold" style="color: #e07a5f; font-size: 0.82rem;">
                                        Dibaca: {{ $item['jumlah_dibaca'] }} / {{ $total_materi }} Materi
                                    </span>
                                    <span class="text-muted fw-semibold" style="font-size: 0.82rem;">{{ $item['persentase'] }}%</span>
                                </div>
                                <!-- Progress Bar Senada SIMUBUN -->
                                <div class="progress" style="height: 8px; border-radius: 6px; background-color: #f1f5f9;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $item['persentase'] }}%; background-color: #e07a5f; border-radius: 6px;" aria-valuenow="{{ $item['persentase'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="mb-3" style="color: #e07a5f; opacity: 0.5;">
                                    <i class="fas fa-users-slash fa-3x"></i>
                                </div>
                                <h6 class="fw-bold text-dark">Tidak Ada Data Siswa</h6>
                                <p class="text-muted small mb-0">Belum ada data siswa terdaftar di kelas ini.</p>
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
    .table-hover tbody tr:hover td { background-color: #fffbeb; }
</style>
@endsection