@extends('layouts.admin')

@section('title', 'Daftar Materi & Tugas')

@section('content')

{{-- NOTIFIKASI BERHASIL / SUKSES --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center justify-content-between p-3" role="alert" style="background-color: #d1e7dd; color: #0f5132; border-radius: 20px !important;">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
            <div>
                <strong class="fw-bold">Berhasil!</strong> 
                <span>{{ session('success') }}</span>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: #2d3142;"><i class="fas fa-book me-2" style="color: #e07a5f;"></i> Daftar Materi Tersimpan</h3>
        <p class="text-muted small mb-0">
            Mata Pelajaran: <b class="text-dark">{{ $mapel->nama_mapel }}</b> | 
            Kelas: <b class="text-dark">{{ $kelas->nama_kelas }}</b>
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('guru.elearning.materi.rekap_kelas', [$kelas_id, $mapel_id]) }}" class="btn rounded-pill shadow-sm fw-bold px-3.5 small d-flex align-items-center transition-all btn-rekap">
            <i class="fas fa-chart-bar me-2"></i> Rekap Progres
        </a>
        <a href="{{ route('guru.elearning.materi.create', $mapel_id) }}" class="btn rounded-pill shadow-sm fw-bold px-3.5 small d-flex align-items-center transition-all btn-tambah">
            <i class="fas fa-plus me-2"></i> Tambah Baru
        </a>
        <a href="{{ route('guru.elearning.index') }}" class="btn bg-white border shadow-sm rounded-pill fw-bold px-3 small text-muted transition-all" style="border-color: #e2e8f0 !important;">
            Kembali
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-radius: 22px !important;">
    <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background-color: #e07a5f;"></div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background-color: #fff7ed; border-bottom: 2px solid #ffedd5;">
                    <tr>
                        <th width="5%" class="py-3 text-center" style="color: #9a3412;">No</th>
                        <th width="32%" class="py-3" style="color: #9a3412;">Judul Materi / Topik</th>
                        <th width="18%" class="py-3" style="color: #9a3412;">Lampiran</th>
                        <th width="15%" class="py-3" style="color: #9a3412;">Tanggal Dibuat</th>
                        <th width="30%" class="py-3 text-center" style="color: #9a3412;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materis as $index => $materi)
                    <tr class="transition-all">
                        <td class="fw-bold text-muted text-center">{{ $index + 1 }}</td>
                        <td class="py-3">
                            <div class="d-flex align-items-center gap-2">
                                @if(isset($materi->is_tugas) && $materi->is_tugas)
                                    <span class="badge rounded-pill px-2.5 py-1" style="background-color: #fffbeb; border: 1px solid #fde68a; color: #b45309; font-size: 9px; letter-spacing: 0.5px;">TUGAS</span>
                                @else
                                    <span class="badge rounded-pill px-2.5 py-1" style="background-color: #f1f5f9; border: 1px solid #cbd5e1; color: #64748b; font-size: 9px; letter-spacing: 0.5px;">MATERI</span>
                                @endif
                                <span class="fw-bold" style="color: #2d3142;">{{ $materi->judul_materi }}</span>
                            </div>
                        </td>
                        <td>
                            @if($materi->link_materi)
                                <a href="{{ $materi->link_materi }}" target="_blank" class="btn btn-sm rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center" style="background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-size: 11px; font-weight: 500;">
                                    <i class="fas fa-external-link-alt me-1"></i> Buka Tautan
                                </a>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1 fw-normal" style="font-size: 11px;">Teks Saja</span>
                            @endif
                        </td>
                        <td class="text-muted small">
                            <div class="fw-medium text-dark"><i class="far fa-calendar-alt me-1" style="color: #e07a5f;"></i> {{ $materi->created_at->format('d M Y') }}</div>
                            <div style="font-size: 10px;" class="text-secondary ms-3">{{ $materi->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap">
                                @if(isset($materi->is_tugas) && $materi->is_tugas)
                                    <!-- Tombol Cek Tugas Siswa -->
                                    <a href="{{ route('guru.elearning.materi.cek_tugas', $materi->id) }}" class="btn btn-sm rounded-pill px-3 py-1 shadow-sm fw-bold d-inline-flex align-items-center" style="background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-size: 11px;" title="Cek Pengumpulan Tugas">
                                        <i class="fas fa-tasks me-1"></i> Cek Tugas
                                    </a>
                                @endif

                                <!-- Tombol Rekap Pembaca -->
                                <a href="{{ route('guru.elearning.materi.rekap', $materi->id) }}" class="btn btn-sm bg-white border rounded-pill px-2.5 py-1 shadow-sm text-info" style="font-size: 12px; border-color: #e2e8f0 !important;" title="Rekap Pembaca">
                                    <i class="fas fa-users"></i>
                                </a>

                                <!-- Tombol Edit -->
                                <a href="{{ route('guru.elearning.materi.edit', $materi->id) }}" class="btn btn-sm bg-white border rounded-pill px-2.5 py-1 shadow-sm text-primary" style="font-size: 12px; border-color: #e2e8f0 !important;" title="Edit Materi">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Form Tombol Hapus -->
                                <form action="{{ route('guru.elearning.materi.destroy', $materi->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus materi/tugas ini? Data pengumpulan siswa juga akan terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm bg-white border rounded-pill px-2.5 py-1 shadow-sm text-danger" style="font-size: 12px; border-color: #e2e8f0 !important;" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="mb-3" style="color: #e07a5f; opacity: 0.5;">
                                <i class="fas fa-folder-open fa-3x"></i>
                            </div>
                            <h6 class="fw-bold" style="color: #475569;">Belum Ada Materi</h6>
                            <p class="small mb-0 text-muted">Silakan buat materi atau tugas baru untuk kelas ini.</p>
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
    
    .btn-tambah {
        background-color: #e07a5f;
        color: #ffffff;
        border: none;
    }
    .btn-tambah:hover {
        background-color: #cc664a;
        color: #ffffff;
    }
    
    .btn-rekap {
        background-color: #f8fafc;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
    .btn-rekap:hover {
        background-color: #e2e8f0;
        color: #1e293b;
    }
</style>
@endsection