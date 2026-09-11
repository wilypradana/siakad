<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk memberi izin Mass Assignment
    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'no_hp',
        'alamat',
        'status_aktif'
    ];

    // (Opsional) Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}