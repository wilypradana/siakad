@extends('layouts.admin')

@section('title', 'Edit Materi / Tugas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: #2d3142;">
            <i class="fas fa-edit me-2" style="color: #e07a5f;"></i> Edit Materi & Penugasan
        </h4>
        <p class="text-muted small mb-0">Perbarui informasi, status tugas, atau tautan materi pembelajaran.</p>
    </div>
    <a href="{{ route('guru.elearning.materi.list', [$materi->kelas_id, $materi->mapel_id]) }}" class="btn bg-white border shadow-sm rounded-pill px-4 fw-semibold small text-muted transition-all" style="border-color: #e2e8f0 !important;">
        <i class="fas fa-arrow-left me-1"></i> Batal / Kembali
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-radius: 22px !important;">
    <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background-color: #e07a5f;"></div>

    <div class="card-body p-4 p-md-5">
        <form action="{{ route('guru.elearning.materi.update', $materi->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- OPSI STATUS TUGAS (DIPERBAIKI AGAR RAPI & RESPONSIF) -->
            <div class="mb-4 p-3.5 rounded-4 border" style="background-color: #fffbeb; border-color: #fde68a !important; border-radius: 18px !important;">
                <div class="form-check form-switch d-flex align-items-center justify-content-between ps-0">
                    <label class="form-check-label fw-bold small order-1 flex-grow-1 ms-3" for="isTugasCheck" style="cursor: pointer; color: #92400e;">
                        <i class="fas fa-tasks me-1 text-warning"></i> Sertakan / Jadikan Postingan ini sebagai Penugasan Siswa
                    </label>
                    <input class="form-check-input order-2 m-0" type="checkbox" name="is_tugas" value="1" id="isTugasCheck" {{ $materi->is_tugas ? 'checked' : '' }} style="cursor: pointer; width: 2.5em; height: 1.25em;">
                </div>
            </div>

            <!-- FORM JUDUL -->
            <div class="mb-4">
                <label class="fw-bold mb-2 small" style="color: #2d3142;">Judul / Topik Materi & Tugas <span class="text-danger">*</span></label>
                <input type="text" name="judul_materi" value="{{ old('judul_materi', $materi->judul_materi) }}" class="form-control fw-bold py-2.5 custom-focus" style="border-radius: 12px; border: 1.5px solid #e2e8f0; color: #1e293b;" required>
            </div>

            <!-- SUMMERNOTE TEXT EDITOR -->
            <div class="mb-4">
                <label class="fw-bold mb-2 small" style="color: #2d3142;">Penjelasan / Instruksi Pengerjaan <span class="text-danger">*</span></label>
                <textarea name="deskripsi" id="summernote" class="form-control" required>{{ old('deskripsi', $materi->deskripsi) }}</textarea>
            </div>

            <!-- INPUT LINK ZERO-STORAGE -->
            <div class="mb-4 p-4 rounded-4 border" style="background-color: #f8fafc; border-radius: 18px !important;">
                <label class="fw-bold mb-2 small" style="color: #475569;"><i class="fas fa-link me-1" style="color: #e07a5f;"></i> Tautkan Lampiran (URL Google Drive / YouTube)</label>
                <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-globe"></i></span>
                    <input type="url" name="link_materi" value="{{ old('link_materi', $materi->link_materi) }}" class="form-control border-start-0 py-2.5 custom-focus" placeholder="https://drive.google.com/...">
                </div>
            </div>

            <!-- TOMBOL AKSI -->
            <div class="text-end pt-3 border-top">
                <button type="submit" class="btn rounded-pill px-5 py-2.5 shadow fw-bold btn-submit-peach">
                    <i class="fas fa-save me-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .transition-all { transition: all 0.3s ease; }
    .form-check-input:checked {
        background-color: #e07a5f !important;
        border-color: #e07a5f !important;
    }
    .custom-focus:focus {
        border-color: #e07a5f !important;
        box-shadow: 0 0 0 3px rgba(224, 122, 95, 0.15) !important;
    }
    .btn-submit-peach {
        background-color: #e07a5f;
        color: #ffffff;
        border: none;
        transition: all 0.3s ease;
    }
    .btn-submit-peach:hover {
        background-color: #cc664a;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(224, 122, 95, 0.3) !important;
    }
</style>

<!-- SUMMERNOTE SCRIPT -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Tuliskan penjelasan materi...',
            tabsize: 2,
            height: 250,
            toolbar: [
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    });
</script>
@endsection