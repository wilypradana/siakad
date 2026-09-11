@extends('layouts.admin')

@section('title', 'Manajemen Jadwal Ujian')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Manajemen Jadwal Ujian</h2>
    
    <div class="d-flex gap-2">
        <a href="{{ route('jadwal.export') }}" class="btn btn-info text-white shadow-sm">
            <i class="fas fa-file-download me-1"></i> Download Jadwal
        </a>

        <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImportJadwal">
            <i class="fas fa-file-excel me-1"></i> Import CSV
        </button>

        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
            <i class="fas fa-plus me-1"></i> Tambah Jadwal
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius: 10px;">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle bg-white w-100" id="tableJadwal">
                <thead class="table-light">
                    <tr>
                        <th>Hari</th>
                        <th>Waktu</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengawas</th>
                        <th>Kelas Peserta</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grouped_jadwal = $data_jadwal->groupBy(function($j) {
                            return $j->hari . '#' . $j->jam_mulai . '#' . $j->jam_selesai . '#' . $j->mapel_id . '#' . $j->guru_id;
                        });
                    @endphp

                    @foreach($grouped_jadwal as $group)
                    @php
                        $first = $group->first();
                        $kelas_gabungan = $group->pluck('kelas.nama_kelas')->implode(', ');
                        $kelas_ids = $group->pluck('kelas_id')->toArray();
                        $grup_ids = $group->pluck('id')->implode(','); 
                    @endphp
                    <tr>
                        <td class="fw-bold text-uppercase">{{ $first->hari }}</td>
                        <td>{{ \Carbon\Carbon::parse($first->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($first->jam_selesai)->format('H:i') }}</td>
                        <td class="fw-bold text-primary">{{ $first->mapel->nama_mapel ?? '-' }}</td>
                        <td>{{ $first->guru->nama ?? '-' }}</td>
                        <td><span class="badge bg-primary px-3 py-2" style="white-space: normal;">{{ $kelas_gabungan }}</span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEditJadwal{{ $first->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('jadwal.destroy', $grup_ids) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger shadow-sm" onclick="return confirm('Hapus jadwal ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- MODAL EDIT JADWAL -->
                    <div class="modal fade text-start" id="modalEditJadwal{{ $first->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header bg-warning text-dark">
                                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Jadwal Ujian</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('jadwal.update', $first->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="grup_ids" value="{{ $grup_ids }}">

                                    <div class="modal-body row g-3">
                                        <div class="col-md-6">
                                            <label class="fw-bold">Mata Pelajaran <span class="text-danger">*</span></label>
                                            <select name="mapel_id" class="form-select" required>
                                                <option value="">-- Pilih Mata Pelajaran --</option>
                                                @foreach($data_mapel as $mapel)
                                                    <option value="{{ $mapel->id }}" {{ $first->mapel_id == $mapel->id ? 'selected' : '' }}>
                                                        {{ $mapel->nama_mapel }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="fw-bold">Guru Pengawas <span class="text-danger">*</span></label>
                                            <select name="guru_id" class="form-select" required>
                                                <option value="">-- Pilih Guru Pengawas --</option>
                                                @foreach($data_guru as $guru)
                                                    <option value="{{ $guru->id }}" {{ $first->guru_id == $guru->id ? 'selected' : '' }}>
                                                        {{ $guru->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="col-12">
                                            <label class="fw-bold text-primary">Pilih Kelas Peserta <span class="text-danger">*</span></label>
                                            <div class="row border p-3 rounded bg-light mx-0" style="max-height: 150px; overflow-y: auto;">
                                                @foreach($data_kelas as $kelas)
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="kelas_id[]" value="{{ $kelas->id }}" id="edit_kelas_{{ $first->id }}_{{ $kelas->id }}" {{ in_array($kelas->id, $kelas_ids) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="edit_kelas_{{ $first->id }}_{{ $kelas->id }}">{{ $kelas->nama_kelas }}</label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="fw-bold">Hari <span class="text-danger">*</span></label>
                                            <select name="hari" class="form-select" required>
                                                <option value="">-- Pilih Hari --</option>
                                                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                                                    <option value="{{ $hari }}" {{ strtolower($first->hari) == strtolower($hari) ? 'selected' : '' }}>{{ $hari }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="fw-bold">Jam Mulai <span class="text-danger">*</span></label>
                                            <input type="time" name="jam_mulai" class="form-control" value="{{ \Carbon\Carbon::parse($first->jam_mulai)->format('H:i') }}" required>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="fw-bold">Jam Selesai <span class="text-danger">*</span></label>
                                            <input type="time" name="jam_selesai" class="form-control" value="{{ \Carbon\Carbon::parse($first->jam_selesai)->format('H:i') }}" required>
                                        </div>
                                    </div>

                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning px-4"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- END MODAL EDIT JADWAL -->

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahJadwal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Tambah Jadwal Ujian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label class="fw-bold">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="mapel_id" class="form-select" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($data_mapel as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold">Guru Pengawas <span class="text-danger">*</span></label>
                        <select name="guru_id" class="form-select" required>
                            <option value="">-- Pilih Guru Pengawas --</option>
                            @foreach($data_guru as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label class="fw-bold text-primary">Pilih Kelas Peserta <span class="text-danger">*</span></label>
                        <div class="row border p-3 rounded bg-light mx-0" style="max-height: 150px; overflow-y: auto;">
                            @foreach($data_kelas as $kelas)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="kelas_id[]" value="{{ $kelas->id }}" id="tambah_kelas_{{ $kelas->id }}">
                                    <label class="form-check-label" for="tambah_kelas_{{ $kelas->id }}">{{ $kelas->nama_kelas }}</label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="fw-bold">Hari <span class="text-danger">*</span></label>
                        <select name="hari" class="form-select" required>
                            <option value="">-- Pilih Hari --</option>
                            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $hari)
                                <option value="{{ $hari }}">{{ $hari }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="fw-bold">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_selesai" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImportJadwal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel me-2"></i>Import Jadwal Ujian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('jadwal.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info small">Pastikan format kolom CSV: Hari, Jam Mulai, Jam Selesai, Nama Mapel, Nama Guru, Nama Kelas.</div>
                    <label class="fw-bold">Upload File CSV:</label>
                    <input type="file" name="file" class="form-control" accept=".csv" required>
                </div>
                <div class="modal-footer bg-light">
                    <button type="submit" class="btn btn-success"><i class="fas fa-upload me-1"></i> Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tableJadwal').DataTable({
            "language": {
                "lengthMenu": "Tampilkan _MENU_ entri",
                "zeroRecords": "Jadwal tidak ditemukan",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                "search": "Cari Jadwal:",
                "paginate": { "next": "Berikutnya", "previous": "Sebelumnya" }
            },
            "pageLength": 10
        });
    });
</script>
@endsection