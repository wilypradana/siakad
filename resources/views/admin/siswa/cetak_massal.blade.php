<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu - 15 Per Lembar</title>
    <style>
        /* Pengaturan ukuran kertas agar pas A4 */
        @page {
            size: A4 portrait;
            margin: 5mm;
        }

        body { 
            font-family: 'Arial', sans-serif; 
            margin: 0; padding: 0; 
            background: #f0f0f0; 
        }

        /* Pembungkus utama untuk setiap lembar kertas */
        .page-wrapper {
            background: white;
            width: 200mm; 
            height: 287mm; /* Tinggi maksimal kertas A4 dikurangi margin */
            margin: 0 auto 10mm auto; /* Tambahkan jarak antar kertas di layar monitor */
            padding: 5mm;
            box-sizing: border-box;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(5, 1fr); /* Atur menjadi 5 baris agar pas tinggi kertas */
            gap: 5px; 
            page-break-after: always; /* Paksa pindah halaman kertas setiap kali pembungkus ini selesai */
        }

        /* Hilangkan page-break pada kertas terakhir agar tidak ada halaman putih kosong */
        .page-wrapper:last-child {
            page-break-after: auto;
            margin-bottom: 0;
        }

        .card-ujian { 
            border: 1.2px solid #000; 
            padding: 8px; 
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #fff;
            height: 100%; /* Kartu memenuhi kotak row-nya masing-masing */
        }

        .header { 
            text-align: center; 
            border-bottom: 1px solid #000; 
            margin-bottom: 4px; 
            padding-bottom: 3px;
        }

        .jenis-ujian { 
            margin: 2px 0; 
            font-size: 9px; 
            font-weight: bold; 
            color: #000; 
            text-transform: uppercase;
            background: #eee;
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            letter-spacing: 0.5px;
        }

        .header h4 { margin: 3px 0 2px 0; font-size: 11px; letter-spacing: 0.5px; }
        .header p { margin: 0; font-size: 8px; }

        .content-table { width: 100%; font-size: 9px; border-collapse: collapse; margin-top: 2px; }
        .content-table td { padding: 1px 0; vertical-align: top; border-bottom: 0.1px solid #eee; }

        .kode-area { 
            border: 1px dashed red; 
            text-align: center; 
            padding: 4px; 
            margin-top: 2px;
        }
        .kode-label { font-size: 7px; font-weight: bold; }
        .kode-val { font-size: 13px; font-weight: bold; color: red; letter-spacing: 1px; }

        .footer-note { font-size: 6px; text-align: center; color: #555; line-height: 1.1; margin-top: 2px; }

        @media print {
            body { background: white; }
            .no-print-btn { display: none; }
            .page-wrapper { margin: 0; border: none; }
        }

        .no-print-btn {
            position: fixed; top: 20px; right: 20px; padding: 10px 20px;
            background: #198754; color: white; border: none; border-radius: 5px;
            cursor: pointer; font-weight: bold; z-index: 9999;
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print-btn">
        🖨️ PRINT KARTU
    </button>

    @php
        $siswaPertama = $data_siswa->first();
        $namaJenisUjian = 'UJIAN AKADEMIK';
        
        if ($siswaPertama) {
            $ujian = \App\Models\Ujian::with('jenisUjian')->where('kelas_id', $siswaPertama->kelas_id)->first();
            if ($ujian && $ujian->jenisUjian) {
                $namaJenisUjian = $ujian->jenisUjian->nama_jenis;
            }
        }
        
        // Memecah koleksi data_siswa menjadi potongan per 15 kartu (karena grid 3x5)
        $halamanSiswa = $data_siswa->chunk(15);
    @endphp

    @foreach($halamanSiswa as $siswaSatuHalaman)
    <!-- Membuka bungkus kertas A4 yang baru -->
    <div class="page-wrapper">
        
        @foreach($siswaSatuHalaman as $siswa)
        <!-- Isi Kartu -->
        <div class="card-ujian">
            <div class="header">
                <div style="font-size: 8px; font-weight: bold; letter-spacing: 0.5px;">KARTU PESERTA</div>
                <h4>SMA-SMK MULIA BUANA</h4>
                <p>Tahun Pelajaran 2025/2026</p>
            </div>
            <div class="text-center" style="text-align: center;">
                <div class="jenis-ujian">{{ $namaJenisUjian }}</div>
            </div>

            <table class="content-table">
                <tr><td width="30%">NAMA</td><td width="5%">:</td><td><strong>{{ strtoupper($siswa->nama) }}</strong></td></tr>
                <tr><td>USERNAME</td><td>:</td><td>{{ $siswa->nis }}</td></tr>
                <tr><td>PASSWORD</td><td>:</td><td><strong style="color: #0d6efd;">{{ $siswa->nis }}</strong></td></tr>
                <tr><td>KELAS</td><td>:</td><td>{{ $siswa->kelas->nama_kelas ?? '-' }}</td></tr>
            </table>

            <div class="kode-area">
                <span class="kode-label">KODE VERIFIKASI (PADA SOAL UJIAN)</span><br>
                <div class="kode-val">{{ $siswa->nomor_kartu ?? 'N/A' }}</div>
            </div>

            <div class="footer-note">
                Username menggunakan NIS masing-masing.<br>
                Simpan kartu ini, jangan sampai hilang atau rusak.
            </div>
        </div>
        @endforeach
        
    </div> <!-- Penutup bungkus kertas -->
    @endforeach

</body>
</html>