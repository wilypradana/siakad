<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    protected $fillable = [
        'judul_ujian', 
        'kelas_id', 
        'mapel_id', 
        'metode_ujian', 
        'link_gform',   
        'waktu_mulai', 
        'waktu_selesai', 
        'durasi',
        'jenis_ujian_id', 
        'guru_id',
        'is_active',
    ];

    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function mapel() { return $this->belongsTo(Mapel::class); }
    public function jenisUjian() { return $this->belongsTo(JenisUjian::class, 'jenis_ujian_id'); }
    public function guru() { return $this->belongsTo(Guru::class); }
    public function soals() { return $this->belongsToMany(Soal::class, 'soal_ujian', 'ujian_id', 'soal_id');}
}