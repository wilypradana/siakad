<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianSiswa extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk membuka gembok keamanan Mass Assignment
    protected $fillable = [
        'ujian_id',
        'siswa_id',
        'is_selesai'
    ];

    // Opsional: Boleh ditambahkan relasinya sekalian
    public function ujian() {
        return $this->belongsTo(Ujian::class);
    }

    public function siswa() {
        return $this->belongsTo(Siswa::class);
    }
}