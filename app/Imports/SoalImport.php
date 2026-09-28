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
    $ujian = Ujian::findOrFail($this->ujian_id);

    foreach ($rows as $index => $row) {

        try {

            if (empty($row['pertanyaan'])) {
                continue;
            }

            $soal = Soal::create([
                'ujian_id'      => $this->ujian_id,
                'pertanyaan'    => $row['pertanyaan'],
                'opsi_a'        => $row['opsi_a'] ?? '',
                'opsi_b'        => $row['opsi_b'] ?? '',
                'opsi_c'        => $row['opsi_c'] ?? '',
                'opsi_d'        => $row['opsi_d'] ?? '',
                'opsi_e'        => $row['opsi_e'] ?? null,
                'kunci_jawaban' => strtoupper(trim($row['kunci_jawaban'] ?? '')),
            ]);

            $ujian->soals()->attach($soal->id);

        } catch (\Throwable $e) {

            throw new \Exception(
                'Error pada baris Excel ' . ($index + 2) .
                ': ' . $e->getMessage()
            );
        }
    }
}

}