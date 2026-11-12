@extends('layouts.admin')

@section('title', 'Bagikan Materi Baru')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-1" style="color: #2d3142;">
            <i class="fas fa-plus-circle me-2" style="color: #e07a5f;"></i> Bagikan Materi & Penugasan
        </h4>
        <p class="text-muted small mb-0">
            Mata Pelajaran: <span class="badge px-2.5 py-1 ms-1 rounded-pill" style="background-color: #fff7ed; color: #c05621; border: 1px solid rgba(224, 122, 95, 0.2);">{{ $mapel->nama_mapel }}</span>
        </p>
    </div>
    <a href="{{ route('guru.elearning.index') }}" class="btn bg-white border shadow-sm rounded-pill px-4 fw-semibold small text-muted transition-all" style="border-color: #e2e8f0 !important;">
        <i class="fas fa-arrow-left me-1"></i> Batal / Kembali
    </a>
</div>

<!-- INFORMASI ZERO-STORAGE -->
<div class="alert shadow-sm border-0 rounded-4 mb-4 d-flex align-items-center p-4" style="background-color: #fff7ed; border-left: 5px solid #e07a5f !important; border-radius: 20px !important;">
    <div class="d-flex align-items-center justify-content-center rounded-circle me-3 flex-shrink-0" style="width: 48px; height: 48px; background-color: #ffedd5; color: #c05621;">
        <i class="fas fa-cloud-upload-alt fs-5"></i>
    </div>
    <div class="small" style="color: #9a3412;">
        <span class="fw-bold d-block mb-1 fs-6">Kebijakan Zero-Storage Server SIMUBUN</span>
        Anda tidak perlu mengunggah file berat ke server. Cukup unggah dokumen/video ke <b>Google Drive</b> atau <b>YouTube</b>, lalu <i>copy-paste</i> tautannya di bawah.
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-radius: 22px !important;">
    <div class="position-absolute top-0 start-0 w-100" style="height: 5px; background-color: #e07a5f;"></div>
    
    <div class="card-body p-4 p-md-5">
        <form action="{{ route('guru.elearning.materi.store', $mapel_id) }}" method="POST">
            @csrf
            
            <!-- CHECKBOX MULTI-KELAS (DIPERBAIKI & DINAMIS) -->
            <div class="mb-4 p-4 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 18px !important;">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <label class="fw-bold small d-block mb-0" style="color: #2d3142;">
                        <i class="fas fa-check-double me-1" style="color: #e07a5f;"></i> Pilih Kelas Tujuan (Bisa Lebih Dari Satu) <span class="text-danger">*</span>
                    </label>
                    <span class="badge bg-white text-secondary border px-2.5 py-1 rounded-pill" style="font-size: 11px;">
                        {{ count($kelas_tersedia) }} Kelas Tersedia
                    </span>
                </div>

                <div class="row g-3">
                    @foreach($kelas_tersedia as $item)
                        <div class="col-xl-4 col-md-6 col-sm-12">
                            <div class="bg-white p-3 rounded-3 border shadow-sm d-flex align-items-center justify-content-between transition-all" style="border-radius: 14px !important; border-color: #e2e8f0 !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-door-open text-primary opacity-75"></i>
                                    <span class="fw-bold small text-dark">{{ $item->kelas->nama_kelas }}</span>
                                </div>
                                <div class="form-check form-switch m-0 ps-0 d-flex align-items-center">
                                    <input class="form-check-input ms-0" type="checkbox" name="kelas_id[]" value="{{ $item->kelas_id }}" id="kelas_{{ $item->kelas_id }}" style="cursor: pointer; width: 2.5em; height: 1.25em;">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- OPSI PILIHAN TUGAS -->
            <div class="mb-4 p-3.5 rounded-4 border" style="background-color: #fffbeb; border-color: #fde68a !important; border-radius: 18px !important;">
                <div class="form-check form-switch d-flex align-items-center justify-content-between ps-0">
                    <label class="form-check-label fw-bold small order-1 flex-grow-1 ms-3" for="isTugasCheck" style="cursor: pointer; color: #92400e;">
                        <i class="fas fa-tasks me-1 text-warning"></i> Sertakan / Jadikan Postingan ini sebagai Penugasan Siswa
                    </label>
                    <input class="form-check-input order-2 m-0" type="checkbox" name="is_tugas" value="1" id="isTugasCheck" style="cursor: pointer; width: 2.5em; height: 1.25em;">
                </div>
                <small class="d-block mt-2 text-muted" style="font-size: 11px;">Jika dicentang, sistem akan memberikan label khusus sebagai tugas di portal siswa.</small>
            </div>

            <!-- FORM JUDUL / TOPIK MATERI -->
            <div class="mb-4">
                <label class="fw-bold mb-2 small" style="color: #2d3142;">Judul / Topik Materi & Tugas <span class="text-danger">*</span></label>
                <input type="text" name="judul_materi" class="form-control fw-bold py-2.5 custom-focus" placeholder="Contoh: Pertemuan 1 - Konsep Dasar & Tugas Mandiri" required style="border-radius: 12px; border: 1.5px solid #e2e8f0; color: #1e293b;">
            </div>

            <!-- SUMMERNOTE TEXT EDITOR -->
            <div class="mb-4">
                <label class="fw-bold mb-2 small" style="color: #2d3142;">Penjelasan / Instruksi Pengerjaan <span class="text-danger">*</span></label>
                <textarea name="deskripsi" id="summernote" class="form-control" required></textarea>
            </div>

            <!-- INPUT LINK ZERO-STORAGE -->
            <div class="mb-5 p-4 rounded-4 border" style="background-color: #f8fafc; border-radius: 18px !important;">
                <label class="fw-bold mb-2 small" style="color: #475569;">
                    <i class="fas fa-link me-1" style="color: #e07a5f;"></i> Tautkan Lampiran (URL Google Drive / YouTube)
                </label>
                <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-globe"></i></span>
                    <input type="url" name="link_materi" class="form-control border-start-0 py-2.5 custom-focus" placeholder="https://drive.google.com/... (Opsional)">
                </div>
            </div>

            <!-- TOMBOL AKSI SIMPAN -->
            <div class="text-end pt-3 border-top">
                <button type="submit" class="btn rounded-pill px-5 py-2.5 shadow-sm fw-bold btn-submit-peach">
                    <i class="fas fa-paper-plane me-2"></i> Terbitkan ke Kelas Terpilih
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .transition-all { transition: all 0.2s ease-in-out; }
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
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(224, 122, 95, 0.3) !important;
        color: #fff;
    }
</style>

<!-- SUMMERNOTE SCRIPT -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Tuliskan penjelasan materi atau instruksi tugas secara rinci di sini...',
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