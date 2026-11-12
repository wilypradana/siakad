@extends('layouts.admin')

@section('title', $materi->judul_materi)

@section('content')
<div class="mb-3">
    <a href="{{ route('siswa.elearning.materi.list', $materi->mapel_id) }}" class="btn btn-light border btn-sm rounded-pill px-3 fw-semibold">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Materi
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
    <div class="mb-3 border-bottom pb-3">
        <div class="d-flex align-items-center gap-2 mb-2">
            @if(isset($materi->is_tugas) && $materi->is_tugas)
                <span class="badge bg-warning text-dark border border-warning rounded-pill px-2" style="font-size: 11px;">
                    <i class="fas fa-tasks me-1"></i> TUGAS SISWA
                </span>
            @else
                <span class="badge bg-primary bg-opacity-10 text-primary fw-semibold px-2 py-1" style="font-size: 11px;">
                    <i class="fas fa-book me-1"></i> MATERI PEMBELAJARAN
                </span>
            @endif
            <span class="text-muted small">&bull; {{ $materi->mapel->nama_mapel ?? 'Mapel' }}</span>
        </div>

        <h4 class="fw-bold text-dark mb-1">{{ $materi->judul_materi }}</h4>
        <p class="text-muted small mb-0">
            <i class="fas fa-chalkboard-teacher me-1"></i> Pengajar: <b>{{ $materi->guru->nama ?? 'Guru' }}</b> | 
            <i class="far fa-clock ms-2 me-1"></i> Diterbitkan: {{ $materi->created_at->translatedFormat('d F Y - H:i') }}
        </p>
    </div>

    <!-- Isi / Deskripsi Materi & Instruksi Tugas -->
    <div class="article-content text-dark mb-4" style="line-height: 1.8; font-size: 14px;">
        {!! $materi->deskripsi !!}
    </div>

    <!-- Tombol Akses Zero-Storage (Google Drive / YouTube) -->
    @if($materi->link_materi)
        <div class="p-3 rounded-4 bg-light border d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h6 class="fw-bold text-dark mb-1 small"><i class="fas fa-external-link-alt text-primary me-2"></i> Lampiran File / Video / Link Pengumpulan Tugas</h6>
                <p class="text-muted small mb-0" style="font-size: 12px;">Klik tombol di samping untuk membuka tautan eksternal dari pengajar.</p>
            </div>
            <a href="{{ $materi->link_materi }}" target="_blank" class="btn btn-primary btn-sm fw-bold rounded-pill px-4 shadow-sm">
                <i class="fas fa-globe me-2"></i> Buka Tautan Lampiran
            </a>
        </div>
    @endif
</div>

<!-- FORM PENGUMPULAN TUGAS KHUSUS JIKA MATERI INI ADALAH TUGAS -->
    @if(isset($materi->is_tugas) && $materi->is_tugas)
        @php
            $siswa = auth()->user()->siswa ?? null;
            $pengumpulan = $siswa ? \App\Models\PengumpulanTugas::where('tugas_id', $materi->id)->where('siswa_id', $siswa->id)->first() : null;
        @endphp

        <div class="card border border-warning shadow-sm rounded-4 bg-warning bg-opacity-10 p-4 mt-4">
            <h5 class="fw-bold text-dark mb-2"><i class="fas fa-upload text-warning me-2"></i> Form Pengumpulan Tugas</h5>
            <p class="text-muted small mb-3">Unggah file tugas Anda ke Google Drive, pastikan izin akses dibuka ("Siapa saja yang memiliki tautan"), lalu tempelkan linknya di bawah ini.</p>

            <form action="{{ route('siswa.elearning.materi.submit', $materi->id) }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="fw-bold text-dark small mb-1">Tautan / Link File Tugas (Google Drive) <span class="text-danger">*</span></label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-link"></i></span>
                        <input type="url" name="link_jawaban" value="{{ old('link_jawaban', $pengumpulan->link_jawaban ?? '') }}" class="form-control border-start-0 py-2" placeholder="https://drive.google.com/file/d/..." required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="fw-bold text-dark small mb-1">Catatan Tambahan untuk Guru (Opsional)</label>
                    <textarea name="catatan_siswa" class="form-control shadow-sm" rows="2" placeholder="Tulis catatan jika ada...">{{ old('catatan_siswa', $pengumpulan->catatan_siswa ?? '') }}</textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        @if($pengumpulan)
                            <span class="badge bg-success text-white px-3 py-2 rounded-pill small">
                                <i class="fas fa-check-circle me-1"></i> Tugas Telah Dikirim
                            </span>
                        @else
                            <span class="badge bg-secondary text-white px-3 py-2 rounded-pill small">
                                <i class="fas fa-exclamation-circle me-1"></i> Belum Mengumpulkan Tugas
                            </span>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-warning fw-bold rounded-pill px-4 shadow-sm text-dark">
                        <i class="fas fa-paper-plane me-1"></i> {{ $pengumpulan ? 'Perbarui Pengumpulan' : 'Kirim Tugas Sekarang' }}
                    </button>
                </div>
            </form>
        </div>
    @endif
@endsection