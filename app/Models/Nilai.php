<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $fillable = [
    'siswa_id', 
    'mapel_id', 
    'nilai_tugas', 
    'nilai_uts', 
    'nilai_uas', 
    'nilai_akhir', 
    'jenis_ujian_id', 
    'sumatif_1', 
    'sumatif_2', 
    'sumatif_3', 
    'nilai_ujian',
    'deskripsi_tercapai',    // Tambahkan ini
    'deskripsi_peningkatan'  // Tambahkan ini
];

    // Relasi ke Siswa
    public function siswa() { return $this->belongsTo(Siswa::class); }
    
    // Relasi ke mapel
    public function mapel() { return $this->belongsTo(Mapel::class); }

    // relasi ke ujian
    public function jenisUjian() { return $this->belongsTo(JenisUjian::class, 'jenis_ujian_id'); }
}