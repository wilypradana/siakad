<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    protected $fillable = [
        'judul_ujian', 'kelas_id', 'mapel_id', 'link_gform', 
        'waktu_mulai', 'waktu_selesai', 'jenis_ujian_id', 'guru_id'
    ];

    public function kelas() { return $this->belongsTo(Kelas::class); }
    public function mapel() { return $this->belongsTo(Mapel::class); }
    public function jenisUjian() { return $this->belongsTo(JenisUjian::class, 'jenis_ujian_id'); }
    public function guru() { return $this->belongsTo(Guru::class); }
}