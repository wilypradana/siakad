@extends('layouts.admin')

@section('title', 'Input Ekskul & Prestasi Massal')

@section('content')
<div class="mb-4">
    <a href="{{ route('wali.dashboard') }}" class="btn btn-secondary btn-sm mb-3"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
    <h2 class="fw-bold">Input Ekskul & Prestasi Massal</h2>
    <p class="text-muted">Kelas: <strong>{{ $kelas_wali->nama_kelas }}</strong></p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Form Filter Ujian -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
    <div class="card-body">
        <form action="{{ route('wali.ekskul_prestasi_massal') }}" method="GET" class="d-flex align-items-end gap-3">
            <div>
                <label class="fw-bold mb-1">Pilih Jenis Ujian / Semester:</label>
                <select name="jenis_ujian_id" class="form-select" style="min-width: 300px;" required>
                    <option value="">-- Pilih Ujian (SAS / SAT) --</option>
                    @foreach($jenis_ujian_tersedia as $jenis)
                        <option value="{{ $jenis->id }}" {{ $jenis_ujian_id == $jenis->id ? 'selected' : '' }}>
                            {{ $jenis->nama_jenis }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Tampilkan Form</button>
        </form>
    </div>
</div>

<!-- Form Input Massal (Hanya Muncul Jika Ujian Sudah Dipilih) -->
@if($jenis_ujian_id)
<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <form action="{{ route('wali.simpan_ekskul_prestasi_massal') }}" method="POST">
        @csrf
        <input type="hidden" name="jenis_ujian_id" value="{{ $jenis_ujian_id }}">
        
        @php
            // Daftar Pilihan Ekstrakurikuler Pilihan (Pramuka dihapus karena wajib di kolom 1)
            $list_ekskul = [
                'Paskibra', 'PMR', 'Teater', 'Pencak Silat', 
                'Marawis', 'CMB', 'Gulat', 'Futsal', 'Voli', 
                'Badminton', 'Band', 'Komputer & AI'
            ];
        @endphp

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th width="3%" rowspan="2">No</th>
                            <th width="17%" rowspan="2">Nama Siswa</th>
                            <th width="25%" colspan="2" class="bg-primary bg-opacity-10">Ekskul Wajib</th>
                            <th width="35%" colspan="2">Ekskul Pilihan (Opsional)</th>
                            <th width="20%" rowspan="2">Prestasi (Opsional)</th>
                        </tr>
                        <tr>
                            <th class="bg-primary bg-opacity-10">Nama Ekskul</th>
                            <th class="bg-primary bg-opacity-10">Predikat</th>
                            <th>Nama Ekskul</th>
                            <th>Predikat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data_siswa as $index => $siswa)
                            @php
                                $ekskul = $ekskul_tersimpan[$siswa->id] ?? collect();
                                $prestasi = $prestasi_tersimpan[$siswa->id] ?? collect();
                                
                                // Tarik data spesifik agar urutan di database tidak tertukar
                                $e_wajib = $ekskul->firstWhere('nama_ekskul', 'Pramuka'); 
                                $e_pilihan = $ekskul->where('nama_ekskul', '!=', 'Pramuka')->first();
                                $p1 = $prestasi->first(); 
                            @endphp
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="fw-bold text-nowrap">
                                    {{ $siswa->nama }}
                                    <input type="hidden" name="siswa_ids[]" value="{{ $siswa->id }}">
                                </td>
                                
                                <!-- EKSKUL 1 (WAJIB PRAMUKA) -->
                                <td class="text-center fw-bold text-primary bg-light">
                                    Pramuka
                                    <!-- Input tersembunyi agar value 'Pramuka' tetap terkirim ke controller -->
                                    <input type="hidden" name="ekskul_1[{{ $siswa->id }}]" value="Pramuka">
                                </td>
                                <td class="bg-light">
                                    <select name="predikat_1[{{ $siswa->id }}]" class="form-select form-select-sm border-primary">
                                        <option value="Sangat Baik" {{ ($e_wajib->predikat ?? '') == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                        <option value="Baik" {{ ($e_wajib->predikat ?? 'Baik') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                        <option value="Cukup" {{ ($e_wajib->predikat ?? '') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                    </select>
                                </td>

                                <!-- EKSKUL 2 (PILIHAN) -->
                                <td>
                                    <select name="ekskul_2[{{ $siswa->id }}]" class="form-select form-select-sm">
                                        <option value="">-- Kosong --</option>
                                        @foreach($list_ekskul as $nama)
                                            <option value="{{ $nama }}" {{ ($e_pilihan->nama_ekskul ?? '') == $nama ? 'selected' : '' }}>
                                                {{ $nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="predikat_2[{{ $siswa->id }}]" class="form-select form-select-sm">
                                        <option value="Sangat Baik" {{ ($e_pilihan->predikat ?? '') == 'Sangat Baik' ? 'selected' : '' }}>Sangat Baik</option>
                                        <option value="Baik" {{ ($e_pilihan->predikat ?? 'Baik') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                        <option value="Cukup" {{ ($e_pilihan->predikat ?? '') == 'Cukup' ? 'selected' : '' }}>Cukup</option>
                                    </select>
                                </td>

                                <!-- PRESTASI -->
                                <td>
                                    <input type="text" name="prestasi[{{ $siswa->id }}]" class="form-control form-control-sm" value="{{ $p1->nama_prestasi ?? '' }}" placeholder="Juara 1 Lomba... / Kosongkan">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">Belum ada siswa di kelas ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white text-end p-3" style="border-radius: 0 0 15px 15px;">
            <button type="submit" class="btn btn-success fw-bold px-4"><i class="fas fa-save me-2"></i> Simpan Semua</button>
        </div>
    </form>
</div>
@endif
@endsection