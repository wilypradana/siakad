@extends('layouts.admin')
@section('title', 'Set Pembelajaran Kelas')

@section('content')
<!-- Tambahan CSS DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<div class="card shadow-sm mb-4 border-0" style="border-radius: 12px;">
    <!-- PERUBAHAN DI SINI: Menambahkan d-flex justify-content-between agar form dan tombol sejajar -->
    <div class="card-body bg-light d-flex justify-content-between align-items-center flex-wrap gap-3">
        <form action="{{ route('pembelajaran.index') }}" method="GET" class="d-flex gap-2 flex-grow-1">
            <select name="kelas_id" class="form-select shadow-sm" style="max-width: 300px;" required>
                <option value="">-- Pilih Kelas Dahulu --</option>
                @foreach($data_kelas as $kelas)
                    <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                        {{ $kelas->nama_kelas }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary shadow-sm fw-bold">Tampilkan Mapel</button>
        </form>

        <!-- TOMBOL MENU BARU -->
        <a href="{{ route('pembelajaran.by_guru') }}" class="btn btn-warning shadow-sm fw-bold">
            <i class="fas fa-bolt me-1"></i> Mode Input Cepat (By Guru)
        </a>
    </div>
</div>

@if($kelas_terpilih)
<div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center flex-wrap gap-2 border-0">
        <h5 class="mb-0 fw-bold">Atur Guru Pengajar - Kelas {{ $kelas_terpilih->nama_kelas }}</h5>
        
        <!-- Form Fitur Copy Kelas -->
        <form action="{{ route('pembelajaran.copy') }}" method="POST" class="d-flex gap-2 m-0" onsubmit="return confirm('Yakin ingin menyalin? Pengaturan guru di kelas ini akan ditimpa penuh oleh kelas sumber.')">
            @csrf
            <input type="hidden" name="kelas_tujuan_id" value="{{ $kelas_terpilih->id }}">
            <select name="kelas_sumber_id" class="form-select form-select-sm shadow-sm" required style="width: 200px;">
                <option value="">-- Salin dari Kelas --</option>
                @foreach($data_kelas as $kelas)
                    @if($kelas->id != $kelas_terpilih->id)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                    @endif
                @endforeach
            </select>
            <button type="submit" class="btn btn-warning btn-sm text-dark fw-bold shadow-sm">
                <i class="fas fa-copy me-1"></i> Salin
            </button>
        </form>
    </div>
    
    <form action="{{ route('pembelajaran.storeMassal') }}" method="POST">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ $kelas_terpilih->id }}">
        
        <div class="card-body p-4">
            <div class="table-responsive">
                <!-- Tambahan ID tablePembelajaran -->
                <table class="table table-hover align-middle mb-0 w-100" id="tablePembelajaran">
                    <thead class="table-light">
                        <tr>
                            <th width="15%" class="py-3">Kelompok</th>
                            <th width="35%" class="py-3">Mata Pelajaran</th>
                            <th width="50%" class="py-3">Pilih Guru Pengajar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data_mapel as $mapel)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $mapel->kelompok }}</td>
                            <td class="fw-semibold text-primary">{{ $mapel->nama_mapel }}</td>
                            <td>
                                <select name="guru_id[{{ $mapel->id }}]" class="form-select">
                                    <option value="">-- Kosongkan jika tidak ada di kelas ini --</option>
                                    @foreach($data_guru as $guru)
                                        @php
                                            $terpilih = isset($pembelajaran_aktif[$mapel->id]) && $pembelajaran_aktif[$mapel->id] == $guru->id;
                                        @endphp
                                        <option value="{{ $guru->id }}" {{ $terpilih ? 'selected' : '' }}>
                                            {{ $guru->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light border-0 text-end py-3">
            <button type="submit" class="btn btn-success fw-bold px-4 shadow-sm"><i class="fas fa-save me-1"></i> Simpan Semua Pemetaan</button>
        </div>
    </form>
</div>
@endif

<!-- Tambahan Script DataTables -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tablePembelajaran').DataTable({
        "paging": false, // WAJIB FALSE agar semua data tersubmit saat disave
        "info": false,   // Menyembunyikan teks "Showing 1 to X of X entries"
        "language": {
            "search": "Cari Mapel:",
            "zeroRecords": "Mata pelajaran tidak ditemukan"
        },
        "columnDefs": [
            { "orderable": false, "targets": [2] } // Mematikan panah sorting di kolom dropdown guru
        ]
    });
});
</script>
@endsection