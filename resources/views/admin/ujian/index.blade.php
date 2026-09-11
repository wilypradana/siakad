@extends('layouts.admin')

@section('title', 'Manajemen Portal Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Portal Ujian (G-Form)</h2>
    <div>
        @if(auth()->user()->role == 'admin')
            <button class="btn btn-outline-primary shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#modalTambahJenis">
                <i class="fas fa-tags me-1"></i> Buat Jenis Ujian
            </button>
        @endif
        
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUjian">
            <i class="fas fa-plus me-1"></i> Buat Jadwal Ujian
        </button>
    </div>
</div>

@if(auth()->user()->role == 'admin')
<div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
    <div class="card-header bg-white pt-3 border-0">
        <h5 class="fw-bold text-secondary"><i class="fas fa-tags me-2"></i> Data Master Jenis Ujian</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Jenis Ujian</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data_jenis as $index => $jenis)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $jenis->nama_jenis }}</td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-warning border-0" data-bs-toggle="modal" data-bs-target="#modalEditJenis{{ $jenis->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger border-0" data-bs-toggle="modal" data-bs-target="#modalHapusJenis{{ $jenis->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Modal Edit Jenis -->
                    <div class="modal fade" id="modalEditJenis{{ $jenis->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title">Edit Jenis Ujian</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('ujian.jenis.update', $jenis->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="modal-body text-start">
                                        <label>Nama Jenis Ujian</label>
                                        <input type="text" name="nama_jenis" class="form-control" value="{{ $jenis->nama_jenis }}" required>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Hapus Jenis -->
                    <div class="modal fade" id="modalHapusJenis{{ $jenis->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">Hapus Jenis</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-start">
                                    Yakin ingin menghapus jenis <b>{{ $jenis->nama_jenis }}</b>?
                                </div>
                                <div class="modal-footer">
                                    <form action="{{ route('ujian.jenis.destroy', $jenis->id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted">Belum ada jenis ujian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow-sm border-0 mb-5">
    <!-- FORM HAPUS MASSAL -->
    <form action="{{ route('ujian.bulkDelete') }}" method="POST">
        @csrf
        @method('DELETE')
        
        <div class="card-header bg-white pt-3 pb-2 border-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-secondary mb-0"><i class="fas fa-list me-2"></i> Daftar Jadwal Ujian Aktif</h5>
            <button type="submit" class="btn btn-danger btn-sm shadow-sm fw-bold" onclick="return confirm('Yakin ingin menghapus semua jadwal ujian yang diceklis?')">
                <i class="fas fa-trash-alt me-1"></i> Hapus Terpilih
            </button>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle w-100" id="tableUjian">
                    <thead class="table-light">
                        <tr>
                            <th width="3%" class="text-center">
                                <input type="checkbox" id="checkAll" class="form-check-input" style="cursor: pointer;">
                            </th>
                            <th width="5%">No</th>
                            <th width="20%">Judul & Jenis Ujian</th>
                            <th width="15%">Mata Pelajaran</th>
                            <th width="10%">Kelas</th>
                            <th width="17%">Waktu Pelaksanaan</th>
                            <th width="15%">Guru</th>
                            <th width="10%">Status</th>
                            <th width="5%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data_ujian as $index => $ujian)
                            @php
                                $sekarang = \Carbon\Carbon::now();
                                $mulai = \Carbon\Carbon::parse($ujian->waktu_mulai);
                                $selesai = \Carbon\Carbon::parse($ujian->waktu_selesai);
                                
                                if ($sekarang->lt($mulai)) { $status = 'Akan Datang'; $badge = 'bg-secondary'; } 
                                elseif ($sekarang->between($mulai, $selesai)) { $status = 'Sedang Berjalan'; $badge = 'bg-success blink'; } 
                                else { $status = 'Selesai'; $badge = 'bg-danger'; }
                            @endphp
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" name="ids[]" value="{{ $ujian->id }}" class="form-check-input check-item" style="cursor: pointer;">
                            </td>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <span class="badge bg-primary mb-1">{{ $ujian->jenisUjian->nama_jenis ?? 'Umum' }}</span><br>
                                <span class="fw-bold text-dark">{{ $ujian->judul_ujian }}</span>
                            </td>
                            <td class="fw-semibold">{{ $ujian->mapel->nama_mapel }}</td>
                            <td><span class="badge bg-info text-dark">{{ $ujian->kelas->nama_kelas }}</span></td>
                            <td style="font-size: 0.85rem;">
                                <i class="fas fa-play-circle text-success me-1"></i> {{ $mulai->format('d/m/Y H:i') }} <br>
                                <i class="fas fa-stop-circle text-danger me-1"></i> {{ $selesai->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                <span class="text-uppercase" style="font-size: 0.85rem;">{{ $ujian->guru->nama ?? 'Admin' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $badge }}">{{ $status }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ $ujian->link_gform }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Cek Link">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#modalResetSiswa{{ $ujian->id }}" title="Reset Ujian Siswa">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalEditUjian{{ $ujian->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

<!-- ============================================== -->
<!-- MODAL EDIT DAN RESET (DI LUAR FORM HAPUS) -->
<!-- ============================================== -->
@foreach($data_ujian as $ujian)
    <!-- Modal Edit Jadwal Ujian -->
    <div class="modal fade" id="modalEditUjian{{ $ujian->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content text-start">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark"><i class="fas fa-edit me-2"></i>Edit Jadwal Ujian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('ujian.update', $ujian->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body row">
                        <div class="col-md-6 mb-3">
                            <label>Jenis Ujian</label>
                            <select name="jenis_ujian_id" class="form-select" required>
                                @foreach($data_jenis as $jenis)
                                    <option value="{{ $jenis->id }}" {{ $ujian->jenis_ujian_id == $jenis->id ? 'selected' : '' }}>{{ $jenis->nama_jenis }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Judul Ujian Khusus</label>
                            <input type="text" name="judul_ujian" class="form-control" value="{{ $ujian->judul_ujian }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Mata Pelajaran</label>
                            <select name="mapel_id" class="form-select" required>
                                @foreach($data_mapel as $mapel)
                                    <option value="{{ $mapel->id }}" {{ $ujian->mapel_id == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Kelas Peserta</label>
                            <select name="kelas_id" class="form-select" required>
                                @foreach($data_kelas as $kelas)
                                    <option value="{{ $kelas->id }}" {{ $ujian->kelas_id == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        @if(auth()->user()->role == 'admin')
                        <div class="col-md-12 mb-3">
                            <label>Guru Pengawas/Pembuat (Khusus Admin)</label>
                            <select name="guru_id" class="form-select">
                                <option value="">-- Ujian Umum / Dibuat Admin --</option>
                                @foreach($data_guru as $guru)
                                    <option value="{{ $guru->id }}" {{ $ujian->guru_id == $guru->id ? 'selected' : '' }}>{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="col-md-12 mb-3">
                            <label>Link Google Form <span class="text-danger">*</span></label>
                            <input type="url" name="link_gform" class="form-control" value="{{ $ujian->link_gform }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label>Waktu Mulai</label>
                            <input type="datetime-local" name="waktu_mulai" class="form-control" value="{{ \Carbon\Carbon::parse($ujian->waktu_mulai)->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Waktu Selesai</label>
                            <input type="datetime-local" name="waktu_selesai" class="form-control" value="{{ \Carbon\Carbon::parse($ujian->waktu_selesai)->format('Y-m-d\TH:i') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning text-dark fw-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Reset Ujian Siswa -->
    <div class="modal fade" id="modalResetSiswa{{ $ujian->id }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content text-start">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-sync-alt me-2"></i>Reset Ujian Siswa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('ujian.reset_siswa', $ujian->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning small">
                            Pilih siswa yang mengalami kendala (tidak sengaja ter-submit/keluar) untuk mereset statusnya.
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Pilih Siswa</label>
                            <select name="siswa_id" class="form-select" required>
                                <option value="">-- Cari Nama Siswa --</option>
                                @foreach(\App\Models\Siswa::where('kelas_id', $ujian->kelas_id)->orderBy('nama', 'asc')->get() as $s)
                                    <option value="{{ $s->id }}">{{ $s->nis }} - {{ $s->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-white fw-bold"><i class="fas fa-check me-1"></i> Reset Sekarang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

<!-- ============================================== -->
<!-- MODAL TAMBAH UJIAN (DENGAN AJAX MAPEL -> KELAS)-->
<!-- ============================================== -->
<div class="modal fade" id="modalTambahUjian" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Buat Jadwal Ujian G-Form</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('ujian.store') }}" method="POST">
                @csrf
                <div class="modal-body row">
                    <div class="col-md-6 mb-3">
                        <label>Jenis Ujian</label>
                        <select name="jenis_ujian_id" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            @foreach($data_jenis as $jenis)
                                <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Judul Ujian Khusus</label>
                        <input type="text" name="judul_ujian" class="form-control" placeholder="Contoh: Susulan TKJ" required>
                    </div>
                    
                    <!-- 1. MAPEL DIATAS -->
                    <div class="col-md-12 mb-3">
                        <label>Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="mapel_id" id="mapel_select" class="form-select" required>
                            <option value="">-- Pilih Mapel Terlebih Dahulu --</option>
                            @foreach($data_mapel as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. KELAS MENUNGGU MAPEL DIPILIH -->
                    <div class="col-md-12 mb-3">
                        <label class="fw-bold text-primary">Kelas Peserta Ujian <span class="text-danger">*</span></label>
                        <p class="text-muted small mb-2">Pilih kelas yang akan mengikuti ujian. Daftar kelas menyesuaikan Mapel yang dipilih.</p>
                        
                        <div class="border p-3 rounded bg-light" style="max-height: 150px; overflow-y: auto;" id="container_kelas">
                            <div class="text-center text-muted small py-2">
                                Silakan pilih Mata Pelajaran terlebih dahulu.
                            </div>
                        </div>
                    </div>                    
                    
                    @if(auth()->user()->role == 'admin')
                    <div class="col-md-12 mb-3">
                        <label>Guru Pengawas/Pembuat (Khusus Admin)</label>
                        <select name="guru_id" class="form-select">
                            <option value="">-- Ujian Umum / Dibuat Admin --</option>
                            @foreach($data_guru as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-12 mb-3">
                        <label>Link Google Form <span class="text-danger">*</span></label>
                        <input type="url" name="link_gform" class="form-control" placeholder="https://docs.google.com/forms/..." required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Waktu Mulai</label>
                        <input type="datetime-local" name="waktu_mulai" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Waktu Selesai</label>
                        <input type="datetime-local" name="waktu_selesai" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .blink { animation: blinker 1.5s linear infinite; }
    @keyframes blinker { 50% { opacity: 0; } }
</style>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#tableUjian').DataTable({
        "language": {
            "search": "Cari Guru / Mapel / Kelas:",
            "lengthMenu": "Tampilkan _MENU_ jadwal",
            "zeroRecords": "Jadwal ujian tidak ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ jadwal",
            "infoEmpty": "Menampilkan 0 data",
            "paginate": { "next": "Berikutnya", "previous": "Sebelumnya" }
        },
        "pageLength": 15,
        "ordering": true,
        "columnDefs": [
            { "orderable": false, "targets": [0, 8] }
        ]
    });

    // Check All
    $('#checkAll').on('click', function() {
        var isChecked = this.checked;
        $('.check-item', table.rows({ filter: 'applied' }).nodes()).prop('checked', isChecked);
    });

    // AJAX Filter Kelas berdasarkan Mapel
    $('#mapel_select').on('change', function() {
        var mapel_id = $(this).val();
        var container = $('#container_kelas');

        if(mapel_id) {
            container.html('<div class="text-center text-muted small py-2"><i class="fas fa-spinner fa-spin me-2"></i>Memuat daftar kelas...</div>');
            
            $.ajax({
                url: "{{ route('ujian.get_kelas') }}",
                type: "GET",
                data: { mapel_id: mapel_id },
                success: function(data) {
                    if(data.length > 0) {
                        var html = '<div class="row">';
                        $.each(data, function(key, kelas) {
                            html += '<div class="col-md-4 mb-2">';
                            html += '<div class="form-check">';
                            html += '<input class="form-check-input" type="checkbox" name="kelas_id[]" value="'+kelas.id+'" id="ujian_kelas_'+kelas.id+'" checked>';
                            html += '<label class="form-check-label fw-bold" for="ujian_kelas_'+kelas.id+'">'+kelas.nama_kelas+'</label>';
                            html += '</div></div>';
                        });
                        html += '</div>';
                        container.html(html);
                    } else {
                        container.html('<div class="text-center text-danger small py-2">Tidak ada kelas untuk mapel ini. Pastikan Set Pembelajaran sudah diisi.</div>');
                    }
                },
                error: function() {
                    container.html('<div class="text-center text-danger small py-2">Gagal mengambil data kelas dari server.</div>');
                }
            });
        } else {
            container.html('<div class="text-center text-muted small py-2">Silakan pilih Mata Pelajaran terlebih dahulu.</div>');
        }
    });
});
</script>
@endsection