@extends('layouts.admin')

@section('title', 'Rekapitulasi Hasil Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0">Rekapitulasi Hasil Ujian</h2>
        <p class="text-muted mb-0">Mapel: <strong>{{ $ujian->mapel->nama_mapel }}</strong> | Kelas: <strong>{{ $ujian->kelas->nama_kelas }}</strong></p>
    </div>
    
    <div class="d-flex gap-2">
        <a href="{{ auth()->user()->role == 'admin' ? route('admin.ujian.index') : route('ujian.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        
        <button onclick="window.print()" class="btn btn-success shadow-sm">
            <i class="fas fa-print me-1"></i> Cetak Rekap
        </button>

        <!-- TOMBOL TUTUP PAKSA -->
        <form action="{{ route('admin.ujian.tutup_paksa', $ujian->id) }}" method="POST" onsubmit="return confirm('YAKIN TUTUP PAKSA? Semua siswa yang sedang mengerjakan akan dipaksa Selesai, dinilai berdasarkan jawaban terakhir mereka, dan akses ujian akan dikunci!');">
            @csrf
            <button type="submit" class="btn btn-warning shadow-sm text-dark fw-bold">
                <i class="fas fa-stop-circle me-1"></i> Tutup Paksa
            </button>
        </form>

        <!-- TOMBOL RESET SEMUA -->
        <form action="{{ route('admin.ujian.reset_semua', $ujian->id) }}" method="POST" onsubmit="return confirm('BAHAYA: Yakin ingin MERESET SEMUA SISWA? Seluruh nilai dan riwayat jawaban pada kelas ini akan musnah secara permanen!');">
            @csrf
            <button type="submit" class="btn btn-danger shadow-sm fw-bold">
                <i class="fas fa-sync-alt me-1"></i> Reset Semua
            </button>
        </form>
    </div>
</div>

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

<div class="card shadow-sm border-0" style="border-radius: 15px;">
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
            <table class="table table-hover table-bordered align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="py-3">No</th>
                        <th width="15%" class="py-3">NIS</th>
                        <th width="35%" class="py-3 text-start">Nama Siswa</th>
                        <th width="10%" class="py-3 text-success">Benar</th>
                        <th width="10%" class="py-3 text-danger">Salah</th>
                        <th width="10%" class="py-3 text-primary fs-5">Nilai Akhir</th>
                        <th width="15%" class="py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($siswas as $index => $siswa)
                        @php
                            // Cek apakah siswa ini ada di data hasil ujian
                            $data_hasil = $hasil->get($siswa->id);
                        @endphp
                        <tr>
                            <td class="fw-bold">{{ $index + 1 }}</td>
                            <td>{{ $siswa->nis }}</td>
                            <td class="text-start fw-bold">{{ strtoupper($siswa->nama) }}</td>
                            
                            @if($data_hasil)
                                <td class="text-success fw-bold">{{ $data_hasil->jumlah_benar }}</td>
                                <td class="text-danger fw-bold">{{ $data_hasil->jumlah_salah }}</td>
                                <td class="text-primary fw-bold fs-5">{{ $data_hasil->nilai_akhir }}</td>
                                <td><span class="badge bg-success"><i class="fas fa-check me-1"></i> Selesai</span></td>
                            @else
                                <td class="text-muted">-</td>
                                <td class="text-muted">-</td>
                                <td class="text-muted">-</td>
                                <td>
                                    @if($ujian->metode_ujian == 'cbt')
                                        <span class="badge bg-secondary">Belum Mengerjakan</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Manual (G-Form)</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-4 text-muted">Tidak ada data siswa di kelas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* CSS Khusus agar tampilan rapi saat tombol Print ditekan */
    @media print {
        body * { visibility: hidden; }
        .card, .card * { visibility: visible; }
        .card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; }
        .btn { display: none !important; }
    }
</style>

<style>
/* CSS KHUSUS UNTUK TAMPILAN SAAT DICETAK (PRINT) */
@media print {

    .print-header {
        display: block !important;
    }
    /* 1. Atur ukuran kertas dan perkecil margin tepi kertas */
    @page {
        size: A4 portrait;
        margin: 1cm; /* Memperluas area cetak */
    }
    

    /* 2. Sembunyikan elemen UI yang tidak perlu ikut tercetak */
    #sidebar, .top-bar, .btn, footer, header, nav {
        display: none !important;
    }

    /* 3. Hilangkan bayangan, border, dan padding dari container utama */
    #main-content, .card, .card-body, .container, .container-fluid {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
        border: none !important;
        background-color: white !important;
    }

    /* 4. INI KUNCI UTAMANYA: Padatkan baris tabel dan perkecil font */
    table {
        width: 100% !important;
        margin-bottom: 0 !important;
    }
    table th, table td {
        padding: 3px 5px !important; /* Mengurangi ruang kosong atas-bawah di dalam sel */
        font-size: 11px !important;  /* Memperkecil ukuran huruf agar muat banyak */
        line-height: 1.2 !important; /* Merapatkan jarak antar baris teks */
        color: #000 !important;      /* Pastikan teks berwarna hitam pekat */
    }

    /* 5. Sesuaikan ukuran judul agar tidak memakan tempat */
    h2, h3, h4, h5 {
        font-size: 14pt !important;
        margin-bottom: 5px !important;
        color: #000 !important;
    }
    p {
        font-size: 11pt !important;
        margin-bottom: 5px !important;
    }

    
}
</style>
@endsection