@extends('layouts.admin')

@section('title', 'Input Nilai & Capaian TP')

@section('content')

{{-- ============================================================
    HEADER INPUT NILAI
============================================================ --}}
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    
    <div>
        <a href="{{ route('guru.dashboard') }}" class="btn bg-white border shadow-sm rounded-pill px-4 py-1.5 fw-semibold small text-muted mb-2 transition-all" style="border-color: #e2e8f0 !important;">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
        <h3 class="fw-bold mb-1 text-dark">
            <i class="fas fa-file-signature me-2 text-primary"></i> Input Nilai Kurikulum Merdeka
        </h3>
        <p class="text-muted small mb-0">
            Mata Pelajaran: <b class="text-dark">{{ $mapel->nama_mapel }}</b> | 
            Kelas: <b class="text-dark">{{ \App\Models\Kelas::find($kelas_id)->nama_kelas ?? 'Data Kelas' }}</b>
        </p>
    </div>

    {{-- KOTAK INFORMASI / DROPDOWN --}}
    <div class="bg-white rounded-3 shadow-sm border p-3.5" style="min-width: 320px; border-color: #e2e8f0 !important;">
        <form action="{{ url()->current() }}" method="GET">
            <label class="fw-bold text-dark small mb-2 d-block">
                <i class="fas fa-filter me-1 text-primary"></i> Filter Jenis Ujian / Rapor:
            </label>
            <div class="input-group input-group-sm">
                <select name="jenis_ujian_id" class="form-select border text-dark fw-medium shadow-none" onchange="this.form.submit()" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1;">
                    @foreach($data_jenis as $jenis)
                        <option value="{{ $jenis->id }}" {{ $jenis_terpilih == $jenis->id ? 'selected' : '' }}>
                            {{ $jenis->nama_jenis }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary text-white" style="border-radius: 0 8px 8px 0;">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            
            @php
                $nama_jenis_terpilih = strtolower(\App\Models\JenisUjian::find($jenis_terpilih)->nama_jenis ?? '');
                $is_sts = strpos($nama_jenis_terpilih, 'tengah semester') !== false;
            @endphp
            
            <div class="mt-2 text-end">
                @if($is_sts)
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">
                        Mode: Sumatif Tengah Semester
                    </span>
                @else
                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">
                        Mode: Rapor Akhir Semester (Dengan TP)
                    </span>
                @endif
            </div>
        </form>
    </div>

</div>

{{-- ============================================================
    TABEL INPUT UTAMA
============================================================ --}}
<div class="card border shadow-sm rounded-3 overflow-hidden" style="border-color: #e2e8f0 !important;">
    <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold text-dark mb-0">Daftar Siswa & Pengisian Form</h6>
        <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill" style="border-color: #cbd5e1 !important;"><i class="fas fa-users me-1"></i> {{ count($data_siswa) }} Siswa</span>
    </div>

    <div class="card-body p-0">
        <form action="{{ route('guru.simpan_nilai', ['kelas_id' => $kelas_id, 'mapel_id' => $mapel->id]) }}" method="POST" id="formInputNilai">
            @csrf
            <input type="hidden" name="jenis_ujian_id" value="{{ $jenis_terpilih }}">

            <div class="table-responsive" style="max-height: 65vh; overflow-y: auto;">
                <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.9rem;">
                    
                    <thead class="text-center position-sticky top-0 z-2 table-light text-secondary" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        <tr>
                            <th rowspan="2" class="align-middle fw-bold py-3" width="5%">No</th>
                            <th rowspan="2" class="align-middle text-start fw-bold py-3" width="18%" style="min-width: 180px;">Nama Lengkap Siswa</th>
                            
                            @if(!$is_sts)
                                <th colspan="3" class="text-white border-bottom py-2 bg-primary" style="letter-spacing: 0.5px;">Nilai Sumatif Lingkup Materi / TP</th>
                            @endif
                            
                            <th rowspan="2" class="align-middle text-dark fw-bold py-3" width="10%" style="background-color: #fef08a;">Nilai Ujian</th>
                            
                            @if(!$is_sts)
                                <th rowspan="2" class="align-middle text-white fw-bold py-3 bg-success" width="9%">Nilai Akhir Rapor</th>
                                <th rowspan="2" class="align-middle text-secondary fw-bold py-3" width="35%">Deskripsi Capaian Kompetensi (Kurmer)</th>
                            @endif
                        </tr>
                        
                        @if(!$is_sts)
                        <tr>
                            <th width="7%" class="py-2 text-white bg-primary bg-opacity-75" title="Tujuan Pembelajaran 1">TP 1</th>
                            <th width="7%" class="py-2 text-white bg-primary bg-opacity-75" title="Tujuan Pembelajaran 2">TP 2</th>
                            <th width="7%" class="py-2 text-white bg-primary bg-opacity-75" title="Tujuan Pembelajaran 3">TP 3</th>
                        </tr>
                        @endif
                    </thead>
                    
                    <tbody>
                        @forelse($data_siswa as $index => $siswa)
                        @php $n = $nilai_existing->get($siswa->id); @endphp
                        
                        <tr>
                            <td class="text-center text-muted fw-medium">{{ $index + 1 }}</td>
                            
                            <td class="fw-bold text-dark">
                                {{ $siswa->nama }}
                                <div class="small fw-normal text-muted" style="font-size: 0.75rem;">NIS: {{ $siswa->nis ?? '-' }}</div>
                            </td>
                            
                            @if(!$is_sts)
                                <td class="p-2"><input type="number" name="nilai[{{ $siswa->id }}][s1]" class="form-control form-control-sm text-center border bg-light fw-medium shadow-none" value="{{ $n->sumatif_1 ?? '' }}" min="0" max="100" placeholder="-" style="border-radius: 6px;"></td>
                                <td class="p-2"><input type="number" name="nilai[{{ $siswa->id }}][s2]" class="form-control form-control-sm text-center border bg-light fw-medium shadow-none" value="{{ $n->sumatif_2 ?? '' }}" min="0" max="100" placeholder="-" style="border-radius: 6px;"></td>
                                <td class="p-2"><input type="number" name="nilai[{{ $siswa->id }}][s3]" class="form-control form-control-sm text-center border bg-light fw-medium shadow-none" value="{{ $n->sumatif_3 ?? '' }}" min="0" max="100" placeholder="-" style="border-radius: 6px;"></td>
                            @else
                                <input type="hidden" name="nilai[{{ $siswa->id }}][s1]" value="0">
                                <input type="hidden" name="nilai[{{ $siswa->id }}][s2]" value="0">
                                <input type="hidden" name="nilai[{{ $siswa->id }}][s3]" value="0">
                            @endif
                            
                            <td class="p-2">
                                <input type="number" name="nilai[{{ $siswa->id }}][ujian]" class="form-control form-control-sm text-center border fw-bold text-dark shadow-none" value="{{ $n->nilai_ujian ?? '' }}" min="0" max="100" placeholder="-" style="border-radius: 6px; background-color: #fef9c3; border-color: #fde047 !important;">
                            </td>
                            
                            @if(!$is_sts)
                                <td class="text-center bg-success bg-opacity-10 border-success border-opacity-25">
                                    <div class="fw-bold text-success" style="font-size: 1.1rem;">{{ $n->nilai_akhir ?? '-' }}</div>
                                </td>
                                
                                <td class="p-2">
                                    <div class="input-group input-group-sm mb-1.5">
                                        <span class="input-group-text bg-white border-0 text-success" title="Capaian Tertinggi / Tercapai"><i class="fas fa-arrow-up"></i></span>
                                        <input type="text" name="nilai[{{ $siswa->id }}][deskripsi_tercapai]" class="form-control border bg-light shadow-none" style="border-radius: 6px; border-color: #e2e8f0 !important;" value="{{ $n->deskripsi_tercapai ?? '' }}" placeholder="Menunjukkan penguasaan yang sangat baik dalam...">
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-0 text-danger" title="Capaian Terendah / Perlu Bimbingan"><i class="fas fa-arrow-down"></i></span>
                                        <input type="text" name="nilai[{{ $siswa->id }}][deskripsi_peningkatan]" class="form-control border bg-light shadow-none" style="border-radius: 6px; border-color: #e2e8f0 !important;" value="{{ $n->deskripsi_peningkatan ?? '' }}" placeholder="Perlu bimbingan dan peningkatan dalam hal...">
                                    </div>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $is_sts ? 4 : 8 }}" class="text-center py-5 text-muted">
                                <i class="fas fa-users-slash fa-2x text-primary opacity-25 mb-2"></i>
                                <h6 class="text-dark fw-bold">Belum Ada Data Siswa</h6>
                                <p class="text-muted small mb-0">Tidak ada siswa yang terdaftar di kelas ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- FOOTER / ACTION BUTTON --}}
            @if(count($data_siswa) > 0)
            <div class="card-footer bg-white border-top p-3.5 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1 text-primary"></i> Pastikan menekan tombol <b>Simpan Data</b> sebelum berpindah halaman.
                </div>
                
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold shadow-sm text-white" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin me-2\'><i> Menyimpan...'">
                    <i class="fas fa-save me-2"></i> Simpan Seluruh Data Nilai & Deskripsi
                </button>
            </div>
            @endif

        </form>
    </div>
</div>

<style>
    .table-hover tbody tr:hover td { background-color: #f8fafc; }
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    input[type=number] { -moz-appearance: textfield; }
    .form-control:focus {
        border-color: #2563eb !important;
        background-color: #ffffff !important;
    }
</style>
@endsection