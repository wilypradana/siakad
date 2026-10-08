<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ekstrakurikuler extends Model
{
    protected $fillable = ['siswa_id', 'jenis_ujian_id', 'nama_ekskul', 'predikat', 'keterangan'];
}
