<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    // Pastikan baris ini ada di DALAM kurung kurawal class
    protected $fillable = [
        'kode_mapel', 
        'nama_mapel', 
        'kelompok',
        'jurusan_mapel',
    ];
}