<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Ujian - {{ $siswa->nama }}</title>
    <style>
        @media print { .no-print { display: none; } }
        body { font-family: 'Arial', sans-serif; display: flex; justify-content: center; padding: 20px; background: #f4f4f4; }
        .card { width: 550px; background: #fff; border: 2px solid #000; padding: 25px; box-shadow: 5px 5px 15px rgba(0,0,0,0.1); position: relative; border-radius: 10px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 15px; }
        .header h2 { margin: 0; font-size: 20px; color: #0d6efd; text-transform: uppercase; }
        .jenis-ujian { 
            margin: 10px auto; 
            font-size: 13px; 
            font-weight: bold; 
            color: #000; 
            text-transform: uppercase;
            background: #eee;
            display: inline-block;
            padding: 5px 15px;
            border-radius: 5px;
            letter-spacing: 1px;
            border: 1px solid #ddd;
        }
        .content td { padding: 4px; font-size: 13px; }
        
        /* Tabel Jadwal */
        .table-jadwal { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 11px; }
        .table-jadwal th, .table-jadwal td { border: 1px solid #000; padding: 6px; text-align: left; }
        .table-jadwal th { background-color: #f2f2f2; text-align: center; }

        .kode-area { margin-top: 15px; border: 2px dashed #dc3545; padding: 10px; text-align: center; background: #fff8f8; }
        .kode-label { font-size: 10px; font-weight: bold; color: #333; }
        .kode-value { font-size: 22px; font-weight: bold; color: #dc3545; letter-spacing: 2px; }
        
        .footer { margin-top: 15px; font-size: 9px; text-align: center; color: #555; border-top: 1px solid #eee; padding-top: 10px; }
        .btn-print { position: absolute; top: -45px; right: 0; background: #198754; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 5px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

    @php
        // Cek pengamanan jika data siswa ada
        $namaJenisUjian = 'UJIAN AKADEMIK'; // Nilai default aman
        
        if ($siswa) {
            $ujian = \App\Models\Ujian::with('jenisUjian')->where('kelas_id', $siswa->kelas_id)->first();
            if ($ujian && $ujian->jenisUjian) {
                $namaJenisUjian = $ujian->jenisUjian->nama_jenis;
            }
        }
    @endphp

    <div class="card">
        <button class="btn-print no-print" onclick="window.print()">
            <i class="fas fa-print"></i> CETAK SEKARANG
        </button>

        <div class="header">
            <h2>KARTU PESERTA UJIAN</h2>
            <p style="margin: 5px 0 0 0; font-size: 16px; font-weight: bold;">SMA-SMK MULIA BUANA</p>
            <p style="margin: 0; font-size: 12px;">Tahun Pelajaran 2025/2026</p>
        </div>

        <!-- POSISI PENEMPATAN NAMA JENIS UJIAN -->
        <div style="text-align: center;">
            <div class="jenis-ujian">{{ $namaJenisUjian }}</div>
        </div>

        <table class="content" width="100%">
            <tr>
                <td width="30%">NAMA PESERTA</td>
                <td width="5%">:</td>
                <td style="font-weight: bold;">{{ strtoupper($siswa->nama) }}</td>
            </tr>
            <tr>
                <td>NIS / USERNAME</td>
                <td>:</td>
                <td>{{ $siswa->nis }}</td>
            </tr>
            <tr>
                <td>PASSWORD</td>
                <td>:</td>
                <td><strong style="color: #0d6efd;">{{ $siswa->nis }}</strong></td>
            </tr>
            <tr>
                <td>KELAS</td>
                <td>:</td>
                <td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td>
            </tr>
        </table>

        {{-- BAGIAN JADWAL UJIAN --}}
        <h4 style="margin: 15px 0 5px 0; font-size: 12px; border-left: 4px solid #0d6efd; padding-left: 8px;">JADWAL UJIAN AKTIF</h4>
        <table class="table-jadwal">
            <thead>
                <tr>
                    <th>Mata Pelajaran</th>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @php
                    // Mengambil ujian yang sesuai dengan kelas siswa
                    $ujians = \App\Models\Ujian::where('kelas_id', $siswa->kelas_id)
                                ->orderBy('waktu_mulai', 'asc')
                                ->get();
                @endphp
                @forelse($ujians as $u)
                <tr>
                    <td>{{ $u->mapel->nama_mapel ?? '-' }} ({{ $u->judul_ujian }})</td>
                    <td align="center">{{ \Carbon\Carbon::parse($u->waktu_mulai)->translatedFormat('d M Y') }}</td>
                    <td align="center">{{ \Carbon\Carbon::parse($u->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($u->waktu_selesai)->format('H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" align="center">Belum ada jadwal ujian untuk kelas ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="kode-area">
            <span class="kode-label">KODE LOGIN (SECRET KEY):</span><br>
            <div class="kode-value">{{ $siswa->nomor_kartu ?? 'BELUM GENERATE' }}</div>
        </div>

        <div class="footer">
            1. Kartu ini wajib dibawa saat pelaksanaan ujian.<br>
            2. Jaga kerahasiaan Kode Login Anda agar tidak digunakan orang lain.<br>
            <small>Dicetak pada: {{ date('d/m/Y H:i:s') }}</small>
        </div>
    </div>

</body>
</html>