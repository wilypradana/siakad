<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    // Tambahkan kolom tingkat, semester, dan tahun_ajaran
    protected $fillable = [
        'nama_kelas', 
        'jurusan', 
        'guru_id', 
        'tingkat', 
        'semester', 
        'tahun_ajaran'
    ];

    // Relasi ke Siswa (Sudah ada sebelumnya)
    public function siswa()
    {
        return $this->hasMany(Siswa::class);
    }

    // RELASI BARU: Kelas memiliki 1 Wali Kelas (Guru)
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}