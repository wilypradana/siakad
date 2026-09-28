<?php
namespace App\Imports;

use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SoalImport implements ToCollection, WithHeadingRow
{
    protected $ujian_id;

    public function __construct($ujian_id)
    {
        $this->ujian_id = $ujian_id;
    }

    public function collection(Collection $rows)
    {
        $ujian = Ujian::find($this->ujian_id);

        foreach ($rows as $row) {
            // Lewati baris jika pertanyaan kosong
            if (!isset($row['pertanyaan'])) {
                continue;
            }$soal = Soal::create([
                'ujian_id'      => $this->ujian_id, // <-- TAMBAHKAN BARIS INI
                'pertanyaan'    => $row['pertanyaan'],
                'opsi_a'        => $row['opsi_a'],
                'opsi_b'        => $row['opsi_b'],
                'opsi_c'        => $row['opsi_c'],
                'opsi_d'        => $row['opsi_d'],
                'opsi_e'        => $row['opsi_e'] ?? null,
                'kunci_jawaban' => strtoupper($row['kunci_jawaban']),
            ]);

            // 2. Tempelkan ID soal ke ujian via pivot
            $ujian->soals()->attach($soal->id);
        }
    }
}