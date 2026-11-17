@extends('layouts.admin')

@section('title', 'Rekapitulasi Hasil Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="fw-bold mb-1 text-dark">
            <i class="fas fa-chart-bar me-2 text-primary"></i> Rekapitulasi Hasil Ujian
        </h3>
        <p class="text-muted small mb-0">
            Mapel: <strong class="text-dark">{{ $ujian->mapel->nama_mapel }}</strong> | 
            Kelas: <strong class="text-dark">{{ $ujian->kelas->nama_kelas }}</strong> |
            Jenis: <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">{{ $ujian->jenisUjian->nama_jenis ?? 'Umum' }}</span>
        </p>
    </div>
    
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ auth()->user()->role == 'admin' ? route('admin.ujian.index') : route('ujian.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        
        <button onclick="window.print()" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-semibold shadow-sm">
            <i class="fas fa-print me-1"></i> Cetak Rekap
        </button>

        <!-- TOMBOL BARU: SINKRONISASI KE NILAI RAPOR -->
        <form action="{{ route('admin.ujian.sync_rapor', $ujian->id) }}" method="POST" onsubmit="return confirm('Kirim seluruh nilai akhir ujian siswa ke kolom Nilai Ujian di modul Input Nilai Rapor?');">
            @csrf
            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold shadow-sm">
                <i class="fas fa-file-export me-1"></i> Kirim ke Nilai Rapor
            </button>
        </form>

        <!-- TOMBOL TUTUP PAKSA -->
        <form action="{{ route('admin.ujian.tutup_paksa', $ujian->id) }}" method="POST" onsubmit="return confirm('YAKIN TUTUP PAKSA? Semua siswa yang sedang mengerjakan akan dipaksa Selesai, dinilai berdasarkan jawaban terakhir mereka, dan akses ujian akan dikunci!');">
            @csrf
            <button type="submit" class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold text-dark shadow-sm">
                <i class="fas fa-stop-circle me-1"></i> Tutup Paksa
            </button>
        </form>

        <!-- TOMBOL RESET SEMUA -->
        <form action="{{ route('admin.ujian.reset_semua', $ujian->id) }}" method="POST" onsubmit="return confirm('BAHAYA: Yakin ingin MERESET SEMUA SISWA? Seluruh nilai dan riwayat jawaban pada kelas ini akan musnah secara permanen!');">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 fw-semibold shadow-sm">
                <i class="fas fa-sync-alt me-1"></i> Reset Semua
            </button>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 p-3 small" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 p-3 small" role="alert" style="background-color: #f8d7da; color: #842029;">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
    </div>
@endif

<div class="card border shadow-sm rounded-3 mb-5" style="border-color: #e2e8f0 !important;">
    <div class="card-header bg-white pt-3 pb-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold text-dark mb-0"><i class="fas fa-users text-primary me-2"></i> Daftar Nilai Peserta Ujian</h6>
        <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill" style="border-color: #cbd5e1 !important;">
            Selesai: {{ $hasil->count() }} / {{ count($siswas) }} Siswa
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <!-- KOP IDENTITAS UJIAN (KHUSUS CETAK) -->
            <div class="print-header mb-4" style="display: none;">
                <h4 class="text-center fw-bold text-uppercase mb-4" style="color: black;">Laporan Rekapitulasi Hasil Ujian</h4>
                <table style="width: 100%; border: none; font-size: 13px; color: black; margin-bottom: 15px;">
                    <tr>
                        <td width="15%"><strong>Mata Pelajaran</strong></td>
                        <td width="2%">:</td>
                        <td width="43%">{{ $ujian->mapel->nama_mapel }}</td>
                        <td width="15%"><strong>Kelas / Jurusan</strong></td>
                        <td width="2%">:</td>
                        <td width="23%">{{ $ujian->kelas->nama_kelas }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Ujian</strong></td>
                        <td>:</td>
                        <td>{{ $ujian->jenisUjian->nama_jenis ?? 'Ujian Umum' }} ({{ $ujian->judul_ujian }})</td>
                        <td><strong>Tgl. Pelaksanaan</strong></td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($ujian->waktu_mulai)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Guru Pengampu</strong></td>
                        <td>:</td>
                        <td colspan="4">{{ $ujian->guru->nama ?? 'Administrator' }}</td>
                    </tr>
                </table>
                <hr style="border-top: 2px solid black; opacity: 1;">
            </div>

            <table class="table table-hover table-bordered align-middle mb-0 text-center" style="font-size: 0.9rem;">
                <thead class="table-light text-secondary" style="border-bottom: 2px solid #e2e8f0;">
                    <tr>
                        <th width="5%" class="py-3">No</th>
                        <th width="15%" class="py-3">NIS</th>
                        <th width="35%" class="py-3 text-start">Nama Siswa</th>
                        <th width="10%" class="py-3 text-success">Benar</th>
                        <th width="10%" class="py-3 text-danger">Salah</th>
                        <th width="10%" class="py-3 text-primary">Nilai Akhir</th>
                        <th width="15%" class="py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $index => $siswa)
                        @php
                            $data_hasil = $hasil->get($siswa->id);
                        @endphp
                        <tr>
                            <td class="fw-bold text-muted">{{ $index + 1 }}</td>
                            <td class="text-muted">{{ $siswa->nis }}</td>
                            <td class="text-start fw-bold text-dark">{{ strtoupper($siswa->nama) }}</td>
                            
                            @if($data_hasil)
                                <td class="text-success fw-bold">{{ $data_hasil->jumlah_benar }}</td>
                                <td class="text-danger fw-bold">{{ $data_hasil->jumlah_salah }}</td>
                                <td class="text-primary fw-bold fs-6">{{ $data_hasil->nilai_akhir }}</td>
                                <td><span class="badge bg-success px-2.5 py-1 rounded-pill"><i class="fas fa-check me-1"></i> Selesai</span></td>
                            @else
                                <td class="text-muted">-</td>
                                <td class="text-muted">-</td>
                                <td class="text-muted">-</td>
                                <td>
                                    @if($ujian->metode_ujian == 'cbt')
                                        <span class="badge bg-secondary px-2.5 py-1 rounded-pill">Belum Mengerjakan</span>
                                    @else
                                        <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill">Manual (G-Form)</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-muted small">Tidak ada data siswa di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover td { background-color: #f8fafc; }

    @media print {
        .print-header { display: block !important; }
        @page { size: A4 portrait; margin: 1cm; }
        #sidebar, .top-bar, .btn, .card-header, .alert, footer, header, nav { display: none !important; }
        body * { visibility: hidden; }
        .card, .card * { visibility: visible; }
        #main-content, .card, .card-body, .container, .container-fluid {
            position: absolute; left: 0; top: 0;
            width: 100% !important; margin: 0 !important; padding: 0 !important;
            box-shadow: none !important; border: none !important; background-color: white !important;
        }
        table { width: 100% !important; margin-bottom: 0 !important; }
        table th, table td {
            padding: 3px 5px !important; font-size: 11px !important;
            line-height: 1.2 !important; color: #000 !important;
        }
        h2, h3, h4, h5 { font-size: 14pt !important; margin-bottom: 5px !important; color: #000 !important; }
        p { font-size: 11pt !important; margin-bottom: 5px !important; }
    }
</style>
@endsection