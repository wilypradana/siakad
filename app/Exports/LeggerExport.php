<?php

namespace App\Exports;

use App\Models\Siswa;
use App\Models\Nilai;
use App\Models\Ujian;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LeggerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $kelas_id;
    protected $jenis_ujian_id;
    protected $data_mapel;
    protected $nomor = 1;

    public function __construct($kelas_id, $jenis_ujian_id)
    {
        $this->kelas_id = $kelas_id;
        $this->jenis_ujian_id = $jenis_ujian_id;

        // Ambil daftar Mapel khusus kelas ini agar kolomnya dinamis dan sesuai urutan
        $mapel_ids_kelas = DB::table('pembelajarans')
                            ->where('kelas_id', $kelas_id)
                            ->pluck('mapel_id')
                            ->toArray();

        if (empty($mapel_ids_kelas)) {
            $mapel_ids_kelas = Ujian::where('kelas_id', $kelas_id)
                                    ->pluck('mapel_id')
                                    ->toArray();
        }

        $this->data_mapel = \App\Models\Mapel::whereIn('id', $mapel_ids_kelas)
                                ->orderBy('kelompok', 'asc')
                                ->orderBy('nama_mapel', 'asc')
                                ->get();
    }

    // 1. Ambil Data Siswa yang akan dilooping (Baris)
    public function collection()
    {
        return Siswa::where('kelas_id', $this->kelas_id)->orderBy('nama', 'asc')->get();
    }

    // 2. Buat Judul Kolom (Header) Excel
    public function headings(): array
    {
        $headers = ['No', 'NIS', 'Nama Siswa'];
        
        foreach ($this->data_mapel as $mapel) {
            $headers[] = strtoupper($mapel->nama_mapel);
        }
        
        $headers[] = 'Rata-Rata';
        return $headers;
    }

    // 3. Petakan Data (Isi Baris)
    public function map($siswa): array
    {
        $row = [
            $this->nomor++,
            $siswa->nis,
            $siswa->nama
        ];

        $total_nilai = 0;
        $jumlah_mapel = 0;

        foreach ($this->data_mapel as $mapel) {
            $nilai = Nilai::where('siswa_id', $siswa->id)
                          ->where('mapel_id', $mapel->id)
                          ->where('jenis_ujian_id', $this->jenis_ujian_id)
                          ->first();

            if ($nilai) {
                $row[] = $nilai->nilai_akhir;
                $total_nilai += (float) $nilai->nilai_akhir;
                $jumlah_mapel++;
            } else {
                $row[] = '-';
            }
        }

        $rata_rata = $jumlah_mapel > 0 ? round($total_nilai / $jumlah_mapel, 2) : 0;
        $row[] = $rata_rata;

        return $row;
    }

    // 4. Atur Desain Excel
    public function styles(Worksheet $sheet)
    {
        return [
            // Baris pertama (Header) jadi tebal (bold)
            1 => ['font' => ['bold' => true]],
        ];
    }
}