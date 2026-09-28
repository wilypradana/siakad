@extends('layouts.admin')

@section('title', 'Manajemen Bank Soal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Manajemen Soal CBT</h2>
        <p class="text-muted mb-0">Ujian: <strong>{{ $ujian->mapel->nama_mapel }} ({{ $ujian->kelas->nama_kelas }})</strong></p>
    </div>
    <div>
        <a href="{{ auth()->user()->role == 'admin' ? route('admin.ujian.index') : route('ujian.index') }}" class="btn btn-secondary shadow-sm me-2">
        <i class="fas fa-arrow-left me-1"></i> Kembali</a>
       <button type="button" class="btn btn-success shadow-sm fw-bold me-2" data-bs-toggle="modal" data-bs-target="#modalImportExcel">
            <i class="fas fa-file-excel me-1"></i> Import Excel
        </button>
               <a href="https://docs.google.com/spreadsheets/d/1vI3RD0maYkxr03JpzBd-HlAbkCE8xfSG/edit?usp=drive_link&ouid=114853339111635132849&rtpof=true&sd=true" class="btn btn-success shadow-sm fw-bold me-2" >
            <i class="fas fa-file-excel me-1"></i> format Excel DOWNLOAD
</a>
       <button type="button" class="btn btn-warning shadow-sm fw-bold me-2" data-bs-toggle="modal" data-bs-target="#modalSalinSoal">
            <i class="fas fa-copy me-1"></i> Salin Soal
        </button>
        <a href="{{ route('admin.soal.create', $ujian->id) }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-1"></i> Tambah Soal Baru</a>
    </div>
</div>

<div class="modal fade" id="modalSalinSoal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark"><i class="fas fa-copy me-2"></i>Salin Soal dari Kelas Lain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.soal.import', $ujian->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info small">
                        Fitur ini akan menyalin (menduplikasi) seluruh soal dari ujian kelas lain yang Anda pilih ke dalam ujian <b>{{ $ujian->kelas->nama_kelas }}</b> ini.
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold mb-2">Pilih Ujian Sumber <span class="text-danger">*</span></label>
                        <select name="sumber_ujian_id" class="form-select" required>
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
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold"><i class="fas fa-download me-1"></i> Mulai Salin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL IMPORT EXCEL -->
<div class="modal fade" id="modalImportExcel" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel me-2"></i>Import Soal dari Excel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.soal.import_excel', $ujian->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning small">
                        <b>Perhatian:</b> Baris pertama pada file Excel Anda (Header) <b>WAJIB</b> bernama huruf kecil semua tanpa spasi persis seperti berikut: <br><br>
                        <code>pertanyaan | opsi_a | opsi_b | opsi_c | opsi_d | opsi_e | kunci_jawaban</code>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold mb-2">Pilih File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success fw-bold"><i class="fas fa-upload me-1"></i> Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>
@if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle me-1"></i> {{ session('success') }}</div>
@endif

<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
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
                        <td class="text-center fw-bold">{{ $index + 1 }}</td>
                        <td>
                            @if($soal->gambar)
                                <img src="{{ asset('uploads/soal/' . $soal->gambar) }}" alt="Gambar" class="img-thumbnail mb-2" style="max-height: 120px;">
                                <br>
                            @endif
                            <div style="white-space: pre-line;">{!! $soal->pertanyaan !!}</div>
                        </td>
                        <td>
                            <ul class="list-unstyled mb-0 small">
                                <li class="{{ $soal->kunci_jawaban == 'A' ? 'text-success fw-bold' : '' }}">A. {{ $soal->opsi_a }} {!! $soal->kunci_jawaban == 'A' ? '<i class="fas fa-check-circle"></i>' : '' !!}</li>
                                <li class="{{ $soal->kunci_jawaban == 'B' ? 'text-success fw-bold' : '' }}">B. {{ $soal->opsi_b }} {!! $soal->kunci_jawaban == 'B' ? '<i class="fas fa-check-circle"></i>' : '' !!}</li>
                                <li class="{{ $soal->kunci_jawaban == 'C' ? 'text-success fw-bold' : '' }}">C. {{ $soal->opsi_c }} {!! $soal->kunci_jawaban == 'C' ? '<i class="fas fa-check-circle"></i>' : '' !!}</li>
                                <li class="{{ $soal->kunci_jawaban == 'D' ? 'text-success fw-bold' : '' }}">D. {{ $soal->opsi_d }} {!! $soal->kunci_jawaban == 'D' ? '<i class="fas fa-check-circle"></i>' : '' !!}</li>
                                @if($soal->opsi_e)
                                <li class="{{ $soal->kunci_jawaban == 'E' ? 'text-success fw-bold' : '' }}">E. {{ $soal->opsi_e }} {!! $soal->kunci_jawaban == 'E' ? '<i class="fas fa-check-circle"></i>' : '' !!}</li>
                                @endif
                            </ul>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.soal.destroy', [$ujian->id, $soal->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin melepaskan soal ini dari ujian?');">
                                <a href="{{ route('admin.soal.edit', $soal->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                            <h5 class="text-dark">Belum ada soal</h5>
                            <p class="mb-0">Silakan klik "Tambah Soal Baru" untuk mulai membuat bank soal CBT ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection