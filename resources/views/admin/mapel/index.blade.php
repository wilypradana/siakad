@extends('layouts.admin')

@section('title', 'Data Mata Pelajaran')

@section('content')
<!-- Tambahan CSS DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="mb-0 fw-bold">Manajemen Mata Pelajaran</h2>
        <p class="text-muted mb-0">Kelola daftar mata pelajaran, kelompok, dan alokasi jurusan.</p>
    </div>
    
    <div class="d-flex gap-2">
        <button class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahMapel">
            <i class="fas fa-plus me-1"></i> Tambah Mapel
        </button>
        
        <button class="btn btn-success shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalImportMapel">
            <i class="fas fa-file-excel me-1"></i> Import CSV
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" style="border-radius: 10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 w-100" id="tableMapel">
                <thead class="table-light">
                    <tr>
                        <th class="text-center py-3" width="5%">No</th>
                        <th class="py-3">Kode Mapel</th>
                        <th class="py-3">Nama Mata Pelajaran</th>
                        <th class="py-3">Kelompok</th>
                        <th class="py-3">Jurusan</th>
                        <th class="text-center py-3" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data_mapel as $index => $mapel)
                    <tr>
                        <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                        <td><span class="fw-bold text-primary">{{ $mapel->kode_mapel }}</span></td>
                        <td class="fw-semibold">{{ $mapel->nama_mapel }}</td>
                        <td>
                            @if(strtolower($mapel->kelompok) == 'umum')
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary">UMUM</span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning">KEJURUAN</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $mapel->jurusan_mapel ?? 'UMUM' }}</span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm shadow-sm">
                                <button class="btn btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#modalEditMapel{{ $mapel->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapusMapel{{ $mapel->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Modal Edit Mapel -->
                    <div class="modal fade" id="modalEditMapel{{ $mapel->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                                <div class="modal-header bg-warning border-0">
                                    <h5 class="modal-title text-dark fw-bold"><i class="fas fa-edit me-2"></i>Edit Mapel</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.mapel.update', $mapel->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="fw-bold mb-1">Kode Mapel</label>
                                            <input type="text" name="kode_mapel" class="form-control" value="{{ $mapel->kode_mapel }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="fw-bold mb-1">Nama Mata Pelajaran</label>
                                            <input type="text" name="nama_mapel" class="form-control" value="{{ $mapel->nama_mapel }}" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <label class="fw-bold mb-1">Kelompok Mapel</label>
                                                <select name="kelompok" class="form-select" required>
                                                    <option value="Umum" {{ strtolower($mapel->kelompok) == 'umum' ? 'selected' : '' }}>Umum</option>
                                                    <option value="Kejuruan" {{ strtolower($mapel->kelompok) == 'kejuruan' ? 'selected' : '' }}>Kejuruan / Pilihan</option>
                                                </select>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <label class="fw-bold mb-1">Kategori Jurusan</label>
                                                <input type="text" name="jurusan_mapel" class="form-control text-uppercase" value="{{ $mapel->jurusan_mapel ?? 'UMUM' }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light border-0">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning text-dark fw-bold">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Hapus Mapel -->
                    <div class="modal fade" id="modalHapusMapel{{ $mapel->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-danger text-white border-0">
                                    <h5 class="modal-title fw-bold">Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4 text-center">
                                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                    <p class="mb-0">Yakin ingin menghapus Mapel <b>{{ $mapel->nama_mapel }}</b>?</p>
                                </div>
                                <div class="modal-footer bg-light border-0 justify-content-center">
                                    <form action="{{ route('admin.mapel.destroy', $mapel->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger fw-bold">Ya, Hapus!</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Mapel -->
<div class="modal fade" id="modalTambahMapel" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Mata Pelajaran</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.mapel.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold mb-1">Kode Mapel</label>
                        <input type="text" name="kode_mapel" class="form-control" placeholder="Contoh: MPK-99" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold mb-1">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" class="form-control" placeholder="Contoh: Pemrograman Web" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="fw-bold mb-1">Kelompok Mapel</label>
                            <select name="kelompok" class="form-select" required>
                                <option value="Umum">Umum</option>
                                <option value="Kejuruan">Kejuruan / Pilihan</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold mb-1">Kategori Jurusan</label>
                            <input type="text" name="jurusan_mapel" class="form-control text-uppercase" placeholder="Contoh: TKJ, AKL, UMUM" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan Mapel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import Mapel -->
<div class="modal fade" id="modalImportMapel" tabindex="-1">
    <!-- (Biarkan kode modal import seperti aslinya atau sesuaikan desainnya, tidak perlu diubah logikanya) -->
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-file-excel me-2"></i>Import Mapel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('mapel.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info small rounded-3">
                        <strong>Aturan File:</strong><br>
                        1. Gunakan file Excel dan Save As menjadi format <strong>CSV (Comma Delimited)</strong>.<br>
                        2. Kolom A = Nama Mata Pelajaran.
                    </div>
                    <label class="fw-bold mb-1">Pilih File CSV (.csv)</label>
                    <input type="file" name="file" class="form-control" accept=".csv" required>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="submit" class="btn btn-success fw-bold"><i class="fas fa-upload me-1"></i> Upload & Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tambahan Script DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tableMapel').DataTable({
        "language": {
            "search": "Cari Mapel:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "zeroRecords": "Data mapel tidak ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Menampilkan 0 data",
            "paginate": {
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        },
        "pageLength": 25,
        "columnDefs": [
            { "orderable": false, "targets": [0, 5] } 
        ]
    });
});
</script>
@endsection