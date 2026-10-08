<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Rapor Massal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <style>
    body {
        font-family: 'Times New Roman', Times, serif;
        color: #000;
        background-color: #fff;
    }
    .lembar-rapor { width: 194mm; margin: 0 auto 30px auto; font-size: var(--fs, 11px); }
    .lembar-rapor .small { font-size: 1em !important; }
    .lembar-rapor > .text-center { margin-bottom: 0.8em !important; }
    .lembar-rapor > .text-center h5 { font-size: 1.25em; margin-bottom: 0.2em; }
    .lembar-rapor > .text-center h6 { font-size: 1.1em; margin-bottom: 0.2em; }
    .lembar-rapor > .text-center hr { margin: 0.4em 0 !important; }
    .lembar-rapor > .row.small.mb-4 { margin-bottom: 0.8em !important; }
    .lembar-rapor > .row.small.mb-4 table { font-size: 1em; }
    .lembar-rapor > .row.small.mb-4 td { padding: 0.15em 0.3em !important; }
    .lembar-rapor > .mb-4 { margin-bottom: 0 !important; }
    .lembar-rapor > .mb-4 h6 { font-size: 1.1em !important; margin-bottom: 0.4em !important; }

    .table-rapor, .table-kehadiran { width: 100%; font-size: 1em !important; margin-bottom: 0 !important; }
    .table-rapor th, .table-rapor td, .table-kehadiran td { border: 1px solid #000 !important; padding: 0.3em 0.4em !important; line-height: 1.2 !important; }
    .table-rapor .fs-6 { font-size: 1.05em !important; }
    .table-rapor td[style] { font-size: 0.95em !important; padding: 0.3em 0.4em !important; }

    .bg-group { background-color: #f2f2f2 !important; font-weight: bold; }
    .signature-section { margin-top: 1em !important; break-inside: avoid; page-break-inside: avoid; }
    .signature-section > .text-end { margin-bottom: 0.8em !important; padding-right: 2em !important; }
    .signature-section .row.text-center.mb-4 { margin-bottom: 0.5em !important; }
    .signature-section p { font-size: 1em !important; line-height: 1.2 !important; }
    .signature-section .mb-5 { margin-bottom: 4em !important; }
    .signature-section .row.text-center.mt-3 { margin-top: 0.5em !important; }

    /* Style khusus elemen yang bisa diedit */
    .editable-sync {
        border-bottom: 1px dashed #000;
        padding: 0 4px;
        cursor: pointer;
        background-color: #fff9db;
        transition: 0.3s;
    }
    .editable-sync:hover { background-color: #ffe066; }

    @media print {
        @page { size: A4 portrait; margin: 8mm; }
        html, body { margin: 0 !important; padding: 0 !important; background: #fff !important; }
        .no-print { display: none !important; }
        .lembar-rapor { margin: 0 auto !important; page-break-after: always; break-after: page; page-break-inside: avoid; break-inside: avoid; }
        .lembar-rapor:last-child { page-break-after: auto; break-after: auto; }
        .table-rapor, .table-kehadiran, .bg-group { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .editable-sync { border: none !important; background: transparent !important; }
    }
</style>
</head>
<body class="p-4 bg-white">
    
    <div class="no-print text-center mb-3">
        <div class="alert alert-warning d-inline-block small py-2">
            <i class="fas fa-info-circle"></i> <b>Tips:</b> Mengubah tanggal, status kenaikan, nama kepsek, atau NIP di halaman siswa pertama akan otomatis merubah seluruh rapor siswa di kelas ini.
        </div>
        <br>
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Cetak Seluruh Kelas</button>
        <button onclick="window.close()" class="btn btn-danger">Tutup</button>
    </div>

    <!-- LOOPING UNTUK SETIAP SISWA -->
    @foreach($kumpulan_siswa as $siswa)
        @php
            $data_nilai = $semua_nilai->where('siswa_id', $siswa->id);

            $nama_kelas = strtoupper($siswa->kelas->nama_kelas ?? '');
            $nama_kelas = strtoupper($siswa->kelas->nama_kelas ?? '');
            // Deteksi SMA berdasarkan kata SMA, IPS, IPA, atau MIPA pada nama kelas
            $is_sma = strpos($nama_kelas, 'SMA') !== false || strpos($nama_kelas, 'IPS') !== false || strpos($nama_kelas, 'E') !== false || strpos($nama_kelas, 'MIPA') !== false;
            
            $nama_kepsek = $is_sma ? 'Marnis, S.Pd.' : 'Khodijah, S.Pd.I';
            $nama_instansi = $is_sma ? 'SEKOLAH MENENGAH ATAS (SMA)' : 'SEKOLAH MENENGAH KEJURUAN (SMK)';
            $nama_sekolah_singkat = $is_sma ? 'SMA' : 'SMK';
            
            $nama_jenis_lower = strtolower($jenis_ujian->nama_jenis ?? '');
            $is_sts_rapor = strpos($nama_jenis_lower, 'tengah semester') !== false || strpos($nama_jenis_lower, 'sts') !== false;
            $is_sas_rapor = strpos($nama_jenis_lower, 'akhir semester') !== false || strpos($nama_jenis_lower, 'sas') !== false;
            $is_sat_rapor = strpos($nama_jenis_lower, 'akhir tahun') !== false || strpos($nama_jenis_lower, 'sat') !== false;
        @endphp

        <!-- Kontainer pemisah halaman -->
        <div class="lembar-rapor">
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
                            <th width="10%">{{ $is_sts_rapor ? 'Nilai' : 'Nilai Akhir' }}</th>
                            @if(!$is_sts_rapor) <th width="50%">Capaian Kompetensi</th> @endif
                        </tr>
                    </thead>
                   
   <tbody>
    @php
        $no = 1;
        $mapel_ids_kelas = \Illuminate\Support\Facades\DB::table('pembelajarans')
            ->where('kelas_id', $siswa->kelas_id)->pluck('mapel_id')->filter()->unique()->values()->toArray();

        if (empty($mapel_ids_kelas)) {
            $mapel_ids_kelas = \App\Models\Ujian::where('kelas_id', $siswa->kelas_id)
                ->pluck('mapel_id')->filter()->unique()->values()->toArray();
        }

        $semua_mapel = \App\Models\Mapel::whereIn('id', $mapel_ids_kelas)->orderBy('id', 'asc')->get();

        $kelompokUmum = $semua_mapel->filter(function ($m) { return strtolower(trim($m->kelompok ?? '')) === 'umum'; });
        $kelompokKejuruan = $semua_mapel->filter(function ($m) { return strtolower(trim($m->kelompok ?? '')) === 'kejuruan'; });
        $kelompokMulok = $semua_mapel->filter(function ($m) { return in_array(strtolower(trim($m->kelompok ?? '')), ['mulok', 'muatan lokal', 'muatan local', 'lokal']); });
    @endphp

    {{-- ================= UMUM ================= --}}
    @if($kelompokUmum->isNotEmpty())
        <tr class="bg-group"><td colspan="{{ $is_sts_rapor ? 3 : 4 }}">Kelompok Mata Pelajaran Umum</td></tr>
        @foreach($kelompokUmum as $mapel)
            @php $nilai = $data_nilai->firstWhere('mapel_id', $mapel->id); @endphp
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td class="fw-semibold">{{ ucwords(strtolower($mapel->nama_mapel)) }}</td>
                <td class="text-center fw-bold fs-6 {{ !$nilai ? 'text-danger' : '' }}">
                    {{ $nilai ? ($is_sts_rapor ? $nilai->nilai_ujian : $nilai->nilai_akhir) : '-' }}
                </td>
                @if(!$is_sts_rapor)
                    <td style="font-size: 0.8rem; padding: 6px;">
                        @if($nilai)
                            <div contenteditable="true" style="background: transparent; border: none; padding: 0; min-height: 20px;" title="Klik untuk mengedit capaian khusus mapel ini">
                                @if(!empty($nilai->deskripsi_tercapai) || !empty($nilai->deskripsi_peningkatan))
                                    {{ $nilai->deskripsi_tercapai ? 'Menunjukkan penguasaan yang baik dalam kompetensi ' . $nilai->deskripsi_tercapai . '. ' : '' }}
                                    {{ $nilai->deskripsi_peningkatan ? 'Perlu bimbingan dalam kompetensi ' . $nilai->deskripsi_peningkatan . '.' : '' }}
                                @else
                                    Menunjukkan penguasaan yang baik dalam kompetensi {{ strtolower($mapel->nama_mapel) }}.
                                @endif
                            </div>
                        @else
                            Belum dinilai
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    @endif

    {{-- ================= KEJURUAN ================= --}}
    @if($kelompokKejuruan->isNotEmpty())
        <tr class="bg-group"><td colspan="{{ $is_sts_rapor ? 3 : 4 }}">Kelompok Mata Pelajaran Kejuruan</td></tr>
        @foreach($kelompokKejuruan as $mapel)
            @php $nilai = $data_nilai->firstWhere('mapel_id', $mapel->id); @endphp
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td class="fw-semibold">{{ ucwords(strtolower($mapel->nama_mapel)) }}</td>
                <td class="text-center fw-bold fs-6 {{ !$nilai ? 'text-danger' : '' }}">
                    {{ $nilai ? ($is_sts_rapor ? $nilai->nilai_ujian : $nilai->nilai_akhir) : '-' }}
                </td>
                @if(!$is_sts_rapor)
                    <td style="font-size: 0.8rem; padding: 6px;">
                        @if($nilai)
                            <div contenteditable="true" style="background: transparent; border: none; padding: 0; min-height: 20px;" title="Klik untuk mengedit capaian khusus mapel ini">
                                @if(!empty($nilai->deskripsi_tercapai) || !empty($nilai->deskripsi_peningkatan))
                                    {{ $nilai->deskripsi_tercapai ? 'Menunjukkan penguasaan yang baik dalam kompetensi ' . $nilai->deskripsi_tercapai . '. ' : '' }}
                                    {{ $nilai->deskripsi_peningkatan ? 'Perlu bimbingan dalam kompetensi ' . $nilai->deskripsi_peningkatan . '.' : '' }}
                                @else
                                    Menunjukkan penguasaan yang baik dalam kompetensi {{ strtolower($mapel->nama_mapel) }}.
                                @endif
                            </div>
                        @else
                            Belum dinilai
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    @endif

    {{-- ================= MUATAN LOKAL ================= --}}
    @if($kelompokMulok->isNotEmpty())
        <tr class="bg-group"><td colspan="{{ $is_sts_rapor ? 3 : 4 }}">Kelompok Mata Pelajaran Muatan Lokal</td></tr>
        @foreach($kelompokMulok as $mapel)
            @php $nilai = $data_nilai->firstWhere('mapel_id', $mapel->id); @endphp
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td class="fw-semibold">{{ ucwords(strtolower($mapel->nama_mapel)) }}</td>
                <td class="text-center fw-bold fs-6 {{ !$nilai ? 'text-danger' : '' }}">
                    {{ $nilai ? ($is_sts_rapor ? $nilai->nilai_ujian : $nilai->nilai_akhir) : '-' }}
                </td>
                @if(!$is_sts_rapor)
                    <td style="font-size: 0.8rem; padding: 6px;">
                        @if($nilai)
                            <div contenteditable="true" style="background: transparent; border: none; padding: 0; min-height: 20px;" title="Klik untuk mengedit capaian khusus mapel ini">
                                @if(!empty($nilai->deskripsi_tercapai) || !empty($nilai->deskripsi_peningkatan))
                                    {{ $nilai->deskripsi_tercapai ? 'Menunjukkan penguasaan yang baik dalam kompetensi ' . $nilai->deskripsi_tercapai . '. ' : '' }}
                                    {{ $nilai->deskripsi_peningkatan ? 'Perlu bimbingan dalam kompetensi ' . $nilai->deskripsi_peningkatan . '.' : '' }}
                                @else
                                    Menunjukkan penguasaan yang baik dalam kompetensi {{ strtolower($mapel->nama_mapel) }}.
                                @endif
                            </div>
                        @else
                            Belum dinilai
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    @endif

</tbody>
                </table>
            </div>

            {{-- KOMPONEN NON-AKADEMIK KHUSUS SAS & SAT --}}
            @if($is_sas_rapor || $is_sat_rapor)
                @php
                    $catatan_rapor = \App\Models\CatatanRapor::where('siswa_id', $siswa->id)->where('jenis_ujian_id', $jenis_ujian->id ?? null)->first();
                    $data_ekskul = \App\Models\Ekstrakurikuler::where('siswa_id', $siswa->id)->where('jenis_ujian_id', $jenis_ujian->id ?? null)->get();
                    $data_prestasi = \App\Models\Prestasi::where('siswa_id', $siswa->id)->where('jenis_ujian_id', $jenis_ujian->id ?? null)->get();
                @endphp

                <!-- B. EKSTRAKURIKULER -->
                <h6 class="fw-bold mt-3 mb-1" style="font-size: 0.85rem;">B. Ekstrakurikuler</h6>
                <table class="table-rapor table-sm mb-0">
                    <thead class="table-light text-center fw-bold">
                        <tr><th width="5%">No</th><th width="65%">Kegiatan Ekstrakurikuler</th><th width="30%">Predikat</th></tr>
                    </thead>
                    <tbody>
                        @forelse($data_ekskul as $idx => $ekskul)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>{{ $ekskul->nama_ekskul }}</td>
                                <td class="text-center fw-bold">{{ $ekskul->predikat }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Belum ada ekstrakurikuler.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- C. PRESTASI -->
                <h6 class="fw-bold mt-2 mb-1" style="font-size: 0.85rem;">C. Prestasi</h6>
                <table class="table-rapor table-sm mb-0">
                    <thead class="table-light text-center fw-bold">
                        <tr><th width="5%">No</th><th width="95%">Nama Prestasi / Penghargaan</th></tr>
                    </thead>
                    <tbody>
                        @forelse($data_prestasi as $idx => $pres)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>{{ $pres->nama_prestasi }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted">Tidak ada catatan prestasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- D. KETIDAKHADIRAN & E. CATATAN -->
                <div class="row mt-2 mb-2 small" style="break-inside: avoid;">
                    <div class="col-5">
                        <h6 class="fw-bold mb-1" style="font-size: 0.85rem;">D. Ketidakhadiran</h6>
                        <table class="table-rapor table-sm mb-0">
                            <tr><td width="60%">Sakit</td><td width="40%" class="text-center">{{ $catatan_rapor->sakit ?? '-' }} hari</td></tr>
                            <tr><td>Izin</td><td class="text-center">{{ $catatan_rapor->izin ?? '-' }} hari</td></tr>
                            <tr><td>Tanpa Keterangan</td><td class="text-center">{{ $catatan_rapor->alfa ?? '-' }} hari</td></tr>
                        </table>
                    </div>
                    <div class="col-7">
                        <h6 class="fw-bold mb-1" style="font-size: 0.85rem;">E. Catatan Wali Kelas</h6>
                        <div class="border border-dark p-2" style="min-height: 50px;">
                            {{ $catatan_rapor->catatan_wali_kelas ?? '-' }}
                        </div>
                    </div>
                </div>
            @endif

            {{-- KOTAK KENAIKAN KELAS KHUSUS SAT --}}
            @if($is_sat_rapor)
                <div class="border border-dark p-2 mb-2 text-center" style="font-size: 0.85em; break-inside: avoid;">
                    <strong>KETERANGAN KENAIKAN KELAS</strong><br>
                    Berdasarkan pencapaian seluruh kompetensi pada semester ke-1 dan ke-2, peserta didik ditetapkan:<br>
                    <div contenteditable="true" class="editable-sync edit-status fw-bold mt-1 d-inline-block" style="font-size: 1.05em;" title="Klik untuk mengubah status naik/tinggal">
                        NAIK KE KELAS XI (SEBELAS)
                    </div>
                </div>
            @endif

            <div class="signature-section small">
                <div class="text-end mb-3 pe-5">
                    Bogor, <span contenteditable="true" class="editable-sync edit-tanggal fw-bold" title="Klik untuk mengedit tanggal">{{ date('d F Y') }}</span>
                </div>
                <div class="row text-center mb-3">
                    <div class="col-6">
                        <p class="mb-5">Orang Tua / Wali Murid,</p>
                        <p class="mb-0">....................................................</p>
                    </div>
                    <div class="col-6">
                        <p class="mb-5">Wali Kelas,</p>
                        <p class="mb-0 fw-bold text-decoration-underline">{{ auth()->user()->name ?? 'Wali Kelas' }}</p>
                    </div>
                </div>
                
                <div class="row text-center mt-2">
                    <div class="col-12">
                        <p class="mb-4">Mengetahui,<br>Kepala Sekolah</p>
                        <p class="mb-0 fw-bold text-decoration-underline">
                            <span contenteditable="true" class="editable-sync edit-kepsek" title="Klik untuk mengetik Nama Kepala Sekolah">{{ $nama_kepsek }}</span>
                        </p>
                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">NIP. <span contenteditable="true" class="editable-sync edit-nip">-</span></p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    
    <script>
    // ===== SINKRONISASI MASSAL YANG AMAN & SPESIFIK =====
    function setupSync(className) {
        document.querySelectorAll('.' + className).forEach(item => {
            item.addEventListener('input', function () {
                const newValue = this.textContent;
                document.querySelectorAll('.' + className).forEach(el => {
                    if (el !== this) {
                        el.textContent = newValue;
                    }
                });
            });
        });
    }

    // Jalankan sinkronisasi terpisah untuk masing-masing bagian penting
    setupSync('edit-tanggal');
    setupSync('edit-kepsek');
    setupSync('edit-nip');
    setupSync('edit-status');

    // ===== Pas satu halaman A4 untuk SETIAP siswa =====
    function fitSatuHalaman() {
        const maxTinggi = (281 * 96 / 25.4) - 8;
        document.querySelectorAll('.lembar-rapor').forEach(lembar => {
            for (let fs = 15; fs >= 7; fs -= 0.25) {
                lembar.style.setProperty('--fs', fs + 'px');
                if (lembar.scrollHeight <= maxTinggi) break;
            }
        });
    }

    window.addEventListener('load', fitSatuHalaman);
    window.addEventListener('beforeprint', fitSatuHalaman);
</script>
</body>
</html>