<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelajaran extends Model
{
    use HasFactory;

    // Menyesuaikan dengan nama tabel di database
    protected $table = 'pembelajarans';

    // Mengizinkan semua kolom untuk diisi secara massal
    protected $guarded = [];

    // Relasi ke tabel kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke tabel mapel
    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    // Relasi ke tabel guru
    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}