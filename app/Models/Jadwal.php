<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id', 
        'mapel_id', 
        'guru_id', 
        'hari', 
        'jam_mulai', 
        'jam_selesai'
    ];

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    // Relasi ke Mapel
    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    // Relasi ke Guru
    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }
}