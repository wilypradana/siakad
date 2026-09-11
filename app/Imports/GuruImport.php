<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Guru;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuruImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Pastikan baris NIP dan Nama tidak kosong
        if (empty($row['nip']) || empty($row['nama'])) {
            return null;
        }

        // Cek jika akun dengan username/NIP ini sudah ada, lewati agar tidak error duplicate
        $existingUser = User::where('username', $row['nip'])->first();
        if ($existingUser) {
            return null;
        }

        return DB::transaction(function () use ($row) {
            // 1. Buat Akun User
            $user = User::create([
                'name'     => $row['nama'],
                'username' => $row['nip'],
                'email'    => $row['nip'] . '@sekolah.com',
                'password' => Hash::make('guru123'), // Password default
                'role'     => 'guru',
            ]);

            // 2. Buat Profil Guru
            return Guru::create([
                'user_id'      => $user->id,
                'nip'          => $row['nip'],
                'nama'         => $row['nama'],
                'no_hp'        => $row['no_hp'] ?? null,
                'alamat'       => $row['alamat'] ?? null,
                'status_aktif' => 1,
            ]);
        });
    }
}