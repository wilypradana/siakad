@extends('layouts.admin')

@section('title', 'Penilaian Pengumpulan Tugas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: #2d3142;">
            <i class="fas fa-tasks me-2" style="color: #e07a5f;"></i> Penilaian Pengumpulan Tugas Siswa
        </h4>
        <p class="text-muted small mb-0">
            Topik Tugas: <b class="text-dark">{{ $materi->judul_materi }}</b> | 
            Kelas: <b class="text-dark">{{ $materi->kelas->nama_kelas ?? '-' }}</b>
        </p>
    </div>
    <a href="{{ route('guru.elearning.materi.list', [$materi->kelas_id, $materi->mapel_id]) }}" class="btn bg-white border shadow-sm rounded-pill px-4 fw-semibold small text-muted transition-all" style="border-color: #e2e8f0 !important;">
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
                        <th width="15%" class="py-3" style="color: #9a3412;">NIS / NISN</th>
                        <th width="30%" class="py-3" style="color: #9a3412;">Nama Siswa</th>
                        <th width="25%" class="py-3 text-center" style="color: #9a3412;">Status Pengiriman</th>
                        <th width="25%" class="py-3 text-center" style="color: #9a3412;">Aksi / Tautan Jawaban</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($semua_siswa as $index => $siswa)
                        @php
                            $tugas_siswa = $pengumpulan[$siswa->id] ?? null;
                        @endphp
                        <tr class="transition-all">
                            <td class="fw-bold text-muted text-center">{{ $index + 1 }}</td>
                            <td class="text-secondary">{{ $siswa->nis ?? '-' }}</td>
                            <td class="fw-bold" style="color: #2d3142;">{{ $siswa->nama }}</td>
                            <td class="text-center">
                                @if($tugas_siswa)
                                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-size: 11px;">
                                        <i class="fas fa-check-circle me-1"></i> Sudah Mengumpulkan
                                        <small class="d-block text-muted fw-normal mt-0.5" style="font-size: 9px;">{{ \Carbon\Carbon::parse($tugas_siswa->updated_at)->translatedFormat('d M Y - H:i') }}</small>
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #f8fafc; color: #64748b; border: 1px solid #cbd5e1; font-size: 11px;">
                                        <i class="fas fa-clock me-1"></i> Belum Mengumpulkan
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($tugas_siswa && $tugas_siswa->link_jawaban)
                                    <a href="{{ $tugas_siswa->link_jawaban }}" target="_blank" class="btn btn-sm rounded-pill px-3 fw-semibold shadow-sm btn-buka-tugas">
                                        <i class="fas fa-external-link-alt me-1"></i> Buka File Tugas
                                    </a>
                                    @if($tugas_siswa->catatan_siswa)
                                        <small class="d-block text-muted mt-1 fst-italic" style="font-size: 10px;" title="{{ $tugas_siswa->catatan_siswa }}">
                                            Catatan: "{{ Str::limit($tugas_siswa->catatan_siswa, 30) }}"
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
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
    .btn-buka-tugas {
        background-color: #e07a5f;
        color: white;
        border: none;
    }
    .btn-buka-tugas:hover {
        background-color: #cc664a;
        color: white;
    }
</style>
@endsection