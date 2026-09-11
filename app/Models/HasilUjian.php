<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class HasilUjian extends Model
{
    protected $fillable = ['ujian_id', 'siswa_id', 'jumlah_benar', 'jumlah_salah', 'nilai_akhir'];
}