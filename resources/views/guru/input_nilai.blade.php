@extends('layouts.admin')

@section('title', 'Input Nilai Kurikulum Merdeka')

@section('content')
<div class="mb-4">
    <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary btn-sm mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>
    <h2 class="fw-bold">Input Nilai: {{ $mapel->nama_mapel }}</h2>
</div>

<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-header bg-white pt-4 border-0">
        <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-center">
            <div class="col-auto">
                <label class="fw-bold text-muted">Pilih Jenis Rapor / Ujian:</label>
            </div>
            <div class="col-md-4">
                <select name="jenis_ujian_id" class="form-select border-primary" onchange="this.form.submit()">
                    @foreach($data_jenis as $jenis)
                        <option value="{{ $jenis->id }}" {{ $jenis_terpilih == $jenis->id ? 'selected' : '' }}>
                            {{ $jenis->nama_jenis }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <span class="badge bg-light text-primary border border-primary p-2">
                    <i class="fas fa-info-circle"></i> Input nilai untuk rapor yang dipilih
                </span>
            </div>
        </form>
    </div>

    <div class="card-body">
        <form action="{{ route('guru.simpan_nilai', ['kelas_id' => $kelas_id, 'mapel_id' => $mapel->id]) }}" method="POST">
            @csrf
            <input type="hidden" name="jenis_ujian_id" value="{{ $jenis_terpilih }}">
            
            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="table-primary text-center">
                        <tr>
                            <th rowspan="2" class="align-middle">No</th>
                            <th rowspan="2" class="align-middle text-start">Nama Siswa</th>
                            <th colspan="3">Nilai Sumatif Lingkup Materi</th>
                            <th rowspan="2" class="align-middle">Nilai Sumatif Akhir</th>
                            <th rowspan="2" class="align-middle">Nilai Rapor</th>
                        </tr>
                        <tr>
                            <th width="100">Sumatif 1</th>
                            <th width="100">Sumatif 2</th>
                            <th width="100">Sumatif 3</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data_siswa as $index => $siswa)
                        @php $n = $nilai_existing->get($siswa->id); @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $siswa->nama }}</td>
                            <td><input type="number" name="nilai[{{ $siswa->id }}][s1]" class="form-control text-center" value="{{ $n->sumatif_1 ?? '' }}"></td>
                            <td><input type="number" name="nilai[{{ $siswa->id }}][s2]" class="form-control text-center" value="{{ $n->sumatif_2 ?? '' }}"></td>
                            <td><input type="number" name="nilai[{{ $siswa->id }}][s3]" class="form-control text-center" value="{{ $n->sumatif_3 ?? '' }}"></td>
                            <td><input type="number" name="nilai[{{ $siswa->id }}][ujian]" class="form-control text-center bg-light fw-bold" value="{{ $n->nilai_ujian ?? '' }}"></td>
                            <td class="text-center fw-bold text-primary fs-5">{{ $n->nilai_akhir ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary btn-lg px-5 shadow"><i class="fas fa-save me-2"></i> Simpan Nilai {{ \App\Models\JenisUjian::find($jenis_terpilih)->nama_jenis }}</button>
            </div>
        </form>
    </div>
</div>
@endsection