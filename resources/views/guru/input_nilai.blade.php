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

    {{-- KOTAK FILTER JENIS UJIAN --}}
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
    PANEL GENERATOR DESKRIPSI TP OTOMATIS (KHUSUS RAPOR AKHIR)
============================================================ --}}
@if(!$is_sts)
<div class="card border shadow-sm rounded-3 mb-4" style="border-color: #bfdbfe !important; background-color: #f8fafc;">
    <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="d-inline-flex align-items-center justify-content-center rounded-2 bg-primary bg-opacity-10 text-primary" style="width: 32px; height: 32px;">
                <i class="fas fa-magic"></i>
            </span>
            <div>
                <h6 class="fw-bold text-dark mb-0">Generator Deskripsi Capaian Kompetensi Otomatis</h6>
                <div class="text-muted" style="font-size: 0.75rem;">Isi ringkasan materi tiap Tujuan Pembelajaran (TP) di bawah ini, lalu klik generate untuk menyusun deskripsi seluruh siswa secara otomatis.</div>
            </div>
        </div>
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 fw-semibold shadow-sm" onclick="generateSemuaDeskripsi()">
            <i class="fas fa-wand-magic-sparkles me-1"></i> Generate Otomatis Semua Siswa
        </button>
    </div>

    <div class="card-body p-3">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-primary mb-1">Materi Tujuan Pembelajaran 1 (TP 1)</label>
                <input type="text" id="master_tp1" class="form-control form-control-sm border shadow-none" placeholder="Contoh: konfigurasi dasar routing statis dan dinamis" style="border-radius: 8px;">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-primary mb-1">Materi Tujuan Pembelajaran 2 (TP 2)</label>
                <input type="text" id="master_tp2" class="form-control form-control-sm border shadow-none" placeholder="Contoh: manajemen bandwidth dan firewall pada router" style="border-radius: 8px;">
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-primary mb-1">Materi Tujuan Pembelajaran 3 (TP 3)</label>
                <input type="text" id="master_tp3" class="form-control form-control-sm border shadow-none" placeholder="Contoh: troubleshooting konektivitas jaringan nirkabel" style="border-radius: 8px;">
            </div>
        </div>
    </div>
</div>
@endif

