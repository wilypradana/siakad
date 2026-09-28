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
            
            @php
                // Cek apakah ujian yang dipilih adalah STS (berdasarkan kata kuncinya)
                $nama_jenis_terpilih = strtolower(\App\Models\JenisUjian::find($jenis_terpilih)->nama_jenis ?? '');
                $is_sts = strpos($nama_jenis_terpilih, 'tengah semester') !== false;
            @endphp

            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="table-primary text-center">
                        <tr>
                            <th rowspan="2" class="align-middle">No</th>
                            <th rowspan="2" class="align-middle text-start">Nama Siswa</th>
                            
                            @if(!$is_sts)
                                <th colspan="3">Nilai Sumatif Lingkup Materi</th>
                            @endif
                            
                            <th rowspan="2" class="align-middle">Nilai Murni Ujian ({{ strtoupper($nama_jenis_terpilih) }})</th>
                            
                            @if(!$is_sts)
                                <th rowspan="2" class="align-middle">Nilai Rapor Akhir</th>
                            @endif
                        </tr>
                        
                        @if(!$is_sts)
                        <tr>
                            <th width="100">TP 1</th>
                            <th width="100">TP 2</th>
                            <th width="100">TP 3</th>
                        </tr>
                        @endif
                    </thead>
                    <tbody>
                        @foreach($data_siswa as $index => $siswa)
                        @php $n = $nilai_existing->get($siswa->id); @endphp
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $siswa->nama }}</td>
                            
                            @if(!$is_sts)
                                <td><input type="number" name="nilai[{{ $siswa->id }}][s1]" class="form-control text-center" value="{{ $n->sumatif_1 ?? '' }}" min="0" max="100"></td>
                                <td><input type="number" name="nilai[{{ $siswa->id }}][s2]" class="form-control text-center" value="{{ $n->sumatif_2 ?? '' }}" min="0" max="100"></td>
                                <td><input type="number" name="nilai[{{ $siswa->id }}][s3]" class="form-control text-center" value="{{ $n->sumatif_3 ?? '' }}" min="0" max="100"></td>
                            @else
                                <!-- Jika STS, paksa nilai s1, s2, s3 menjadi 0 agar rumus tidak error -->
                                <input type="hidden" name="nilai[{{ $siswa->id }}][s1]" value="0">
                                <input type="hidden" name="nilai[{{ $siswa->id }}][s2]" value="0">
                                <input type="hidden" name="nilai[{{ $siswa->id }}][s3]" value="0">
                            @endif
                            
                            <td><input type="number" name="nilai[{{ $siswa->id }}][ujian]" class="form-control text-center bg-light fw-bold" value="{{ $n->nilai_ujian ?? '' }}" min="0" max="100"></td>
                            
                            @if(!$is_sts)
                                <td class="text-center fw-bold text-primary fs-5">{{ $n->nilai_akhir ?? '-' }}</td>
                            @endif
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

<script>
document.addEventListener('DOMContentLoaded', function () {

    const inputs = document.querySelectorAll(
        'input[name^="nilai["][type="number"]'
    );

    inputs.forEach(function (input) {

        input.addEventListener('paste', function (e) {

            const text = (e.clipboardData || window.clipboardData)
                .getData('text');

            // Jika hanya satu nilai biasa, biarkan browser menangani paste
            if (!text.includes('\n') && !text.includes('\r')) {
                return;
            }

            e.preventDefault();

            // Ambil nilai per baris
            const values = text
                .split(/\r?\n/)
                .map(value => value.trim())
                .filter(value => value !== '');

            // Tentukan kolom berdasarkan name input
            const name = input.getAttribute('name');

            // Contoh:
            // nilai[12][s1]
            // nilai[12][s2]
            // nilai[12][s3]
            // nilai[12][ujian]

            const match = name.match(/\[([^\]]+)\]\[([^\]]+)\]/);

            if (!match) {
                return;
            }

            const kolom = match[2];

            // Ambil semua input pada kolom yang sama
            const columnInputs = Array.from(inputs).filter(function (el) {
                const elName = el.getAttribute('name');
                return elName.endsWith('][' + kolom + ']');
            });

            // Posisi siswa tempat paste dimulai
            const startIndex = columnInputs.indexOf(input);

            // Masukkan nilai ke bawah
            values.forEach(function (value, index) {

                const targetIndex = startIndex + index;

                if (targetIndex < columnInputs.length) {
                    columnInputs[targetIndex].value = value;
                }

            });

        });

    });

});
</script>

@endsection