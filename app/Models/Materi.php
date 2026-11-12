<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $fillable = [
        'guru_id', 'mapel_id', 'kelas_id', 'judul_materi', 'deskripsi', 'link_materi','is_tugas'
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
}