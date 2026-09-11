<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu Akun Login Guru</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #000;
        }
        
        /* 1. Hapus float dan margin kiri/kanan manual agar Bootstrap Grid bekerja maksimal */
        .card-login {
            border: 2px dashed #333;
            border-radius: 8px;
            padding: 8px; /* Padding dikurangi */
            background: #fff;
            page-break-inside: avoid;
            width: 100%;
        }
        
        .school-header {
            font-size: 0.75rem; /* Font diperkecil */
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        
        .credentials {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 5px; /* Padding dikurangi */
            border-radius: 6px;
            font-family: monospace;
            font-size: 0.85rem; /* Font diperkecil */
        }
        
        /* 2. Optimasi ruang kertas saat diprint */
        @media print {
            /* Perkecil margin pinggir kertas menjadi 1cm */
            @page { size: A4 portrait; margin: 1cm; }
            .no-print { display: none !important; }
            body { padding: 0 !important; }
            .card-login { border: 1px solid #000 !important; }
            .container-fluid { padding: 0 !important; }
        }
    </style>
</head>
<body class="p-4">

    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary fw-bold px-4">
            <i class="fas fa-print me-1"></i> Cetak / Print Kartu
        </button>
        <button onclick="window.close()" class="btn btn-secondary fw-bold px-4">Tutup</button>
    </div>

    <!-- 3. Gunakan container-fluid untuk memaksimalkan lebar layar/kertas -->
    <div class="container-fluid px-0">
        <!-- 4. Tambahkan gx-2 gy-2 untuk mengecilkan jarak (gutter) antar kolom -->
        <div class="row gx-2 gy-2">
            @foreach($data_guru as $guru)
            
            <!-- 5. UBAH DARI col-md-6 MENJADI col-4 AGAR MENJADI 3 KOLOM KESAMPING -->
            <div class="col-4">
                <div class="card-login">
                    <div class="school-header text-center">
                        SMA-SMK MULIA BUANA<br>
                        <!-- Typo 'SIMTEM' diperbaiki menjadi 'SISTEM' -->
                        <span style="font-size: 0.65rem; font-weight: normal;">KARTU AKSES SISTEM UJIAN</span>
                    </div>
                    <table class="table table-sm table-borderless mb-1" style="font-size: 0.75rem;">
                        <tr>
                            <td width="30%" class="p-0 pb-1">Nama</td>
                            <td class="p-0 pb-1">: <strong>{{ $guru->nama }}</strong></td>
                        </tr>
                        <tr>
                            <td width="30%" class="p-0 pb-1">Akses</td>
                            <td class="p-0 pb-1">: <strong>Guru</strong></td>
                        </tr>
                    </table>
                    <div class="credentials text-center">
                        <div>Username: <b>{{ $guru->nip }}</b></div>
                        <div>Password: <b>{{ $guru->nip }}</b> <span style="font-size: 0.65rem; display: block; color: #666;"></span></div>
                    </div>
                    <div class="text-center mt-2" style="font-size: 0.65rem; color: #555;">
                        Gunakan ini untuk login ke Sistem Ujian atau Portal Ujian.
                    </div>
                </div>
            </div>
            
            @endforeach
        </div>
    </div>

</body>
</html>