@extends('layouts.admin')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="fas fa-chalkboard-teacher text-primary me-2"></i> Dashboard Wali Kelas
        </h3>
        <p class="text-muted small mb-0">Kelas: <strong class="text-primary">{{ $kelas_wali->nama_kelas }} ({{ $kelas_wali->jurusan }})</strong></p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Aksi Kelas Terpadu (Legger & Cetak Massal) -->
<div class="card shadow-sm border mb-4" style="border-radius: 12px; border-color: #e2e8f0 !important;">
    <div class="card-body p-3.5">
        <form action="{{ route('wali.lihat_legger') }}" method="GET" class="d-flex flex-column flex-md-row gap-2 mb-0 align-items-md-center">
            @csrf
            
            <label class="fw-bold text-dark text-nowrap mb-0 me-md-2 small">
                <i class="fas fa-tasks text-primary me-1"></i> Tindakan Kelas:
            </label>
            
            <select name="jenis_ujian_id" class="form-select form-select-sm border" style="max-width: 280px;" required>
                <option value="">-- Pilih Jenis Ujian --</option>
                @foreach($jenis_ujian_tersedia as $jenis)
                    <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-sm btn-primary text-nowrap fw-semibold shadow-none">
                <i class="fas fa-table me-1"></i> Lihat Legger
            </button>
            <button type="submit" 
                    formaction="{{ route('wali.cetak_rapor_massal') }}" 
                    formmethod="POST" 
                    formtarget="_blank"
                    class="btn btn-sm btn-success text-nowrap fw-semibold shadow-none">
                <i class="fas fa-print me-1"></i> Cetak Semua Siswa
            </button>
            <a href="{{ route('wali.input_catatan_massal') }}" class="btn btn-sm btn-warning text-dark text-nowrap fw-semibold shadow-none">
                <i class="fas fa-edit me-1"></i> Input Kehadiran & Catatan
            </a>
            <a href="{{ route('wali.ekskul_prestasi_massal') }}" class="btn btn-sm btn-info text-dark text-nowrap fw-semibold shadow-none">
                <i class="fas fa-star me-1"></i> Input Ekskul & Prestasi
            </a>
        </form>
    </div>
</div>

<div class="card shadow-sm border" style="border-radius: 12px; border-color: #e2e8f0 !important;">
    <div class="card-header bg-white pt-4 pb-3 border-bottom d-flex align-items-center justify-content-between">
        <h5 class="fw-bold text-dark mb-0">
            <i class="fas fa-users text-primary me-2"></i> Daftar Siswa & Cetak Rapor
        </h5>
        <span class="badge bg-light text-secondary border px-2.5 py-1" style="font-size: 0.8rem;">
            Total: {{ count($data_siswa) }} Siswa
        </span>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-secondary" style="font-size: 0.85rem;">
                    <tr>
                        <th class="py-3 ps-4" width="5%">No</th>
                        <th class="py-3" width="15%">NIS</th>
                        <th class="py-3" width="30%">Nama Siswa</th>
                        <th class="py-3" width="20%">Total Nilai Input</th>
                        <th class="py-3 text-center pe-4" width="30%">Aksi Rapor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data_siswa as $index => $siswa)
                    <tr>
                        <td class="ps-4 text-muted fw-medium">{{ $index + 1 }}</td>
                        <td class="fw-bold text-dark">{{ $siswa->nis }}</td>
                        <td class="fw-semibold text-dark">{{ $siswa->nama }}</td>
                        <td>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1">
                                {{ $siswa->nilai ? $siswa->nilai->count() : 0 }} Mapel Selesai
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                <!-- Form Cetak Rapor Tunggal -->
                                <form action="{{ route('wali.cetak_rapor') }}" method="POST" target="_blank" class="m-0 d-flex gap-1.5 align-items-center">
                                    @csrf
                                    <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                    <select name="jenis_ujian_id" class="form-select form-select-sm" style="min-width: 140px;" required>
                                        <option value="">Pilih Ujian...</option>
                                        @foreach($jenis_ujian_tersedia as $jenis)
                                            <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <button type="submit" class="btn btn-sm btn-success fw-semibold text-nowrap">
                                        <i class="fas fa-print me-1"></i> Cetak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fas fa-folder-open fa-2x text-primary opacity-25 mb-2"></i>
                            <p class="mb-0 small">Belum ada data siswa di kelas ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('.select-jenis-ujian');
    
    selects.forEach(select => {
        select.addEventListener('change', function() {
            const siswaId = this.getAttribute('data-siswa-id');
            const jenisUjianId = this.value;
            
            const modalForm = this.closest('.modal-content');
            if(!modalForm) return;
            
            const inputSakit = modalForm.querySelector('.input-sakit');
            const inputIzin = modalForm.querySelector('.input-izin');
            const inputAlfa = modalForm.querySelector('.input-alfa');
            const inputCatatan = modalForm.querySelector('.input-catatan');

            if(inputSakit) inputSakit.value = '';
            if(inputIzin) inputIzin.value = '';
            if(inputAlfa) inputAlfa.value = '';
            if(inputCatatan) inputCatatan.value = '';

            if(jenisUjianId) {
                this.disabled = true;

                fetch(`/wali-kelas/rapor/catatan/get-data?siswa_id=${siswaId}&jenis_ujian_id=${jenisUjianId}`)
                    .then(response => response.json())
                    .then(res => {
                        if(res.status === 'success') {
                            if(inputSakit) inputSakit.value = res.data.sakit;
                            if(inputIzin) inputIzin.value = res.data.izin;
                            if(inputAlfa) inputAlfa.value = res.data.alfa;
                            if(inputCatatan) inputCatatan.value = res.data.catatan_wali_kelas;
                        }
                    })
                    .catch(error => console.error('Gagal mengambil data:', error))
                    .finally(() => {
                        this.disabled = false;
                    });
            }
        });
    });
});
</script>
@endsection