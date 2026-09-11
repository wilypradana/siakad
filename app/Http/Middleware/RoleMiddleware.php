<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Menggunakan ...$roles agar bisa menerima banyak role sekaligus (array)
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. Cek apakah user benar-benar sudah login
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Ambil role dari database, lalu jadikan huruf kecil semua 
        // (Ini sangat ampuh untuk mencegah error jika di database tertulis 'Guru' dengan G besar)
        $userRole = strtolower(Auth::user()->role);

        // 3. Pastikan role dalam bentuk array dicari dengan benar
        // Kita juga pastikan array $roles yang dilempar dari web.php semuanya huruf kecil
        $allowedRoles = array_map('strtolower', $roles);

        // 4. Jika role user TIDAK ADA di dalam daftar yang diizinkan, tolak!
        if (!in_array($userRole, $allowedRoles)) {
            // Sengaja disamakan dengan pesan error di gambar Bapak
            return redirect('/')->with('error', 'Akses ditolak! Anda tidak memiliki izin.'); 
        }

        // Jika aman, persilakan masuk
        return $next($request);
    }
}