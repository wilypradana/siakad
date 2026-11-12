<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MateriLog extends Model
{
    use HasFactory;

    protected $table = 'materi_logs';
    protected $fillable = ['materi_id', 'siswa_id', 'read_at'];

    // Relasi ke Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // Relasi ke Materi
    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }
}