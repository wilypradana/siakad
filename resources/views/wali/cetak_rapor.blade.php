<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RAPOR - {{ strtoupper($siswa->nama) }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #000; background-color: #fff; }
        .table-rapor th, .table-rapor td { border: 1px solid #000 !important; padding: 6px 8px !important; }
        .signature-section { margin-top: 30px; page-break-inside: avoid; }
        .editable-date { border-bottom: 1px dashed #000; padding: 0 4px; cursor: pointer; background-color: #fff9db; transition: 0.3s; }
        .editable-date:hover { background-color: #ffe066; }
        .bg-group { background-color: #f2f2f2 !important; font-weight: bold; }
        @media print {
            @page { size: A4; margin: 1.5cm; }
            body { padding: 0 !important; }
            .editable-date { border-bottom: none !important; background-color: transparent !important; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="p-4 bg-white">
    
    @php
        // LOGIKA PENENTUAN LEMBAGA DAN KEPALA SEKOLAH
        $nama_kelas = strtoupper($siswa->kelas->nama_kelas ?? '');
        $is_sma = strpos($nama_kelas, 'SMA') !== false; // Cek apakah ada kata "SMA" di nama kelas
        
        $nama_kepsek = $is_sma ? 'Marnis, S.Pd.' : 'Khodijah, S.Pd.I';
        $nama_instansi = $is_sma ? 'SEKOLAH MENENGAH ATAS (SMA)' : 'SEKOLAH MENENGAH KEJURUAN (SMK)';
        $nama_sekolah_singkat = $is_sma ? 'SMA' : 'SMK';
    @endphp

    <div class="no-print text-center mb-3">
        <div class="alert alert-warning d-inline-block small py-2">
            <i class="fas fa-info-circle"></i> <b>Tips:</b> Klik pada nama kepala sekolah, NIP, atau tanggal untuk mengubahnya sebelum mencetak.
        </div>
        <br>
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Cetak Rapor</button>
        <button onclick="window.close()" class="btn btn-danger">Tutup</button>
    </div>

    <div class="page-break">
        <div class="text-center mb-4">
            <h5 class="fw-bold mb-1">LAPORAN HASIL BELAJAR SISWA (RAPOR)</h5>
            <h6 class="fw-bold mb-1 text-uppercase">{{ $jenis_ujian->nama_jenis ?? 'UJIAN' }}</h6>
            <h6 class="fw-bold text-uppercase">{{ $nama_instansi }} MULIA BUANA</h6>
            <hr class="border-2 border-dark opacity-100 my-2">
        </div>

        <div class="row small mb-4">
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td width="35%">Nama Siswa</td><td>: <strong>{{ strtoupper($siswa->nama) }}</strong></td></tr>
                    <tr><td>NIS</td><td>: {{ $siswa->nis }}</td></tr>
                    <tr><td>Sekolah</td><td>: {{ $nama_sekolah_singkat }} Mulia Buana</td></tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td width="35%">Kelas / Rombel</td><td>: {{ $siswa->kelas->nama_kelas ?? '-' }}</td></tr>
                    <tr><td>Fase</td><td>: E / F</td></tr>
                    <tr><td>Tahun Pelajaran</td><td>: {{ $siswa->kelas->tahun_ajaran ?? '2025/2026' }}</td></tr>
                </table>
            </div>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold mb-2" style="font-size: 0.9rem;">A. Nilai Akademik</h6>
            <table class="table table-rapor align-middle mb-0 small">
                <thead class="table-light text-center align-middle fw-bold">
                    <tr>
                        <th width="5%">NO</th>
                        <th width="35%">Mata Pelajaran</th>
                        <th width="10%">Nilai Akhir</th>
                        <th width="50%">Capaian Kompetensi</th>
                    </tr>
                </thead>
                <tbody>
                    @php 
                        // 1. Cari Mapel yang terdaftar khusus untuk KELAS INI
                        $mapel_ids_kelas = \Illuminate\Support\Facades\DB::table('pembelajarans')
                                                ->where('kelas_id', $siswa->kelas_id)
                                                ->pluck('mapel_id')
                                                ->toArray();

                        if (empty($mapel_ids_kelas)) {
                            $mapel_ids_kelas = \App\Models\Ujian::where('kelas_id', $siswa->kelas_id)
                                                                ->pluck('mapel_id')
                                                                ->toArray();
                        }

                        // 2. Tarik data Mapel sesuai kelas tersebut
                        $semua_mapel = \App\Models\Mapel::whereIn('id', $mapel_ids_kelas)
                                                        ->orderBy('id', 'asc')
                                                        ->get();
                        
                        // 3. Filter berdasarkan Kelompok
                        $kelompokUmum = $semua_mapel->filter(function($m) { 
                            return strtolower($m->kelompok ?? '') === 'umum'; 
                        });
                        $kelompokKejuruan = $semua_mapel->filter(function($m) { 
                            return strtolower($m->kelompok ?? '') === 'kejuruan'; 
                        });
                    @endphp

                    <!-- KELOMPOK MATA PELAJARAN UMUM -->
                    @if($kelompokUmum->count() > 0)
                        <tr class="bg-group"><td colspan="4">Kelompok Mata Pelajaran Umum</td></tr>
                        @php $no = 1; @endphp
                        @foreach($kelompokUmum as $mapel)
                            @php
                                $nilai = $data_nilai->firstWhere('mapel_id', $mapel->id);
                            @endphp
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td class="fw-semibold">{{ ucwords(strtolower($mapel->nama_mapel)) }}</td>
                                <td class="text-center fw-bold fs-6 {{ !$nilai ? 'text-danger' : '' }}">{{ $nilai->nilai_akhir ?? '-' }}</td>
                                <td style="font-size: 0.8rem; padding: 6px;">
                                    {{ $nilai ? 'Menunjukkan penguasaan yang baik dalam kompetensi ' . strtolower($mapel->nama_mapel) . '.' : 'Belum dinilai' }}
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    <!-- KELOMPOK MATA PELAJARAN KEJURUAN / PEMINATAN -->
                    @if($kelompokKejuruan->count() > 0)
                        <tr class="bg-group"><td colspan="4">Kelompok Mata Pelajaran Kejuruan / Peminatan</td></tr>
                        @php $no = 1; @endphp
                        @foreach($kelompokKejuruan as $mapel)
                            @php $nilai = $data_nilai->firstWhere('mapel_id', $mapel->id); @endphp
                            <tr>
                                <td class="text-center">{{ $no++ }}</td>
                                <td class="fw-semibold">{{ ucwords(strtolower($mapel->nama_mapel)) }}</td>
                                <td class="text-center fw-bold fs-6 {{ !$nilai ? 'text-danger' : '' }}">{{ $nilai->nilai_akhir ?? '-' }}</td>
                                <td style="font-size: 0.8rem; padding: 6px;">
                                    {{ $nilai ? 'Menunjukkan penguasaan yang baik dalam kompetensi ' . strtolower($mapel->nama_mapel) . '.' : 'Belum dinilai' }}
                                </td>
                            </tr>
                        @endforeach
                    @endif

                </tbody>
            </table>
        </div>

        <div class="signature-section small">
            <div class="text-end mb-4 pe-5">
                Bogor, <span contenteditable="true" class="editable-date fw-bold" title="Klik untuk mengedit tanggal">{{ date('d F Y') }}</span>
            </div>
            <div class="row text-center mb-4">
                <div class="col-6">
                    <p class="mb-5">Orang Tua / Wali Murid,</p>
                    <p class="mb-0">....................................................</p>
                </div>
                <div class="col-6">
                    <p class="mb-5">Wali Kelas,</p>
                    <p class="mb-0 fw-bold text-decoration-underline">{{ auth()->user()->name ?? 'Wali Kelas' }}</p>
                    <p class="mb-0 text-muted" style="font-size: 0.75rem;">NIP. ........................................</p>
                </div>
            </div>
            
            <div class="row text-center mt-3">
                <div class="col-12">
                    <p class="mb-5">Mengetahui,<br>Kepala Sekolah</p>
                    
                    <p class="mb-0 fw-bold text-decoration-underline">
                        <span contenteditable="true" class="editable-date" title="Klik untuk mengetik Nama Kepala Sekolah">{{ $nama_kepsek }}</span>
                    </p>
                    
                    <p class="mb-0 text-muted" style="font-size: 0.75rem;">NIP. 
                        <span contenteditable="true" class="editable-date" title="Klik untuk mengetik NIP">-</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>