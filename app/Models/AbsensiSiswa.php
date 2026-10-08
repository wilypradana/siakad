<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiSiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'mapel_id',
        'kelas_id',
        'guru_id',
        'tanggal',
        'status'
    ];
}