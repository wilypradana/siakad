<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    use HasFactory;

    // Mengizinkan semua kolom diisi (Mass Assignment) kecuali kolom ID
    protected $guarded = ['id'];

    // Relasi: Setiap soal dimiliki oleh 1 ujian
    public function ujian()
    {
        return $this->belongsTo(Ujian::class);
    }

    // Relasi: 1 soal bisa memiliki banyak rekaman jawaban dari berbagai siswa
    public function jawaban_siswas()
    {
        return $this->hasMany(JawabanSiswa::class);
    }
}