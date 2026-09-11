@extends('layouts.admin')

@section('title', 'Manajemen Data Siswa')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0 text-dark fw-bold">Manajemen Data Siswa</h2>
    
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-danger shadow-sm d-none" id="btnHapusMassal" onclick="hapusMassal()">
            <i class="fas fa-trash-alt me-1"></i> Hapus Terpilih (<span id="jumlahCeklis">0</span>)
        </button>
        
        <button type="button" class="btn btn-success shadow-sm d-none" id="btnLunasMassal" onclick="lunasMassal()">
            <i class="fas fa-money-check-alt me-1"></i> Tandai Lunas (<span id="jumlahCeklisLunas">0</span>)
        </button>
        
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-print"></i> Cetak Kartu
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="prosesCetakKartu('semua')">Cetak Semua Terpilih</a></li>
                <li><a class="dropdown-item" href="#" onclick="prosesCetakKartu('lunas')">Cetak Lunas Saja</a></li>
            </ul>
        </div>

        <a href="{{ route('siswa.export') }}" class="btn btn-info text-white shadow-sm">
            <i class="fas fa-file-download me-1"></i> Export Data
        </a>

        <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportSiswa">
            <i class="fas fa-file-excel me-1"></i> Import CSV
        </button>

        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
            <i class="fas fa-plus me-1"></i> Tambah Siswa
        </button>
        <form action="{{ route('siswa.sync_password') }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin mereset seluruh password siswa menjadi NIS mereka masing-masing?')">
            @csrf
            <button type="submit" class="btn btn-warning shadow-sm text-dark fw-bold">
                <i class="fas fa-key me-1"></i> Sync Password = NIS
            </button>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <form action="{{ route('admin.siswa.index') }}" method="GET" class="d-flex gap-2">
            <select name="filter_kelas" class="form-select shadow-sm" onchange="this.form.submit()">
                <option value="">-- Tampilkan Semua Kelas --</option>
                @foreach($data_kelas as $kelas)
                    <option value="{{ $kelas->id }}" {{ request('filter_kelas') == $kelas->id ? 'selected' : '' }}>
                        Kelas {{ $kelas->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li><i class="fas fa-exclamation-circle me-1"></i> {{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle bg-white w-100" id="tableSiswa">
                <thead class="table-light text-secondary">
                    <tr>
                        <th width="5%"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                        <th>NIS</th>
                        <th>Nama Lengkap</th>
                        <th>Kelas</th>
                        <th>Status Pembayaran</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data_siswa as $siswa)
                    <tr>
                        <td><input type="checkbox" class="check-item form-check-input" value="{{ $siswa->id }}"></td>
                        <td class="fw-bold">{{ $siswa->nis }}</td>
                        <td class="text-uppercase">{{ $siswa->nama }}</td>
                        <td><span class="badge bg-primary px-3 py-2">{{ $siswa->kelas->nama_kelas ?? 'N/A' }}</span></td>
                        <td>
                            @if($siswa->status_bayar == 1)
                                {{-- Menggunakan d-flex align-items-center agar sejajar secara horizontal --}}
                                <div class="d-flex align-items-center gap-2">
                                    <div>
                                        <span class="badge bg-success py-2 px-3 shadow-sm">
                                            <i class="fas fa-check-circle me-1"></i> LUNAS
                                        </span>
                                        <br>
                                        <small class="text-primary fw-bold" style="letter-spacing: 1px; font-size: 10px;">
                                            ID: {{ $siswa->nomor_kartu }}
                                        </small>
                                    </div>
                                    
                                    {{-- Tombol Batal Lunas sekarang ada di samping --}}
                                    <form action="{{ route('admin.siswa.batal_lunas', $siswa->id) }}" method="POST" onsubmit="return confirm('Batalkan pelunasan untuk {{ $siswa->nama }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm border-0" title="Batalkan Pelunasan" style="padding: 2px 5px;">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('admin.siswa.lunas', $siswa->id) }}" method="POST" onsubmit="return confirm('Konfirmasi pelunasan untuk {{ $siswa->nama }}?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger w-100 shadow-sm">
                                        <i class="fas fa-money-bill-wave me-1"></i> Tandai Lunas
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <button type="button" class="btn btn-warning btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalEditSiswa{{ $siswa->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('siswa.destroy', $siswa->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEditSiswa{{ $siswa->id }}" tabindex="-1" aria-labelledby="editLabel{{ $siswa->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-user-edit me-2"></i>Edit Siswa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <!-- TAMBAHAN ENCTYPE SANGAT PENTING UNTUK UPLOAD FOTO -->
                                <form action="{{ route('siswa.update', $siswa->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf @method('PUT')
                                    <div class="modal-body text-start">
                                        
                                        <div class="mb-3 text-dark">
                                            <label class="fw-bold mb-1">NIS (Nomor Induk Siswa)</label>
                                            <input type="text" class="form-control bg-light" value="{{ $siswa->nis }}" readonly>
                                            <small class="text-danger" style="font-size: 11px;">*NIS digunakan untuk login dan tidak dapat diubah</small>
                                        </div>

                                        <div class="mb-3 text-dark">
                                            <label class="fw-bold mb-1">Nama Lengkap</label>
                                            <input type="text" name="nama" class="form-control text-uppercase" value="{{ $siswa->nama }}" required>
                                        </div>

                                        <div class="mb-3 text-dark">
                                            <label class="fw-bold mb-1">Kelas</label>
                                            <select name="kelas_id" class="form-select" required>
                                                @foreach($data_kelas as $kelas)
                                                    <option value="{{ $kelas->id }}" {{ $siswa->kelas_id == $kelas->id ? 'selected' : '' }}>
                                                        {{ $kelas->nama_kelas }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="row g-2 mb-3 text-dark">
                                            <div class="col-6">
                                                <label class="fw-bold mb-1">Nomor HP</label>
                                                <input type="text" name="no_hp" class="form-control" value="{{ $siswa->no_hp }}" placeholder="Contoh: 0812...">
                                            </div>
                                            <div class="col-6">
                                                <label class="fw-bold mb-1">Status Siswa</label>
                                                <select name="status" class="form-select">
                                                    <option value="aktif" {{ $siswa->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                                    <option value="lulus" {{ $siswa->status == 'lulus' ? 'selected' : '' }}>Lulus</option>
                                                    <option value="pindah" {{ $siswa->status == 'pindah' ? 'selected' : '' }}>Pindah / Keluar</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3 text-dark">
                                            <label class="fw-bold mb-1">Nama Orang Tua / Wali</label>
                                            <input type="text" name="nama_ortu" class="form-control text-uppercase" value="{{ $siswa->nama_ortu }}" placeholder="Nama Ayah/Ibu">
                                        </div>

                                        <div class="mb-3 text-dark">
                                            <label class="fw-bold mb-1">Alamat Lengkap</label>
                                            <textarea name="alamat" class="form-control" rows="2" placeholder="Masukkan alamat lengkap">{{ $siswa->alamat }}</textarea>
                                        </div>

                                        <div class="mb-3 text-dark">
                                            <label class="fw-bold mb-1">Foto Profile (Opsional)</label>
                                            <input type="file" name="foto" class="form-control" accept="image/png, image/jpeg, image/jpg">
                                            @if($siswa->foto)
                                                <small class="text-success mt-1 d-block"><i class="fas fa-check-circle me-1"></i>Siswa ini sudah memiliki foto. Upload file baru jika ingin mengganti.</small>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning text-dark fw-bold px-4">Simpan Perubahan</button>
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

<div class="modal fade" id="modalTambahSiswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>Tambah Siswa Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- TAMBAHAN ENCTYPE AGAR BISA UPLOAD FOTO -->
            <form action="{{ route('siswa.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body text-start p-4" style="max-height: 70vh; overflow-y: auto;">
                    
                    <div class="mb-3 text-dark">
                        <label class="fw-bold mb-1">NIS (Nomor Induk Siswa) <span class="text-danger">*</span></label>
                        <input type="number" name="nis" class="form-control bg-light" required placeholder="Masukkan NIS unik">
                        <small class="text-primary mt-1 d-block" style="font-size: 11px;">
                            <i class="fas fa-info-circle me-1"></i>NIS otomatis menjadi Username & Password login siswa.
                        </small>
                    </div>

                    <div class="mb-3 text-dark">
                        <label class="fw-bold mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control text-uppercase" required placeholder="Sesuai Akta Kelahiran">
                    </div>

                    <div class="mb-3 text-dark">
                        <label class="fw-bold mb-1">Pilih Kelas <span class="text-danger">*</span></label>
                        <select name="kelas_id" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($data_kelas as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 text-dark">
                        <label class="fw-bold mb-1">Nomor HP</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 08123456789">
                    </div>

                    <div class="mb-3 text-dark">
                        <label class="fw-bold mb-1">Nama Orang Tua / Wali</label>
                        <input type="text" name="nama_ortu" class="form-control text-uppercase" placeholder="Nama Ayah / Ibu">
                    </div>

                    <div class="mb-3 text-dark">
                        <label class="fw-bold mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Masukkan alamat domisili"></textarea>
                    </div>

                    <div class="mb-3 text-dark">
                        <label class="fw-bold mb-1">Foto Profile (Opsional)</label>
                        <input type="file" name="foto" class="form-control" accept="image/png, image/jpeg, image/jpg">
                        <small class="text-muted mt-1 d-block" style="font-size: 11px;">Format JPG/PNG. Maksimal 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImportSiswa" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Import Data (CSV)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('siswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3 text-dark text-start">
                        <label class="fw-bold">Pilih Kelas</label>
                        <select name="kelas_id" class="form-select" required>
                            @foreach($data_kelas as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 text-dark text-start">
                        <label class="fw-bold">File CSV</label>
                        <input type="file" name="file" class="form-control" required accept=".csv">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success fw-bold px-4">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<form id="formHapusMassal" action="{{ route('siswa.bulkDelete') }}" method="POST" style="display: none;">
    @csrf @method('DELETE')
    <div id="hiddenInputs"></div>
</form>

<!-- FORM BARU -->
<form id="formLunasMassal" action="{{ route('siswa.bulkLunas') }}" method="POST" style="display: none;">
    @csrf
    <div id="hiddenInputsLunas"></div>
</form>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // 1. Pastikan variabel table didefinisikan secara global agar bisa diakses fungsi lain
    window.tableSiswa = $('#tableSiswa').DataTable({
        "language": { "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json" },
        "columnDefs": [{ "orderable": false, "targets": [0, 5] }],
        
        // 1. Set default tampilan otomatis menjadi 40 baris
        "pageLength": 40, 
        
        // 2. (Opsional) Tambahkan angka 40 ke dalam pilihan dropdown "Show entries"
        "lengthMenu": [[10, 25, 40, 50, 100, -1], [10, 25, 40, 50, 100, "Semua"]]
    });

    // 2. Event listener untuk Checkbox All
    $('#checkAll').on('change', function() {
        var rows = window.tableSiswa.rows({ 'search': 'applied' }).nodes();
        $('input.check-item', rows).prop('checked', this.checked);
        toggleHapusButton();
    });

    // 3. Event listener untuk Checkbox satuan
    $('#tableSiswa tbody').on('change', 'input.check-item', function() {
        toggleHapusButton();
    });
});

// 4. Fungsi pengecekan tombol (PENTING: Gunakan window.tableSiswa)
function toggleHapusButton() {
    var checkedCount = window.tableSiswa.$('input.check-item:checked').length;
    
    // Update angka di badge tombol
    $('#jumlahCeklis').text(checkedCount);
    $('#jumlahCeklisPrint').text(checkedCount);
    
    // Munculkan atau sembunyikan tombol
    if (checkedCount > 0) { 
        $('#btnHapusMassal').removeClass('d-none'); 
        $('#btnCetakMassal').removeClass('d-none'); 
    } else { 
        $('#btnHapusMassal').addClass('d-none'); 
        $('#btnCetakMassal').addClass('d-none'); 
    }
}

// 4. Fungsi pengecekan tombol
function toggleHapusButton() {
    var checkedCount = window.tableSiswa.$('input.check-item:checked').length;
    
    // Update angka di badge tombol
    $('#jumlahCeklis').text(checkedCount);
    $('#jumlahCeklisPrint').text(checkedCount);
    $('#jumlahCeklisLunas').text(checkedCount); // UPDATE INI
    
    // Munculkan atau sembunyikan tombol
    if (checkedCount > 0) { 
        $('#btnHapusMassal').removeClass('d-none'); 
        $('#btnCetakMassal').removeClass('d-none'); 
        $('#btnLunasMassal').removeClass('d-none'); // UPDATE INI
    } else { 
        $('#btnHapusMassal').addClass('d-none'); 
        $('#btnCetakMassal').addClass('d-none'); 
        $('#btnLunasMassal').addClass('d-none'); // UPDATE INI
    }
}

// ... fungsi cetakMassal dan hapusMassal yang sudah ada ...

// 7. FUNGSI BARU: Lunas Massal
function lunasMassal() {
    var checkedInputs = window.tableSiswa.$('input.check-item:checked');
    if (checkedInputs.length > 0) {
        if (confirm('Yakin ingin menandai LUNAS pada ' + checkedInputs.length + ' siswa terpilih?')) {
            var hiddenInputs = $('#hiddenInputsLunas');
            hiddenInputs.empty(); 
            
            checkedInputs.each(function() {
                hiddenInputs.append('<input type="hidden" name="ids[]" value="' + $(this).val() + '">');
            });
            
            $('#formLunasMassal').submit();
        }
    }
}

// 5. Fungsi Cetak Massal
function cetakMassal() {
    var checkedInputs = window.tableSiswa.$('input.check-item:checked');
    if (checkedInputs.length > 0) {
        var ids = [];
        checkedInputs.each(function() {
            ids.push($(this).val());
        });
        
        var url = "{{ route('admin.siswa.cetak_massal') }}?ids=" + ids.join(',');
        window.open(url, '_blank');
    }
}

// 6. Fungsi Hapus Massal
function hapusMassal() {
    var checkedInputs = window.tableSiswa.$('input.check-item:checked');
    if (checkedInputs.length > 0) {
        if (confirm('Yakin ingin menghapus ' + checkedInputs.length + ' data siswa yang terpilih?')) {
            var hiddenInputs = $('#hiddenInputs');
            hiddenInputs.empty(); // Bersihkan input lama
            
            checkedInputs.each(function() {
                // Masukkan ID yang dicentang ke dalam form tersembunyi
                hiddenInputs.append('<input type="hidden" name="ids[]" value="' + $(this).val() + '">');
            });
            
            // Kirim form
            $('#formHapusMassal').submit();
        }
    }
}

function prosesCetakKartu(jenis) {
        let ids = [];
        
        // Menggunakan selector yang mencari semua checkbox tercentang di dalam <tbody> tabel
        $('tbody input[type="checkbox"]:checked').each(function() {
            // Pastikan nilai value dari checkbox ini bukan kosong
            if($(this).val()) {
                ids.push($(this).val());
            }
        });

        if(ids.length > 0) {
            let url = "{{ url('/admin/siswa/cetak-massal') }}?ids=" + ids.join(',') + "&jenis=" + jenis;
            window.open(url, '_blank');
        } else {
            alert('Pilih minimal satu siswa terlebih dahulu!');
        }
    }

</script>
@endsection