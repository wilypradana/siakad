<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
        {
            $credentials = $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                $role = Auth::user()->role;

                // SISTEM PENGATUR LALU LINTAS ROLE
                if ($role == 'admin') {
                    return redirect()->intended('/admin/dashboard');
                } elseif ($role == 'guru') {
                    // (Tambahan opsional) Cek jika guru ini Wali Kelas
                    $cek_wali = \App\Models\Kelas::where('guru_id', \App\Models\Guru::where('user_id', Auth::id())->value('id'))->exists();
                    if($cek_wali){
                        // Bisa diarahkan ke dashboard khusus wali kelas atau dashboard guru biasa dengan menu tambahan
                        return redirect()->intended('/wali-kelas/dashboard'); 
                    }
                    return redirect()->intended('/guru/dashboard');
                } elseif ($role == 'siswa') {
                    return redirect()->intended('/siswa/dashboard');
                }
            }

            return back()->with('error', 'Username atau Password salah!');
        }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}