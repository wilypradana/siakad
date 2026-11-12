<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id', 'mapel_id', 'kelas_id', 'judul_tugas', 'deskripsi_tugas', 'link_referensi', 'batas_waktu'
    ];

    public function guru() {
        return $this->belongsTo(Guru::class);
    }
    public function mapel() {
        return $this->belongsTo(Mapel::class);
    }
    public function kelas() {
        return $this->belongsTo(Kelas::class);
    }
    public function pengumpulan() {
        return $this->hasMany(PengumpulanTugas::class);
    }
}