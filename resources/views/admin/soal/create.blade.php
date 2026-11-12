@extends('layouts.admin')

@section('title', 'Tambah Soal Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold mb-1 text-dark">
            <i class="fas fa-plus-circle me-2 text-primary"></i>Tambah Soal Baru
        </h3>
        <p class="text-muted small mb-0">Ujian: <strong class="text-dark">{{ $ujian->judul_ujian }}</strong></p>
    </div>
    <a href="{{ route('admin.soal.index', $ujian->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-4 fw-semibold shadow-sm">
        <i class="fas fa-arrow-left me-1"></i> Batal / Kembali
    </a>
</div>

<div class="card border shadow-sm rounded-3" style="border-color: #e2e8f0 !important;">
    <div class="card-body p-4">
        <form action="{{ route('admin.soal.store', $ujian->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="form-label fw-bold text-dark small">Teks Pertanyaan <span class="text-danger">*</span></label>
                <textarea name="pertanyaan" class="form-control shadow-none" rows="5" required placeholder="Ketik soal atau studi kasus di sini..." style="border-radius: 8px; border-color: #cbd5e1;"></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-dark small">Gambar Pendukung <span class="text-muted fw-normal">(Opsional)</span></label>
                <input type="file" name="gambar" class="form-control form-control-sm shadow-none" accept="image/jpeg,image/png,image/jpg" style="border-radius: 8px;">
                <small class="text-muted">Format: JPG/PNG, Maksimal ukuran: 2MB.</small>
            </div>

            <hr class="mb-4 border-secondary opacity-25">
            <div class="d-flex justify-content-between align-items-end mb-3">
                <h6 class="fw-bold text-dark mb-0"><i class="fas fa-list-ol text-primary me-2"></i>Pilihan Jawaban</h6>
            </div>
            
            <div class="alert alert-info py-2.5 px-3 border-0 rounded-3 small mb-4" style="background-color: #eff6ff; color: #1e40af;">
                <i class="fas fa-info-circle me-1"></i> <b>Tips:</b> Jika pilihan jawaban berupa gambar, Anda cukup mengetikkan <b>nama file gambarnya beserta ekstensi</b> (contoh: <code>grafik_a.png</code>) di kolom opsi. Pastikan file gambar tersebut telah ter-upload di folder server.
            </div>
            
            <div class="row bg-light p-3 rounded-3 mb-4 border" style="border-color: #e2e8f0 !important;">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-dark small">Opsi A <span class="text-danger">*</span></label>
                    <textarea name="opsi_a" class="form-control shadow-none" rows="2" required style="border-radius: 8px;"></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-dark small">Opsi B <span class="text-danger">*</span></label>
                    <textarea name="opsi_b" class="form-control shadow-none" rows="2" required style="border-radius: 8px;"></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-dark small">Opsi C <span class="text-danger">*</span></label>
                    <textarea name="opsi_c" class="form-control shadow-none" rows="2" required style="border-radius: 8px;"></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold text-dark small">Opsi D <span class="text-danger">*</span></label>
                    <textarea name="opsi_d" class="form-control shadow-none" rows="2" required style="border-radius: 8px;"></textarea>
                </div>
                <div class="col-md-12 mb-2">
                    <label class="form-label fw-bold text-dark small">Opsi E <span class="text-muted fw-normal">(Khusus SMK/SMA - Kosongkan jika tidak perlu)</span></label>
                    <textarea name="opsi_e" class="form-control shadow-none" rows="2" style="border-radius: 8px;"></textarea>
                </div>
            </div>

            <div class="mb-4 col-md-4">
                <label class="form-label fw-bold text-success small"><i class="fas fa-check-circle me-1"></i> Kunci Jawaban Benar <span class="text-danger">*</span></label>
                <select name="kunci_jawaban" class="form-select border-success text-success fw-bold shadow-none" required style="border-radius: 8px;">
                    <option value="">-- Pilih Kunci --</option>
                    <option value="A">Jawaban A</option>
                    <option value="B">Jawaban B</option>
                    <option value="C">Jawaban C</option>
                    <option value="D">Jawaban D</option>
                    <option value="E">Jawaban E</option>
                </select>
            </div>

            <div class="text-end pt-3 border-top">
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold shadow-sm"><i class="fas fa-save me-2"></i>Simpan Soal</button>
            </div>
        </form>
    </div>
</div>
@endsection