{{-- ============================================================
    TABEL INPUT UTAMA
============================================================ --}}
<div class="card border shadow-sm rounded-3 overflow-hidden" style="border-color: #e2e8f0 !important;">
    <div class="card-header bg-white border-bottom p-3.5 d-flex align-items-center justify-content-between">
        <h6 class="fw-bold text-dark mb-0">Daftar Siswa & Pengisian Form</h6>
        <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill" style="border-color: #cbd5e1 !important;">
            <i class="fas fa-users me-1"></i> {{ count($data_siswa) }} Siswa
        </span>
    </div>

    <div class="card-body p-0">
        <form action="{{ route('guru.simpan_nilai', ['kelas_id' => $kelas_id, 'mapel_id' => $mapel->id]) }}" method="POST" id="formInputNilai">
            @csrf
            <input type="hidden" name="jenis_ujian_id" value="{{ $jenis_terpilih }}">

            <div class="table-responsive" style="max-height: 65vh; overflow-y: auto;">
                <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.9rem;">
                    
                    <thead class="text-center position-sticky top-0 z-2 table-light text-secondary" style="box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                        <tr>
                            <th rowspan="2" class="align-middle fw-bold py-3" width="4%">No</th>
                            <th rowspan="2" class="align-middle text-start fw-bold py-3" width="18%" style="min-width: 180px;">Nama Lengkap Siswa</th>
                            
                            @if(!$is_sts)
                                <th colspan="3" class="text-white border-bottom py-2 bg-primary" style="letter-spacing: 0.5px;">Nilai Sumatif Lingkup Materi / TP</th>
                            @endif
                            
                            <th rowspan="2" class="align-middle text-dark fw-bold py-3" width="9%" style="background-color: #fef08a;">Nilai Ujian</th>
                            
                            @if(!$is_sts)
                                <th rowspan="2" class="align-middle text-white fw-bold py-3 bg-success" width="8%">Nilai Akhir</th>
                                <th rowspan="2" class="align-middle text-secondary fw-bold py-3" width="37%">Deskripsi Capaian Kompetensi (Kurmer)</th>
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
                        
                        <tr class="baris-siswa" data-siswa-id="{{ $siswa->id }}" data-siswa-nama="{{ $siswa->nama }}">
                            <td class="text-center text-muted fw-medium">{{ $index + 1 }}</td>
                            
                            <td class="fw-bold text-dark">
                                {{ $siswa->nama }}
                                <div class="small fw-normal text-muted" style="font-size: 0.75rem;">NIS: {{ $siswa->nis ?? '-' }}</div>
                            </td>
                            
                            @if(!$is_sts)
                                <td class="p-2">
                                    <input type="number" name="nilai[{{ $siswa->id }}][s1]" class="form-control form-control-sm text-center border bg-light fw-medium shadow-none input-tp1" value="{{ $n->sumatif_1 ?? '' }}" min="0" max="100" placeholder="-" style="border-radius: 6px;" oninput="hitungNilaiAkhir({{ $siswa->id }})">
                                </td>
                                <td class="p-2">
                                    <input type="number" name="nilai[{{ $siswa->id }}][s2]" class="form-control form-control-sm text-center border bg-light fw-medium shadow-none input-tp2" value="{{ $n->sumatif_2 ?? '' }}" min="0" max="100" placeholder="-" style="border-radius: 6px;" oninput="hitungNilaiAkhir({{ $siswa->id }})">
                                </td>
                                <td class="p-2">
                                    <input type="number" name="nilai[{{ $siswa->id }}][s3]" class="form-control form-control-sm text-center border bg-light fw-medium shadow-none input-tp3" value="{{ $n->sumatif_3 ?? '' }}" min="0" max="100" placeholder="-" style="border-radius: 6px;" oninput="hitungNilaiAkhir({{ $siswa->id }})">
                                </td>
                            @else
                                <input type="hidden" name="nilai[{{ $siswa->id }}][s1]" value="0">
                                <input type="hidden" name="nilai[{{ $siswa->id }}][s2]" value="0">
                                <input type="hidden" name="nilai[{{ $siswa->id }}][s3]" value="0">
                            @endif
                            
                            <td class="p-2">
                                <input type="number" name="nilai[{{ $siswa->id }}][ujian]" class="form-control form-control-sm text-center border fw-bold text-dark shadow-none input-ujian" value="{{ $n->nilai_ujian ?? '' }}" min="0" max="100" placeholder="-" style="border-radius: 6px; background-color: #fef9c3; border-color: #fde047 !important;" oninput="hitungNilaiAkhir({{ $siswa->id }})">
                            </td>
                            
                            @if(!$is_sts)
                                <td class="text-center bg-success bg-opacity-10 border-success border-opacity-25">
                                    <div class="fw-bold text-success nilai-akhir-display" id="akhir_{{ $siswa->id }}" style="font-size: 1.1rem;">{{ $n->nilai_akhir ?? '-' }}</div>
                                </td>
                                
                                <td class="p-2">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="flex-grow-1">
                                            <div class="input-group input-group-sm mb-1.5">
                                                <span class="input-group-text bg-white border-0 text-success" title="Capaian Tertinggi / Tercapai"><i class="fas fa-arrow-up"></i></span>
                                                <input type="text" name="nilai[{{ $siswa->id }}][deskripsi_tercapai]" id="desc_tinggi_{{ $siswa->id }}" class="form-control border bg-light shadow-none" style="border-radius: 6px; border-color: #e2e8f0 !important;" value="{{ $n->deskripsi_tercapai ?? '' }}" placeholder="Menunjukkan penguasaan yang sangat baik dalam...">
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white border-0 text-danger" title="Capaian Terendah / Perlu Bimbingan"><i class="fas fa-arrow-down"></i></span>
                                                <input type="text" name="nilai[{{ $siswa->id }}][deskripsi_peningkatan]" id="desc_rendah_{{ $siswa->id }}" class="form-control border bg-light shadow-none" style="border-radius: 6px; border-color: #e2e8f0 !important;" value="{{ $n->deskripsi_peningkatan ?? '' }}" placeholder="Perlu bimbingan dan peningkatan dalam hal...">
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary px-2 py-1 mt-1" onclick="generateDeskripsiSiswa({{ $siswa->id }})" title="Generate Deskripsi Siswa Ini">
                                            <i class="fas fa-magic"></i>
                                        </button>
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
                
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold shadow-sm text-white" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin me-2\'></i> Menyimpan...'">
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

<script>
// 1. Live Calculation Nilai Akhir Rapor (Sesuai Rumus di NilaiController)
function hitungNilaiAkhir(siswaId) {
    const row = document.querySelector(`tr[data-siswa-id="${siswaId}"]`);
    if (!row) return;

    const s1 = parseFloat(row.querySelector('.input-tp1')?.value) || 0;
    const s2 = parseFloat(row.querySelector('.input-tp2')?.value) || 0;
    const s3 = parseFloat(row.querySelector('.input-tp3')?.value) || 0;
    const ujian = parseFloat(row.querySelector('.input-ujian')?.value) || 0;

    const display = document.getElementById(`akhir_${siswaId}`);
    if (display) {
        const rataSumatif = (s1 + s2 + s3) / 3;
        const nilaiAkhir = Math.round((rataSumatif + ujian) / 2);
        display.textContent = nilaiAkhir;
    }
}

