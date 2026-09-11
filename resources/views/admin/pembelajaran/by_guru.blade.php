@extends('layouts.admin')

@section('title', 'Set Pembelajaran via Guru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Set Pembelajaran Cepat (Berdasarkan Guru)</h4>
    <a href="{{ route('pembelajaran.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Mode Kelas
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> {{ session('error') }}</div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('pembelajaran.store_by_guru') }}" method="POST">
            @csrf
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">1. Pilih Guru</label>
                    <select name="guru_id" class="form-select" required>
                        <option value="">-- Pilih Nama Guru --</option>
                        @foreach($data_guru as $guru)
                            <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">2. Pilih Mata Pelajaran</label>
                    <select name="mapel_id" class="form-select" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        @foreach($data_mapel as $mapel)
                            <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }} (Kelompok {{ $mapel->kelompok }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3 d-flex justify-content-between align-items-end">
                <label class="form-label fw-bold mb-0">3. Pilih Kelas yang Diajar</label>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-check-all">Pilih Semua Kelas</button>
            </div>
            
            <div class="card bg-light border-0">
                <div class="card-body">
                    @php
                        // Mengelompokkan kelas berdasarkan kolom 'jurusan' dari database
                        $grouped_kelas = $data_kelas->groupBy(function($item) {
                            return $item->jurusan ? strtoupper($item->jurusan) : 'UMUM / LAINNYA';
                        });
                    @endphp

                    @foreach($grouped_kelas as $jurusan => $kelas_list)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <h6 class="fw-bold text-primary mb-0"><i class="fas fa-layer-group me-2"></i> JURUSAN: {{ $jurusan }}</h6>
                                <!-- Tombol centang semua khusus per jurusan -->
                                <button type="button" class="btn btn-sm btn-link text-decoration-none py-0 check-jurusan fw-bold" data-target="group-{{ $loop->index }}">Pilih Semua {{ $jurusan }}</button>
                            </div>
                            
                            <div class="row group-{{ $loop->index }}">
                                @foreach($kelas_list as $kelas)
                                <div class="col-md-3 col-sm-4 col-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input checkbox-kelas" type="checkbox" name="kelas_id[]" value="{{ $kelas->id }}" id="kelas_{{ $kelas->id }}">
                                        <label class="form-check-label" for="kelas_{{ $kelas->id }}" style="cursor: pointer;">
                                            {{ $kelas->nama_kelas }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-success fw-bold px-4">
                    <i class="fas fa-save me-1"></i> Simpan Set Pembelajaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Fitur Check/Uncheck Semua Kelas (Global)
    document.getElementById('btn-check-all').addEventListener('click', function() {
        let checkboxes = document.querySelectorAll('.checkbox-kelas');
        let allChecked = true;
        
        checkboxes.forEach(function(checkbox) {
            if(!checkbox.checked) allChecked = false;
        });

        checkboxes.forEach(function(checkbox) {
            checkbox.checked = !allChecked;
        });
        
        this.innerText = allChecked ? "Pilih Semua Kelas" : "Batalkan Pilihan";
    });

    // Fitur Check/Uncheck Per Jurusan
    document.querySelectorAll('.check-jurusan').forEach(function(btn) {
        btn.addEventListener('click', function() {
            let targetClass = this.getAttribute('data-target');
            let checkboxes = document.querySelectorAll('.' + targetClass + ' .checkbox-kelas');
            let allChecked = true;
            
            checkboxes.forEach(function(cb) {
                if(!cb.checked) allChecked = false;
            });

            checkboxes.forEach(function(cb) {
                cb.checked = !allChecked;
            });
            
            this.innerText = allChecked ? "Pilih Semua" : "Batalkan Pilihan";
        });
    });
</script>
@endsection