<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    // Tambahkan baris sakti ini untuk membuka blokir mass assignment
    protected $fillable = [
        'user_id',
        'kelas_id',
        'nis',
        'nama',
        'status_bayar', // Pastikan ini ada
        'nomor_kartu',  // Pastikan ini ada
        'no_hp',
        'alamat',
        'nama_ortu',
        'foto',
        'status'
        
    ];

    // Relasi: Siswa dimiliki oleh satu Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    // Relasi: Siswa memiliki satu akun User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
// Relasi ke tabel Nilai (Satu siswa bisa punya banyak nilai)
    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'siswa_id');
    }
}