// 2. Generator Deskripsi Kurmer Per Siswa
function generateDeskripsiSiswa(siswaId, silent = false) {
    const tp1Text = document.getElementById('master_tp1')?.value.trim();
    const tp2Text = document.getElementById('master_tp2')?.value.trim();
    const tp3Text = document.getElementById('master_tp3')?.value.trim();

    if (!tp1Text && !tp2Text && !tp3Text) {
        if (!silent) {
            alert('Silakan isi minimal satu materi Tujuan Pembelajaran (TP 1, TP 2, atau TP 3) pada kotak generator di atas terlebih dahulu.');
            document.getElementById('master_tp1')?.focus();
        }
        return false;
    }

    const row = document.querySelector(`tr[data-siswa-id="${siswaId}"]`);
    if (!row) return false;

    const daftarTP = [];
    const val1 = row.querySelector('.input-tp1')?.value;
    const val2 = row.querySelector('.input-tp2')?.value;
    const val3 = row.querySelector('.input-tp3')?.value;

    if (tp1Text && val1 !== '') daftarTP.push({ nilai: parseFloat(val1), materi: tp1Text });
    if (tp2Text && val2 !== '') daftarTP.push({ nilai: parseFloat(val2), materi: tp2Text });
    if (tp3Text && val3 !== '') daftarTP.push({ nilai: parseFloat(val3), materi: tp3Text });

    if (daftarTP.length === 0) {
        if (!silent) alert('Nilai TP siswa ini masih kosong.');
        return false;
    }

    // Urutkan dari nilai tertinggi ke terendah
    daftarTP.sort((a, b) => b.nilai - a.nilai);

    const tertinggi = daftarTP[0];
    const terendah = daftarTP[daftarTP.length - 1];

    // Susun Kalimat Capaian Tertinggi (Kurikulum Merdeka)
    let predikatTinggi = 'penguasaan yang baik';
    if (tertinggi.nilai >= 90) predikatTinggi = 'penguasaan yang sangat baik';
    else if (tertinggi.nilai >= 80) predikatTinggi = 'penguasaan yang baik';
    else predikatTinggi = 'pemahaman yang cukup baik';

    const kalimatTinggi = `Menunjukkan ${predikatTinggi} dalam ${tertinggi.materi}.`;

    // Susun Kalimat Perlu Bimbingan
    let kalimatRendah = '';
    if (daftarTP.length === 1 || tertinggi.materi === terendah.materi) {
        kalimatRendah = terendah.nilai < 75 
            ? `Perlu bimbingan dan pendampingan lebih lanjut dalam ${terendah.materi}.`
            : `Pertahankan capaian belajar dalam ${terendah.materi}.`;
    } else {
        kalimatRendah = terendah.nilai < 75
            ? `Perlu bimbingan dan peningkatan dalam hal ${terendah.materi}.`
            : `Perlu penguatan dan peningkatan lebih lanjut dalam ${terendah.materi}.`;
    }

    const inputTinggi = document.getElementById(`desc_tinggi_${siswaId}`);
    const inputRendah = document.getElementById(`desc_rendah_${siswaId}`);

    if (inputTinggi) {
        inputTinggi.value = kalimatTinggi;
        inputTinggi.style.backgroundColor = '#eff6ff';
        setTimeout(() => inputTinggi.style.backgroundColor = '', 600);
    }
    if (inputRendah) {
        inputRendah.value = kalimatRendah;
        inputRendah.style.backgroundColor = '#eff6ff';
        setTimeout(() => inputRendah.style.backgroundColor = '', 600);
    }

    return true;
}

// 3. Generator Massal untuk Seluruh Siswa di Kelas
function generateSemuaDeskripsi() {
    const tp1Text = document.getElementById('master_tp1')?.value.trim();
    const tp2Text = document.getElementById('master_tp2')?.value.trim();
    const tp3Text = document.getElementById('master_tp3')?.value.trim();

    if (!tp1Text && !tp2Text && !tp3Text) {
        alert('Silakan isi ringkasan materi TP 1, TP 2, atau TP 3 pada kotak generator di atas terlebih dahulu!');
        document.getElementById('master_tp1')?.focus();
        return;
    }

    const rows = document.querySelectorAll('.baris-siswa');
    let berhasil = 0;

    rows.forEach(row => {
        const siswaId = row.getAttribute('data-siswa-id');
        if (generateDeskripsiSiswa(siswaId, true)) {
            berhasil++;
        }
    });

    if (berhasil === 0) {
        alert('Belum ada nilai TP yang terisi pada tabel siswa. Silakan isi angka nilai TP 1–3 terlebih dahulu.');
    }
}
</script>
@endsection