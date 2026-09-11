@extends('layouts.admin')
@section('title', 'Manajemen Kelas')

@section('content')
<!-- Tambahan CSS DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<!-- Header & Tombol Tambah -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h2 class="mb-0 fw-bold">Manajemen Kelas</h2>
        <p class="text-muted mb-0">Kelola ruang kelas, program keahlian, dan wali kelas.</p>
    </div>
    <button type="button" class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahKelas">
        <i class="fas fa-plus me-1"></i> Tambah Kelas
    </button>
</div>

<!-- Alert Sukses/Error -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 10px;">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- List Kelas (Table Format) -->
<div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
    <div class="card-body p-4">
        <div class="table-responsive">
            <!-- Tambahan ID tableKelas dan w-100 -->
            <table class="table table-hover align-middle mb-0 w-100" id="tableKelas">
                <thead class="table-light">
                    <tr>
                        <th class="text-center py-3" width="5%">No</th>
                        <th class="py-3">Nama Kelas</th>
                        <th class="py-3">Jurusan</th>
                        <th class="py-3">Wali Kelas</th>
                        <th class="py-3">Periode</th>
                        <th class="text-center py-3">Siswa</th>
                        <th class="text-center py-3" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data_kelas as $index => $kelas)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <span class="fw-bold text-primary fs-6">{{ $kelas->nama_kelas }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $kelas->jurusan ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-tie text-secondary me-2"></i>
                                    <span class="fw-semibold">{{ $kelas->guru->nama ?? 'Belum Diatur' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">Tingkat {{ $kelas->tingkat ?? '-' }} (Smt {{ $kelas->semester ?? '-' }})</div>
                                <small class="text-muted">{{ $kelas->tahun_ajaran ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary rounded-pill px-3">{{ $kelas->siswa_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm shadow-sm">
                                    <a href="{{ route('admin.kelas.show', $kelas->id) }}" class="btn btn-info text-white" title="Lihat Siswa">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#editKelasModal{{ $kelas->id }}" title="Edit Kelas">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.kelas.destroy', $kelas->id) }}" method="POST" class="d-inline m-0" onsubmit="return confirm('Yakin ingin menghapus kelas ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Hapus Kelas" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit Kelas -->
                        <div class="modal fade" id="editKelasModal{{ $kelas->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow" style="border-radius: 12px;">
                                    <div class="modal-header bg-warning border-0">
                                        <h5 class="modal-title text-dark fw-bold"><i class="fas fa-edit me-2"></i>Edit Data Rombel</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.kelas.update', $kelas->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <label class="fw-bold mb-1">Nama Kelas / Rombel</label>
                                                <input type="text" name="nama_kelas" class="form-control text-uppercase" value="{{ $kelas->nama_kelas }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fw-bold mb-1">Program Keahlian (Jurusan)</label>
                                                <input type="text" name="jurusan" class="form-control text-uppercase" value="{{ $kelas->jurusan }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fw-bold mb-1">Pilih Wali Kelas (Opsional)</label>
                                                <select name="guru_id" class="form-select">
                                                    <option value="">-- Belum Ada Wali Kelas --</option>
                                                    @foreach($data_guru as $guru)
                                                        <option value="{{ $guru->id }}" {{ $kelas->guru_id == $guru->id ? 'selected' : '' }}>
                                                            {{ $guru->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-6">
                                                    <label class="fw-bold mb-1">Tingkatan</label>
                                                    <select name="tingkat" class="form-select" required>
                                                        <option value="">-- Pilih --</option>
                                                        <option value="10" {{ $kelas->tingkat == '10' ? 'selected' : '' }}>Tingkat 10 (Fase E)</option>
                                                        <option value="11" {{ $kelas->tingkat == '11' ? 'selected' : '' }}>Tingkat 11 (Fase F)</option>
                                                        <option value="12" {{ $kelas->tingkat == '12' ? 'selected' : '' }}>Tingkat 12 (Fase F)</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="fw-bold mb-1">Semester</label>
                                                    <select name="semester" class="form-select" required>
                                                        <option value="">-- Pilih --</option>
                                                        <option value="Ganjil" {{ $kelas->semester == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                                        <option value="Genap" {{ $kelas->semester == 'Genap' ? 'selected' : '' }}>Genap</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fw-bold mb-1">Tahun Pelajaran</label>
                                                <select name="tahun_ajaran" class="form-select" required>
                                                    <option value="2025/2026" {{ $kelas->tahun_ajaran == '2025/2026' ? 'selected' : '' }}>2025/2026</option>
                                                    <option value="2026/2027" {{ $kelas->tahun_ajaran == '2026/2027' ? 'selected' : '' }}>2026/2027</option>
                                                    <option value="2027/2028" {{ $kelas->tahun_ajaran == '2027/2028' ? 'selected' : '' }}>2027/2028</option>
                                                </select>
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
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-school fa-3x text-muted mb-3 opacity-50"></i>
                                <h5 class="text-muted fw-bold">Belum Ada Data Kelas</h5>
                                <p class="text-muted mb-0">Silakan klik tombol "Tambah Kelas" untuk membuat rombongan belajar baru.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="modalTambahKelas" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i>Tambah Kelas Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kelas.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold mb-1">Nama Kelas / Rombel</label>
                        <input type="text" name="nama_kelas" class="form-control text-uppercase" placeholder="Contoh: 10 TKJ 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold mb-1">Program Keahlian (Jurusan)</label>
                        <input type="text" name="jurusan" class="form-control text-uppercase" placeholder="Contoh: TEKNIK KOMPUTER DAN JARINGAN" required>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold mb-1">Pilih Wali Kelas (Opsional)</label>
                        <select name="guru_id" class="form-select">
                            <option value="">-- Belum Ada Wali Kelas --</option>
                            @foreach($data_guru as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="fw-bold mb-1">Tingkatan</label>
                            <select name="tingkat" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="10">Tingkat 10 (Fase E)</option>
                                <option value="11">Tingkat 11 (Fase F)</option>
                                <option value="12">Tingkat 12 (Fase F)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="fw-bold mb-1">Semester</label>
                            <select name="semester" class="form-select" required>
                                <option value="">-- Pilih --</option>
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold mb-1">Tahun Pelajaran</label>
                        <select name="tahun_ajaran" class="form-select" required>
                            <option value="2026/2027" selected>2026/2027</option>
                            <option value="2027/2028">2027/2028</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold">Simpan Kelas</button>
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
    $('#tableKelas').DataTable({
        "language": {
            "search": "Cari Kelas/Wali:",
            "lengthMenu": "Tampilkan _MENU_ data",
            "zeroRecords": "Data kelas tidak ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Menampilkan 0 data",
            "paginate": {
                "next": "Berikutnya",
                "previous": "Sebelumnya"
            }
        },
        "pageLength": 10,
        "columnDefs": [
            { "orderable": false, "targets": [0, 6] } // Menonaktifkan panah sorting pada kolom No (0) dan Aksi (6)
        ]
    });
});
</script>
@endsection