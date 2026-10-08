<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanRapor extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'jenis_ujian_id',
        'sakit',
        'izin',
        'alfa',
        'catatan_wali_kelas'
    ];
}