@extends('layouts.admin')

@section('title', 'Manajemen Bank Soal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1 text-dark">
            <i class="fas fa-database me-2 text-primary"></i> Manajemen Soal CBT
        </h5>
        <p class="text-muted small mb-0">Ujian: <strong class="text-dark">{{ $ujian->mapel->nama_mapel }} ({{ $ujian->kelas->nama_kelas }})</strong></p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ auth()->user()->role == 'admin' ? route('admin.ujian.index') : route('ujian.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        <button type="button" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
            <i class="fas fa-file-excel me-1"></i> Import Excel
        </button>
        <a href="https://docs.google.com/spreadsheets/d/1vI3RD0maYkxr03JpzBd-HlAbkCE8xfSG/edit?usp=drive_link&ouid=114853339111635132849&rtpof=true&sd=true" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold shadow-sm" target="_blank">
            <i class="fas fa-download me-1"></i> Format Excel DOWNLOAD
        </a>
        <button type="button" class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#modalSalinSoal">
            <i class="fas fa-copy me-1"></i> Salin Soal
        </button>
        <a href="{{ route('admin.soal.create', $ujian->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah Soal Baru
        </a>
    </div>
</div>

{{-- MODAL SALIN SOAL --}}
<div class="modal fade" id="modalSalinSoal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 14px;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title text-dark fw-bold fs-6"><i class="fas fa-copy me-2 text-warning"></i>Salin Soal dari Kelas Lain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.soal.import', $ujian->id) }}" method="POST">
                @csrf
                <div class="modal-body small">
                    <div class="alert alert-info border-0 py-2 mb-3 rounded-3 small" style="background-color: #eff6ff; color: #1e40af;">
                        Fitur ini akan menyalin (menduplikasi) seluruh soal dari ujian kelas lain yang Anda pilih ke dalam ujian <b>{{ $ujian->kelas->nama_kelas }}</b> ini.
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold mb-2 text-dark">Pilih Ujian Sumber <span class="text-danger">*</span></label>
                        <select name="sumber_ujian_id" class="form-select form-select-sm" required style="border-radius: 8px;">
                            <option value="">-- Pilih Ujian Kelas Lain --</option>
                            @forelse($ujian_lain as $ul)
                                <option value="{{ $ul->id }}">
                                   {{ $ul->mapel->nama_mapel }} | Kelas {{ $ul->kelas->nama_kelas }} | {{ $ul->judul_ujian == '-' ? 'Sumatif' : $ul->judul_ujian }}
                                </option>
                            @empty
                                <option value="" disabled>Belum ada jadwal ujian lain untuk mapel ini.</option>
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm rounded-pill px-4 fw-semibold text-dark"><i class="fas fa-download me-1"></i> Mulai Salin</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL IMPORT EXCEL --}}
<div class="modal fade" id="modalImportExcel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 14px;">
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title fw-bold text-dark fs-6"><i class="fas fa-file-excel me-2 text-success"></i>Import Soal dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.soal.import_excel', $ujian->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body small">
                    <div class="alert alert-warning border-0 py-2 mb-3 rounded-3 small" style="background-color: #fffbeb; color: #92400e;">
                        <b>Perhatian:</b> Baris pertama pada file Excel Anda (Header) <b>WAJIB</b> bernama huruf kecil semua tanpa spasi persis seperti berikut: <br><br>
                        <code>pertanyaan | opsi_a | opsi_b | opsi_c | opsi_d | opsi_e | kunci_jawaban</code>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold mb-2 text-dark">Pilih File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file_excel" class="form-control form-control-sm" accept=".xlsx, .xls, .csv" required style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-4 fw-semibold"><i class="fas fa-upload me-1"></i> Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 p-3 small" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
    </div>
@endif

@php
    $formatOpsi = function($teks) {
        if (!$teks) return '';
        $teks = trim($teks);
        if (preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $teks)) {
            return '<br><img src="'.asset('uploads/soal/'.$teks).'" class="img-fluid rounded border shadow-sm mt-1" style="max-height: 60px; object-fit: contain;">';
        }
        return $teks;
    };
@endphp

<div class="card border shadow-sm rounded-3 mb-5" style="border-color: #e2e8f0 !important;">
    <div class="card-header bg-white pt-4 pb-3 border-bottom d-flex align-items-center justify-content-between">
        <h5 class="fw-bold text-dark mb-0 fs-6"><i class="fas fa-list-ol me-2 text-primary"></i> Daftar Soal Terdaftar</h5>
        <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill" style="border-color: #cbd5e1 !important;">
            Total: {{ count($ujian->soals ?? []) }} Soal
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                <thead class="table-light text-secondary" style="border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th width="5%" class="text-center py-3">No</th>
                        <th width="45%" class="py-3">Pertanyaan & Gambar</th>
                        <th width="35%" class="py-3">Opsi Jawaban & Kunci</th>
                        <th width="15%" class="text-center py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ujian->soals as $index => $soal)
                    <tr>
                        <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                        <td class="py-3">
                            @if($soal->gambar)
                                <img src="{{ asset('uploads/soal/' . $soal->gambar) }}" alt="Gambar" class="img-thumbnail rounded-2 mb-2 shadow-sm" style="max-height: 110px;">
                                <br>
                            @endif
                            <div class="text-dark" style="white-space: pre-line; line-height: 1.5;">{!! $soal->pertanyaan !!}</div>
                        </td>
                        <td class="py-3">
                            <ul class="list-unstyled mb-0 small">
                                <li class="{{ $soal->kunci_jawaban == 'A' ? 'text-success fw-bold' : 'text-secondary' }}">A. {!! $formatOpsi($soal->opsi_a) !!} {!! $soal->kunci_jawaban == 'A' ? '<i class="fas fa-check-circle ms-1"></i>' : '' !!}</li>
                                <li class="{{ $soal->kunci_jawaban == 'B' ? 'text-success fw-bold' : 'text-secondary' }}">B. {!! $formatOpsi($soal->opsi_b) !!} {!! $soal->kunci_jawaban == 'B' ? '<i class="fas fa-check-circle ms-1"></i>' : '' !!}</li>
                                <li class="{{ $soal->kunci_jawaban == 'C' ? 'text-success fw-bold' : 'text-secondary' }}">C. {!! $formatOpsi($soal->opsi_c) !!} {!! $soal->kunci_jawaban == 'C' ? '<i class="fas fa-check-circle ms-1"></i>' : '' !!}</li>
                                <li class="{{ $soal->kunci_jawaban == 'D' ? 'text-success fw-bold' : 'text-secondary' }}">D. {!! $formatOpsi($soal->opsi_d) !!} {!! $soal->kunci_jawaban == 'D' ? '<i class="fas fa-check-circle ms-1"></i>' : '' !!}</li>
                                @if($soal->opsi_e)
                                <li class="{{ $soal->kunci_jawaban == 'E' ? 'text-success fw-bold' : 'text-secondary' }}">E. {!! $formatOpsi($soal->opsi_e) !!} {!! $soal->kunci_jawaban == 'E' ? '<i class="fas fa-check-circle ms-1"></i>' : '' !!}</li>
                                @endif
                            </ul>
                        </td>
                        <td class="text-center py-3">
                            <form action="{{ route('admin.soal.destroy', [$ujian->id, $soal->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin melepaskan soal ini dari ujian?');" class="m-0">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('admin.soal.edit', $soal->id) }}" class="btn btn-sm btn-outline-warning px-2.5 py-1" title="Edit Soal">
                                        <i class="fas fa-edit"></i> 
                                    </a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2.5 py-1" title="Hapus Soal"><i class="fas fa-trash"></i></button>
                                </div>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted small">
                            <i class="fas fa-box-open fa-3x mb-3 text-primary opacity-25 d-block"></i>
                            <h6 class="text-dark fw-bold">Belum ada soal</h6>
                            <p class="mb-0">Silakan klik "Tambah Soal Baru" untuk mulai membuat bank soal CBT ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover td { background-color: #f8fafc; }
</style>
@endsection