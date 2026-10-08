@extends('layouts.admin')

@section('title', 'Absensi Siswa')

@section('content')
<div class="mb-4">
    <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary btn-sm mb-3"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
    <h2 class="fw-bold">Absensi Siswa: {{ $mapel->nama_mapel }}</h2>
    <p class="text-muted">Kelas: <strong>{{ $kelas->nama_kelas }}</strong></p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-header bg-white pt-4 border-0">
        <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-center">
            <div class="col-auto">
                <label class="fw-bold text-muted">Pilih Tanggal:</label>
            </div>
            <div class="col-md-3">
                <input type="date" name="tanggal" class="form-control border-primary" value="{{ $tanggal }}" onchange="this.form.submit()">
            </div>
        </form>
    </div>

    <div class="card-body">
        <form action="{{ route('guru.simpan_absensi', ['kelas_id' => $kelas->id, 'mapel_id' => $mapel->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">

            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="table-primary text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th width="30%" class="text-start">Nama Siswa</th>
                            <th width="45%">Status Kehadiran (Tanggal: {{ $tanggal }})</th>
                            <th width="20%">Akumulasi Rekap (H / S / I / A)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data_siswa as $index => $siswa)
                        @php 
                            $absen = $absensi_existing->get($siswa->id);
                            $status_pilih = $absen->status ?? 'H'; // Default Hadir

                            // Hitung rekap total kehadiran siswa ini pada mapel ini
                            $rekap_h = \App\Models\AbsensiSiswa::where('siswa_id', $siswa->id)->where('mapel_id', $mapel->id)->where('status', 'H')->count();
                            $rekap_s = \App\Models\AbsensiSiswa::where('siswa_id', $siswa->id)->where('mapel_id', $mapel->id)->where('status', 'S')->count();
                            $rekap_i = \App\Models\AbsensiSiswa::where('siswa_id', $siswa->id)->where('mapel_id', $mapel->id)->where('status', 'I')->count();
                            $rekap_a = \App\Models\AbsensiSiswa::where('siswa_id', $siswa->id)->where('mapel_id', $mapel->id)->where('status', 'A')->count();
                        @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $siswa->nama }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="status[{{ $siswa->id }}]" value="H" id="hadir_{{ $siswa->id }}" {{ $status_pilih == 'H' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success btn-sm px-3" for="hadir_{{ $siswa->id }}">Hadir</label>

                                    <input type="radio" class="btn-check" name="status[{{ $siswa->id }}]" value="S" id="sakit_{{ $siswa->id }}" {{ $status_pilih == 'S' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-warning btn-sm px-3" for="sakit_{{ $siswa->id }}">Sakit</label>

                                    <input type="radio" class="btn-check" name="status[{{ $siswa->id }}]" value="I" id="izin_{{ $siswa->id }}" {{ $status_pilih == 'I' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info btn-sm px-3" for="izin_{{ $siswa->id }}">Izin</label>

                                    <input type="radio" class="btn-check" name="status[{{ $siswa->id }}]" value="A" id="alfa_{{ $siswa->id }}" {{ $status_pilih == 'A' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-danger btn-sm px-3" for="alfa_{{ $siswa->id }}">Alfa</label>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">H: {{ $rekap_h }}</span>
                                <span class="badge bg-warning text-dark">S: {{ $rekap_s }}</span>
                                <span class="badge bg-info text-dark">I: {{ $rekap_i }}</span>
                                <span class="badge bg-danger">A: {{ $rekap_a }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow"><i class="fas fa-save me-2"></i> Simpan Absensi</button>
            </div>
        </form>
    </div>
</div>
@endsection