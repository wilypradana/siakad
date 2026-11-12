@extends('layouts.admin')

@section('title', 'Rekap Pembacaan Materi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1"><i class="fas fa-clipboard-check text-primary me-2"></i> Rekap Absensi Membaca Materi</h4>
        <p class="text-muted small mb-0">Topik: <b>{{ $materi->judul_materi }}</b> | Kelas: <b>{{ $materi->kelas->nama_kelas ?? '-' }}</b></p>
    </div>
    <a href="{{ route('guru.elearning.materi.list', [$materi->kelas_id, $materi->mapel_id]) }}" class="btn btn-light border shadow-sm rounded-pill px-4 fw-semibold small">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">NIS / NISN</th>
                        <th width="45%">Nama Siswa</th>
                        <th width="30%" class="text-center">Status Pembelajaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($semua_siswa as $index => $siswa)
                        @php
                            $sudah_baca = isset($log_baca[$siswa->id]);
                            $waktu_baca = $sudah_baca ? $log_baca[$siswa->id]->read_at : null;
                        @endphp
                        <tr>
                            <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                            <td>{{ $siswa->nis ?? '-' }}</td>
                            <td class="fw-bold text-dark">{{ $siswa->nama }}</td>
                            <td class="text-center">
                                @if($sudah_baca)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-semibold" style="font-size: 12px;">
                                        <i class="fas fa-check-circle me-1"></i> Sudah Dibaca 
                                        <small class="d-block text-muted fw-normal mt-1" style="font-size: 10px;">{{ \Carbon\Carbon::parse($waktu_baca)->translatedFormat('d M Y - H:i') }}</small>
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-3 py-2 fw-semibold" style="font-size: 12px;">
                                        <i class="fas fa-times-circle me-1"></i> Belum Membuka Materi
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fa-3x mb-3 opacity-50"></i>
                                <br>Tidak ada data siswa di kelas ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection