@extends('layouts.admin')

@section('title', 'Tambah Soal Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus-circle text-primary me-2"></i>Tambah Soal Baru</h2>
    <a href="{{ route('admin.soal.index', $ujian->id) }}" class="btn btn-secondary shadow-sm">Batal / Kembali</a>
</div>

<div class="card shadow border-0" style="border-radius: 15px;">
    <div class="card-body p-4">
        <form action="{{ route('admin.soal.store', $ujian->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="form-label fw-bold text-dark">Teks Pertanyaan <span class="text-danger">*</span></label>
                <textarea name="pertanyaan" class="form-control bg-light" rows="5" required placeholder="Ketik soal atau studi kasus di sini..."></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-dark">Gambar Pendukung <span class="text-muted fw-normal">(Opsional)</span></label>
                <input type="file" name="gambar" class="form-control" accept="image/jpeg,image/png,image/jpg">
                <small class="text-muted">Format: JPG/PNG, Maksimal ukuran: 2MB.</small>
            </div>

            <hr class="mb-4">
            <h5 class="fw-bold mb-3 text-secondary"><i class="fas fa-list-ol me-2"></i>Pilihan Jawaban</h5>
            
            <div class="row bg-light p-3 rounded mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Opsi A <span class="text-danger">*</span></label>
                    <textarea name="opsi_a" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Opsi B <span class="text-danger">*</span></label>
                    <textarea name="opsi_b" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Opsi C <span class="text-danger">*</span></label>
                    <textarea name="opsi_c" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Opsi D <span class="text-danger">*</span></label>
                    <textarea name="opsi_d" class="form-control" rows="2" required></textarea>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label fw-bold">Opsi E <span class="text-muted fw-normal">(Khusus SMK/SMA - Kosongkan jika tidak perlu)</span></label>
                    <textarea name="opsi_e" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <div class="mb-4 col-md-4">
                <label class="form-label fw-bold text-success fs-5">Kunci Jawaban Benar <span class="text-danger">*</span></label>
                <select name="kunci_jawaban" class="form-select form-select-lg border-success" required>
                    <option value="">-- Pilih Kunci --</option>
                    <option value="A">Jawaban A</option>
                    <option value="B">Jawaban B</option>
                    <option value="C">Jawaban C</option>
                    <option value="D">Jawaban D</option>
                    <option value="E">Jawaban E</option>
                </select>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-primary btn-lg shadow-sm px-5 fw-bold"><i class="fas fa-save me-2"></i>Simpan Soal</button>
            </div>
        </form>
    </div>
</div>
@endsection