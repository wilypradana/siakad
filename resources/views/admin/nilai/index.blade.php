@extends('layouts.admin')

@section('title', 'Manajemen Nilai Kurikulum Merdeka')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manajemen Nilai</h2>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahNilai">
        <i class="fas fa-plus me-1"></i> Input Nilai Baru
    </button>
</div>

<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Mata Pelajaran</th>
                        <th>Jenis Ujian</th>
                        <th class="text-center">S1</th>
                        <th class="text-center">S2</th>
                        <th class="text-center">S3</th>
                        <th class="text-center">Nilai Ujian</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data_nilai as $index => $nilai)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $nilai->siswa->nama ?? '-' }}</td>
                        <td>{{ $nilai->mapel->nama_mapel ?? '-' }}</td>
                        <td><span class="badge bg-info text-dark">{{ $nilai->jenisUjian->nama_jenis ?? '-' }}</span></td>
                        <td class="text-center">{{ $nilai->sumatif_1 }}</td>
                        <td class="text-center">{{ $nilai->sumatif_2 }}</td>
                        <td class="text-center">{{ $nilai->sumatif_3 }}</td>
                        <td class="text-center bg-light">{{ $nilai->nilai_ujian }}</td>
                        <td class="text-center fw-bold text-primary fs-5">{{ $nilai->nilai_akhir }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalEditNilai{{ $nilai->id }}"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('nilai.destroy', $nilai->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data nilai ini?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEditNilai{{ $nilai->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content text-start">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title text-dark"><i class="fas fa-edit me-2"></i>Edit Nilai</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('nilai.update', $nilai->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body row g-3">
                                        <div class="col-md-12 mb-2">
                                            <label>Jenis Ujian/Rapor <span class="text-danger">*</span></label>
                                            <select name="jenis_ujian_id" class="form-select" required>
                                                @foreach($data_jenis as $jenis)
                                                    <option value="{{ $jenis->id }}" {{ $nilai->jenis_ujian_id == $jenis->id ? 'selected' : '' }}>{{ $jenis->nama_jenis }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label>Pilih Siswa</label>
                                            <select name="siswa_id" class="form-select" required>
                                                @foreach($data_siswa as $siswa)
                                                    <option value="{{ $siswa->id }}" {{ $nilai->siswa_id == $siswa->id ? 'selected' : '' }}>{{ $siswa->nis }} - {{ $siswa->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Pilih Mata Pelajaran</label>
                                            <select name="mapel_id" class="form-select" required>
                                                @foreach($data_mapel as $mapel)
                                                    <option value="{{ $mapel->id }}" {{ $nilai->mapel_id == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12"><hr class="my-1"></div>
                                        <div class="col-md-3">
                                            <label>Sumatif 1</label>
                                            <input type="number" name="sumatif_1" class="form-control text-center" value="{{ $nilai->sumatif_1 }}" min="0" max="100">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Sumatif 2</label>
                                            <input type="number" name="sumatif_2" class="form-control text-center" value="{{ $nilai->sumatif_2 }}" min="0" max="100">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Sumatif 3</label>
                                            <input type="number" name="sumatif_3" class="form-control text-center" value="{{ $nilai->sumatif_3 }}" min="0" max="100">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Nilai Ujian</label>
                                            <input type="number" name="nilai_ujian" class="form-control text-center fw-bold bg-light" value="{{ $nilai->nilai_ujian }}" min="0" max="100">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-warning text-dark fw-bold">Update Nilai</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahNilai" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Input Rekap Nilai Siswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('nilai.store') }}" method="POST">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-md-12 mb-2">
                        <label>Jenis Ujian/Rapor <span class="text-danger">*</span></label>
                        <select name="jenis_ujian_id" class="form-select" required>
                            <option value="">-- Pilih Jenis Ujian --</option>
                            @foreach($data_jenis as $jenis)
                                <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Pilih Siswa</label>
                        <select name="siswa_id" class="form-select" required>
                            <option value="">-- Cari Nama Siswa --</option>
                            @foreach($data_siswa as $siswa)
                                <option value="{{ $siswa->id }}">{{ $siswa->nis }} - {{ $siswa->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Pilih Mata Pelajaran</label>
                        <select name="mapel_id" class="form-select" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($data_mapel as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12"><hr class="my-1"></div>
                    <p class="text-muted small mb-0"><i class="fas fa-info-circle"></i> Masukkan rentang nilai (0-100). Sistem akan menghitung Nilai Akhir otomatis.</p>
                    <div class="col-md-3">
                        <label>Sumatif 1</label>
                        <input type="number" name="sumatif_1" class="form-control text-center" value="0" min="0" max="100">
                    </div>
                    <div class="col-md-3">
                        <label>Sumatif 2</label>
                        <input type="number" name="sumatif_2" class="form-control text-center" value="0" min="0" max="100">
                    </div>
                    <div class="col-md-3">
                        <label>Sumatif 3</label>
                        <input type="number" name="sumatif_3" class="form-control text-center" value="0" min="0" max="100">
                    </div>
                    <div class="col-md-3">
                        <label>Nilai Ujian</label>
                        <input type="number" name="nilai_ujian" class="form-control text-center fw-bold bg-light" value="0" min="0" max="100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan & Hitung Nilai</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection