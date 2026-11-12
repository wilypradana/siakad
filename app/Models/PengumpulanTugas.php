<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumpulanTugas extends Model
{
    use HasFactory;

    protected $table = 'pengumpulan_tugas';
    
    // Menyesuaikan dengan kolom yang sudah ada di phpMyAdmin Anda
    protected $fillable = ['tugas_id', 'siswa_id', 'link_jawaban', 'catatan_siswa', 'nilai', 'catatan_guru'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'tugas_id');
    }
}