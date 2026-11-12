@extends('layouts.admin')

@section('title', 'Edit Soal CBT')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1 text-dark">
            <i class="fas fa-edit me-2 text-warning"></i> Edit Soal CBT
        </h5>
        <p class="text-muted small mb-0">Perbarui pertanyaan, opsi jawaban, atau gambar pendukung.</p>
    </div>
    <a href="{{ route('admin.soal.index', $soal->ujian_id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-4 fw-semibold shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Batal / Kembali
    </a>
</div>

<div class="card border shadow-sm rounded-3 mb-4" style="border-color: #e2e8f0 !important;">
    <div class="card-body p-4">
        <form action="{{ route('admin.soal.update', $soal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="fw-bold mb-2 small text-dark">Pertanyaan Soal <span class="text-danger">*</span></label>
                <textarea name="pertanyaan" id="summernote" class="form-control" required>{!! $soal->pertanyaan !!}</textarea>
                <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle me-1"></i> Gunakan Text Editor di atas, atau gunakan tag HTML <b>&lt;b&gt;tebal&lt;/b&gt;</b>, <i>&lt;i&gt;miring&lt;/i&gt;</i>, <u>&lt;u&gt;garis bawah&lt;/u&gt;</u> manual.</small>
            </div>

            <div class="mb-4 border p-3 rounded-3 bg-light" style="border-color: #e2e8f0 !important;">
                <label class="fw-bold mb-2 small text-dark">Gambar Pendukung (Opsional)</label>
                @if($soal->gambar)
                    <div class="mb-2">
                        <img src="{{ asset('uploads/soal/'.$soal->gambar) }}" height="120" class="rounded-2 shadow-sm border">
                        <div class="small text-muted mt-1">Gambar saat ini</div>
                    </div>
                @endif
                <input type="file" name="gambar" class="form-control form-control-sm shadow-none" accept="image/*" style="border-radius: 8px;">
                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
            </div>

            <div class="alert alert-info py-2.5 px-3 border-0 rounded-3 small mb-3" style="background-color: #eff6ff; color: #1e40af;">
                <i class="fas fa-info-circle me-1"></i> <b>Tips:</b> Jika jawaban berupa gambar, Anda cukup mengetik nama file gambarnya (misal: <code>opsi_a.png</code>) di dalam form opsi di bawah ini.
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="fw-bold small text-dark mb-1">Opsi A <span class="text-danger">*</span></label>
                    <input type="text" name="opsi_a" class="form-control form-control-sm shadow-none" value="{{ $soal->opsi_a }}" required style="border-radius: 8px;">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small text-dark mb-1">Opsi B <span class="text-danger">*</span></label>
                    <input type="text" name="opsi_b" class="form-control form-control-sm shadow-none" value="{{ $soal->opsi_b }}" required style="border-radius: 8px;">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small text-dark mb-1">Opsi C <span class="text-danger">*</span></label>
                    <input type="text" name="opsi_c" class="form-control form-control-sm shadow-none" value="{{ $soal->opsi_c }}" required style="border-radius: 8px;">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small text-dark mb-1">Opsi D <span class="text-danger">*</span></label>
                    <input type="text" name="opsi_d" class="form-control form-control-sm shadow-none" value="{{ $soal->opsi_d }}" required style="border-radius: 8px;">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small text-dark mb-1">Opsi E (Opsional)</label>
                    <input type="text" name="opsi_e" class="form-control form-control-sm shadow-none" value="{{ $soal->opsi_e }}" style="border-radius: 8px;">
                </div>
                
                <div class="col-md-6">
                    <label class="fw-bold text-success small mb-1"><i class="fas fa-check-circle me-1"></i> Kunci Jawaban Benar <span class="text-danger">*</span></label>
                    <select name="kunci_jawaban" class="form-select form-select-sm border-success text-success fw-bold shadow-none" required style="border-radius: 8px;">
                        <option value="A" {{ $soal->kunci_jawaban == 'A' ? 'selected' : '' }}>Opsi A</option>
                        <option value="B" {{ $soal->kunci_jawaban == 'B' ? 'selected' : '' }}>Opsi B</option>
                        <option value="C" {{ $soal->kunci_jawaban == 'C' ? 'selected' : '' }}>Opsi C</option>
                        <option value="D" {{ $soal->kunci_jawaban == 'D' ? 'selected' : '' }}>Opsi D</option>
                        <option value="E" {{ $soal->kunci_jawaban == 'E' ? 'selected' : '' }}>Opsi E</option>
                    </select>
                </div>
            </div>

            <div class="text-end pt-4 mt-3 border-top">
                <a href="{{ route('admin.soal.index', $soal->ujian_id) }}" class="btn btn-sm btn-secondary rounded-pill me-2 px-4">Batal</a>
                <button type="submit" class="btn btn-sm btn-warning rounded-pill text-dark fw-bold px-4"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- PLUGIN SUMMERNOTE LITE -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Ketikkan pertanyaan soal di sini...',
            tabsize: 2,
            height: 250,
            toolbar: [
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview']]
            ]
        });
    });
</script>
@endsection