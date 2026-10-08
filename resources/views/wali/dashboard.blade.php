@extends('layouts.admin')

@section('title', 'Dashboard Wali Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2>Dashboard Wali Kelas</h2>
        <p class="text-muted mb-0">Kelas: <strong class="text-primary">{{ $kelas_wali->nama_kelas }} ({{ $kelas_wali->jurusan }})</strong></p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <!-- Aksi Kelas Terpadu (Legger & Cetak Massal) -->
    <div class="bg-white p-3 rounded shadow-sm border mb-4">
        <form action="{{ route('wali.lihat_legger') }}" method="GET" class="d-flex flex-column flex-md-row gap-2 mb-0 align-items-md-center">
            @csrf
            
            <label class="fw-bold text-nowrap mb-0 me-md-2">Tindakan Kelas:</label>
            
            <select name="jenis_ujian_id" class="form-select form-select-sm border-secondary" style="max-width: 300px;" required>
                <option value="">-- Pilih Jenis Ujian --</option>
                @foreach($jenis_ujian_tersedia as $jenis)
                    <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-sm btn-primary text-nowrap fw-bold shadow-sm">
                <i class="fas fa-table me-1"></i> Lihat Legger
            </button>
            <button type="submit" 
                    formaction="{{ route('wali.cetak_rapor_massal') }}" 
                    formmethod="POST" 
                    formtarget="_blank"
                    class="btn btn-sm btn-success text-nowrap fw-bold shadow-sm">
                <i class="fas fa-print me-1"></i> Cetak Semua Siswa
            </button>
            <a href="{{ route('wali.input_catatan_massal') }}" class="btn btn-sm btn-warning text-dark text-nowrap fw-bold shadow-sm">
                <i class="fas fa-edit me-1"></i> Input Kehadiran & Catatan
            </a>
            <a href="{{ route('wali.ekskul_prestasi_massal') }}" class="btn btn-sm btn-info text-dark text-nowrap fw-bold shadow-sm">
                <i class="fas fa-star me-1"></i> Input Ekskul & Prestasi
            </a>
        </form>
    </div>

<div class="card shadow-sm border-0" style="border-radius: 15px;">
    <div class="card-header bg-white pt-4 pb-0 border-0">
        <h5 class="fw-bold"><i class="fas fa-users text-primary me-2"></i> Daftar Siswa & Cetak Rapor</h5>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="10">No</th>
                        <th width="20">NIS</th>
                        <th width="30">Nama Siswa</th>
                        <th width="20">Total Nilai Input</th>
                        <th width="50" class="text-center">Aksi Rapor</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data_siswa as $index => $siswa)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $siswa->nis }}</td>
                        <td>{{ $siswa->nama }}</td>
                        <td>
                            <span class="badge bg-info text-dark">
                                {{ $siswa->nilai ? $siswa->nilai->count() : 0 }} Mapel Selesai
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                
                                <!-- Form Cetak Rapor Tunggal -->
                                <form action="{{ route('wali.cetak_rapor') }}" method="POST" target="_blank" class="m-0 d-flex gap-1">
                                    @csrf
                                    <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                    <select name="jenis_ujian_id" class="form-select form-select-sm" style="min-width: 130px;" required>
                                        <option value="">Pilih Ujian...</option>
                                        @foreach($jenis_ujian_tersedia as $jenis)
                                            <option value="{{ $jenis->id }}">{{ $jenis->nama_jenis }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <button type="submit" class="btn btn-sm btn-success fw-bold"><i class="fas fa-print"></i> Cetak</button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    

                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada siswa di kelas ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tangkap semua dropdown jenis ujian di dalam modal
    const selects = document.querySelectorAll('.select-jenis-ujian');
    
    selects.forEach(select => {
        select.addEventListener('change', function() {
            const siswaId = this.getAttribute('data-siswa-id');
            const jenisUjianId = this.value;
            
            // Cari elemen input yang berada di dalam form (modal) yang sama
            const modalForm = this.closest('.modal-content');
            const inputSakit = modalForm.querySelector('.input-sakit');
            const inputIzin = modalForm.querySelector('.input-izin');
            const inputAlfa = modalForm.querySelector('.input-alfa');
            const inputCatatan = modalForm.querySelector('.input-catatan');

            // Kosongkan nilai input form terlebih dahulu setiap kali pilihan berubah
            inputSakit.value = '';
            inputIzin.value = '';
            inputAlfa.value = '';
            inputCatatan.value = '';

            // Jika ada pilihan ujian yang di-klik (bukan kosong)
            if(jenisUjianId) {
                // Beri efek loading sederhana dengan disable input sejenak (opsional)
                this.disabled = true;

                // Tarik data dari route /wali-kelas/rapor/catatan/get-data via fetch
                fetch(`/wali-kelas/rapor/catatan/get-data?siswa_id=${siswaId}&jenis_ujian_id=${jenisUjianId}`)
                    .then(response => response.json())
                    .then(res => {
                        if(res.status === 'success') {
                            // Isi form dengan data dari database
                            inputSakit.value = res.data.sakit;
                            inputIzin.value = res.data.izin;
                            inputAlfa.value = res.data.alfa;
                            inputCatatan.value = res.data.catatan_wali_kelas;
                        }
                    })
                    .catch(error => console.error('Gagal mengambil data:', error))
                    .finally(() => {
                        // Aktifkan kembali pilihan select setelah data selesai ditarik
                        this.disabled = false;
                    });
            }
        });
    });
});
</script>
@endsection