@extends('layouts.admin')

@section('title', 'Daftar Materi & Tugas ' . $mapel->nama_mapel)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: #2d3142;">
            <i class="fas fa-book-reader me-2 text-primary"></i> {{ $mapel->nama_mapel }}
        </h4>
        <p class="text-muted small mb-0">Daftar bahan ajar dan penugasan yang dibagikan oleh pengajar.</p>
    </div>
    <a href="{{ route('siswa.elearning.index') }}" class="btn bg-white border shadow-sm rounded-pill px-4 py-1.5 fw-semibold small text-muted transition-all" style="border-color: #e2e8f0 !important;">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-radius: 22px !important;">
    <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background-color: #2563eb;"></div>

    <div class="card-body p-3">
        <div class="list-group list-group-flush">
            @forelse($materis as $materi)
                @php
                    $sudah_baca = \App\Models\MateriLog::where('materi_id', $materi->id)
                                    ->where('siswa_id', auth()->user()->siswa->id ?? 0)
                                    ->exists();
                @endphp
                <div class="list-group-item d-flex justify-content-between align-items-center py-3.5 px-3 border-bottom transition-all hover-bg-light rounded-3 my-1">
                    <div>
                        <div class="d-flex align-items-center flex-wrap gap-2 mb-1.5">
                            @if(isset($materi->is_tugas) && $materi->is_tugas)
                                <span class="badge bg-warning text-dark border border-warning rounded-pill px-2.5 py-1" style="font-size: 10px;">
                                    <i class="fas fa-tasks me-1"></i> TUGAS
                                </span>
                            @else
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 10px;">
                                    <i class="fas fa-book me-1"></i> MATERI
                                </span>
                            @endif

                            <h6 class="fw-bold text-dark mb-0">{{ $materi->judul_materi }}</h6>

                            @if($sudah_baca)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1" style="font-size: 10px;">
                                    <i class="fas fa-check-circle me-1"></i> Sudah Dibaca
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2.5 py-1" style="font-size: 10px;">
                                    <i class="fas fa-clock me-1"></i> Belum Dibaca
                                </span>
                            @endif
                        </div>
                        <span class="text-muted small" style="font-size: 12px;">
                            <i class="fas fa-chalkboard-teacher me-1 text-secondary"></i> {{ $materi->guru->nama ?? 'Guru' }} &bull; 
                            <i class="far fa-clock ms-2 me-1 text-secondary"></i> {{ $materi->created_at->translatedFormat('d F Y - H:i') }}
                        </span>
                    </div>
                    <a href="{{ route('siswa.elearning.show', $materi->id) }}" class="btn btn-primary btn-sm rounded-pill px-3.5 fw-semibold shadow-sm">
                        {{ $sudah_baca ? 'Buka Lagi' : 'Buka' }} <i class="fas fa-chevron-right ms-1 small"></i>
                    </a>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-folder-open fa-3x mb-3 text-primary opacity-25"></i>
                    <p class="mb-0">Belum ada materi atau tugas untuk mata pelajaran ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .transition-all { transition: all 0.2s ease; }
    .hover-bg-light:hover { background-color: #f8fafc !important; }
</style>
@endsection