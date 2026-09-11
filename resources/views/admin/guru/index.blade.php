@extends('layouts.admin')

@section('title', 'Data Guru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Manajemen Data Guru</h2>
    <div class="d-flex gap-2">
        <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportGuru">
            <i class="fas fa-file-excel me-1"></i> Import Guru
        </button>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahGuru">
            <i class="fas fa-plus me-1"></i> Tambah Guru
        </button>
        <a href="{{ route('admin.guru.sync_password') }}" class="btn btn-warning shadow-sm fw-bold" onclick="return confirm('Yakin ingin mereset dan menyinkronkan password seluruh guru menjadi NIP masing-masing?')">
            <i class="fas fa-sync-alt me-1"></i> Sync Semua Password ke NIP
        </a>
        <a href="{{ route('admin.guru.cetak_kartu_massal') }}" target="_blank" class="btn btn-info text-white shadow-sm">
            <i class="fas id-card me-1"></i> Cetak Semua Kartu Guru
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success fw-bold">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <strong>Error Sistem:</strong> {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-warning">
        <strong>Peringatan! Data ditolak karena:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-body">
        <table class="table table-bordered table-hover align-middle bg-white w-100" id="tableGuru">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>NIP (Username)</th>
                    <th>Nama Lengkap</th>
                    <th>No. HP</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data_guru as $index => $guru)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $guru->nip }}</td>
                    <td>{{ $guru->nama }}</td>
                    <td>{{ $guru->no_hp ?? '-' }}</td>
                    <td><span class="badge bg-success">Aktif</span></td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditGuru{{ $guru->id }}">Edit</button>
                        <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalEditPassword{{ $guru->id }}" title="Ubah Password">
                            <i class="fas fa-key"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalHapusGuru{{ $guru->id }}">Hapus</button>
                        <a href="{{ route('admin.guru.cetak_kartu', $guru->id) }}" target="_blank" class="btn btn-sm btn-secondary" title="Cetak Kartu Akun">
                            <i class="fas fa-id-card"></i>
                        </a>
                    </td>
                </tr>

                <div class="modal fade" id="modalEditGuru{{ $guru->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">Edit Data Guru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="/admin/guru/{{ $guru->id }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label>NIP (Username tidak bisa diubah)</label>
                                        <input type="text" class="form-control" value="{{ $guru->nip }}" readonly disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label>Nama Lengkap</label>
                                        <input type="text" name="nama" class="form-control" value="{{ $guru->nama }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>No. Handphone</label>
                                        <input type="text" name="no_hp" class="form-control" value="{{ $guru->no_hp }}">
                                    </div>
                                    <div class="mb-3">
                                        <label>Alamat</label>
                                        <textarea name="alamat" class="form-control" rows="2">{{ $guru->alamat }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalHapusGuru{{ $guru->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">Konfirmasi Hapus</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                Apakah Anda yakin ingin menghapus data guru <b>{{ $guru->nama }}</b>? 
                                <br><small class="text-danger">Tindakan ini juga akan menghapus akun login guru tersebut secara permanen.</small>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <form action="/admin/guru/{{ $guru->id }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Ya, Hapus!</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                

                <!-- Modal Edit Password -->
                <div class="modal fade" id="modalEditPassword{{ $guru->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content text-start">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title"><i class="fas fa-key me-2"></i>Ubah Password Login</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('guru.update_password', $guru->id) ?? url('/admin/guru/'.$guru->id.'/password') }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="alert alert-warning small">
                                        Mengubah password untuk <b>{{ $guru->nama }}</b>. Berikan password baru ini secara rahasia kepada guru yang bersangkutan.
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Password Baru</label>
                                        <input type="text" name="password" class="form-control" placeholder="Masukkan minimal 6 karakter..." required minlength="6">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-info text-white fw-bold"><i class="fas fa-save me-1"></i> Simpan Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
                
                @if($data_guru->isEmpty())
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada data guru.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="modalTambahGuru" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Data Guru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="/admin/guru" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <small>Akun login akan otomatis dibuat. <br><b>Username:</b> (Sama dengan NIP), <b>Password default:</b> (Sama dengan NIP)</small>
                    </div>
                    <div class="mb-3">
                        <label>NIP</label>
                        <input type="text" name="nip" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>No. Handphone</label>
                        <input type="text" name="no_hp" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL IMPORT GURU -->
<div class="modal fade" id="modalImportGuru" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel me-2"></i>Import Data Guru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
                <form action="{{ route('admin.guru.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info small">
                        Pastikan kolom baris pertama (Header) Excel bernilai persis: <br>
                        <strong>nip</strong>, <strong>nama</strong>, <strong>no_hp</strong>, <strong>alamat</strong>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Pilih File Excel / CSV:</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-upload me-1"></i> Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tableGuru').DataTable({
            "language": {
                "lengthMenu": "Tampilkan _MENU_ entri",
                "zeroRecords": "Data guru tidak ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
                "infoFiltered": "(difilter dari _MAX_ total entri)",
                "search": "Cari Guru:",
                "paginate": {
                    "next": "Berikutnya",
                    "previous": "Sebelumnya"
                }
            },
            "pageLength": 10 // Jumlah data yang tampil per halaman
        });
    });
</script>


@endsection