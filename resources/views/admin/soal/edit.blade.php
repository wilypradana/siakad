@extends('layouts.admin')

@section('title', 'Edit Soal CBT')

@section('content')
<div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
    <div class="card-header bg-white pt-3 pb-2 border-0">
        <h5 class="fw-bold"><i class="fas fa-edit text-warning me-2"></i> Edit Soal CBT</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.soal.update', $soal->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label class="fw-bold mb-2">Pertanyaan Soal</label>
                <!-- Menampilkan nilai lama ke dalam textarea -->
                <textarea name="pertanyaan" id="summernote" class="form-control">{!! $soal->pertanyaan !!}</textarea>
                <small class="text-muted"><i class="fas fa-info-circle"></i> Gunakan Text Editor (seperti Summernote/CKEditor) jika sudah dipasang, atau gunakan tag HTML <b>&lt;b&gt;tebal&lt;/b&gt;</b>, <i>&lt;i&gt;miring&lt;/i&gt;</i>, <u>&lt;u&gt;garis bawah&lt;/u&gt;</u> manual.</small>
            </div>

            <div class="mb-4 border p-3 rounded bg-light">
                <label class="fw-bold mb-2">Gambar Pendukung (Opsional)</label>
                @if($soal->gambar)
                    <div class="mb-2">
                        <img src="{{ asset('uploads/soal/'.$soal->gambar) }}" height="120" class="rounded shadow-sm">
                        <div class="small text-muted mt-1">Gambar saat ini</div>
                    </div>
                @endif
                <input type="file" name="gambar" class="form-control" accept="image/*">
                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Opsi A</label>
                    <input type="text" name="opsi_a" class="form-control" value="{{ $soal->opsi_a }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Opsi B</label>
                    <input type="text" name="opsi_b" class="form-control" value="{{ $soal->opsi_b }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Opsi C</label>
                    <input type="text" name="opsi_c" class="form-control" value="{{ $soal->opsi_c }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Opsi D</label>
                    <input type="text" name="opsi_d" class="form-control" value="{{ $soal->opsi_d }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Opsi E (Opsional)</label>
                    <input type="text" name="opsi_e" class="form-control" value="{{ $soal->opsi_e }}">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="fw-bold text-success mb-2"><i class="fas fa-check-circle me-1"></i> Kunci Jawaban Benar</label>
                    <select name="kunci_jawaban" class="form-select border-success text-success fw-bold" required>
                        <option value="A" {{ $soal->kunci_jawaban == 'A' ? 'selected' : '' }}>Opsi A</option>
                        <option value="B" {{ $soal->kunci_jawaban == 'B' ? 'selected' : '' }}>Opsi B</option>
                        <option value="C" {{ $soal->kunci_jawaban == 'C' ? 'selected' : '' }}>Opsi C</option>
                        <option value="D" {{ $soal->kunci_jawaban == 'D' ? 'selected' : '' }}>Opsi D</option>
                        <option value="E" {{ $soal->kunci_jawaban == 'E' ? 'selected' : '' }}>Opsi E</option>
                    </select>
                </div>
            </div>

            <hr class="mt-4">
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.soal.index', $soal->ujian_id) }}" class="btn btn-secondary me-2 px-4">Batal</a>
                <button type="submit" class="btn btn-warning text-dark fw-bold px-4"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- PLUGIN SUMMERNOTE LITE (TEXT EDITOR) -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Ketikkan pertanyaan soal di sini...',
            tabsize: 2,
            height: 250, // Tinggi kolom editor
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