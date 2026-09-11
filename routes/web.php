<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, AdminController, GuruController, SiswaController, 
    MapelController, JadwalController, KelasController, NilaiController, 
    UjianController, PortalGuruController, WaliKelasController, PortalSiswaController, 
    PembelajaranController,
};

// --- AUTH ---
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- GRUP ADMIN ---
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Guru
    Route::get('/guru', [GuruController::class, 'index'])->name('admin.guru.index');
    Route::post('/guru', [GuruController::class, 'store'])->name('admin.guru.store');
    Route::post('/guru/import', [GuruController::class, 'import'])->name('admin.guru.import');
    Route::get('/guru/sync-password', [GuruController::class, 'syncPasswordToNip'])->name('admin.guru.sync_password');
    Route::get('/guru/cetak-kartu-massal', [GuruController::class, 'cetakKartuMassal'])->name('admin.guru.cetak_kartu_massal');
    Route::get('/guru/cetak-kartu/{id}', [GuruController::class, 'cetakKartu'])->name('admin.guru.cetak_kartu');
    Route::put('/guru/{id}/password', [GuruController::class, 'updatePassword'])->name('guru.update_password');
    Route::put('/guru/{id}', [GuruController::class, 'update'])->name('admin.guru.update');
    Route::delete('/guru/{id}', [GuruController::class, 'destroy'])->name('admin.guru.destroy');
    
    // Siswa
    Route::get('/siswa', [SiswaController::class, 'index'])->name('admin.siswa.index');
    Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/export', [SiswaController::class, 'export'])->name('siswa.export');
    Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
    Route::delete('/siswa/bulk-delete', [SiswaController::class, 'bulkDelete'])->name('siswa.bulkDelete');
    Route::post('/siswa/sync-password', [SiswaController::class, 'syncPasswordToNis'])->name('siswa.sync_password');
    Route::post('/siswa/bulk-lunas', [SiswaController::class, 'bulkLunas'])->name('siswa.bulkLunas');
    Route::get('/siswa/cetak-massal', [SiswaController::class, 'cetakMassal'])->name('admin.siswa.cetak_massal');
    Route::put('/siswa/{id}', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{id}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::post('/siswa/{id}/lunas', [SiswaController::class, 'tandaiLunas'])->name('admin.siswa.lunas');
    Route::post('/siswa/{id}/batal-lunas', [SiswaController::class, 'batalLunas'])->name('admin.siswa.batal_lunas');

    // Mapel
    Route::get('/mapel', [MapelController::class, 'index'])->name('admin.mapel.index');
    Route::post('/mapel', [MapelController::class, 'store'])->name('admin.mapel.store');
    Route::put('/mapel/{id}', [MapelController::class, 'update'])->name('admin.mapel.update');
    Route::delete('/mapel/{id}', [MapelController::class, 'destroy'])->name('admin.mapel.destroy');
    Route::post('/mapel/import', [MapelController::class, 'import'])->name('mapel.import');
    
    // Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('admin.kelas.index');
    Route::post('/kelas', [KelasController::class, 'store'])->name('admin.kelas.store');
    Route::get('/kelas/{id}', [KelasController::class, 'show'])->name('admin.kelas.show');
    Route::put('/kelas/{id}', [KelasController::class, 'update'])->name('admin.kelas.update');
    Route::delete('/kelas/{id}', [KelasController::class, 'destroy'])->name('admin.kelas.destroy');

    // Jadwal
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('admin.jadwal.index');
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::get('/jadwal/export', [JadwalController::class, 'export'])->name('jadwal.export');
    Route::post('/jadwal/import', [JadwalController::class, 'import'])->name('jadwal.import');
    Route::put('/jadwal/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

    // Set Pembelajaran
    Route::get('/pembelajaran', [PembelajaranController::class, 'index'])->name('pembelajaran.index');
    Route::post('/pembelajaran/simpan', [PembelajaranController::class, 'storeMassal'])->name('pembelajaran.storeMassal');
    Route::post('/pembelajaran/copy', [PembelajaranController::class, 'copyData'])->name('pembelajaran.copy');
    Route::get('/pembelajaran/by-guru', [PembelajaranController::class, 'createByGuru'])->name('pembelajaran.by_guru');
    Route::post('/pembelajaran/by-guru', [PembelajaranController::class, 'storeByGuru'])->name('pembelajaran.store_by_guru');
    
    // Nilai
    Route::get('/nilai', [NilaiController::class, 'index'])->name('admin.nilai.index');
    Route::post('/nilai', [NilaiController::class, 'store'])->name('nilai.store');
    Route::put('/nilai/{id}', [NilaiController::class, 'update'])->name('nilai.update');
    Route::delete('/nilai/{id}', [NilaiController::class, 'destroy'])->name('nilai.destroy');

    // Halaman Ujian Admin
    Route::get('/ujian', [UjianController::class, 'index'])->name('admin.ujian.index');
    
    // Master Jenis Ujian (Hanya bisa ditambah/dihapus Admin)
    Route::post('/ujian/jenis', [UjianController::class, 'storeJenis'])->name('ujian.jenis.store');
    Route::put('/ujian/jenis/{id}', [UjianController::class, 'updateJenis'])->name('ujian.jenis.update');
    Route::delete('/ujian/jenis/{id}', [UjianController::class, 'destroyJenis'])->name('ujian.jenis.destroy');
});

// --- RUTE BERSAMA ADMIN & GURU ---
Route::middleware(['auth', 'role:admin,guru'])->group(function () {
    
    // API AJAX yang bisa diakses Admin dan Guru
    Route::get('/api/ujian/get-kelas', [UjianController::class, 'getKelasByMapel'])->name('ujian.get_kelas');

    // Rute Aksi Ujian (Simpan, Hapus, Update) yang dipakai bersama oleh form
    Route::prefix('aksi-ujian')->group(function () {
        Route::post('/store', [UjianController::class, 'store'])->name('ujian.store');
        Route::delete('/bulk-delete', [UjianController::class, 'bulkDelete'])->name('ujian.bulkDelete');
        Route::put('/{id}/update', [UjianController::class, 'update'])->name('ujian.update');
        Route::delete('/{id}/destroy', [UjianController::class, 'destroy'])->name('ujian.destroy');
        Route::post('/{id}/reset-siswa', [UjianController::class, 'resetStatusUjian'])->name('ujian.reset_siswa');
    });

    Route::prefix('guru')->group(function () {
        Route::get('/dashboard', [PortalGuruController::class, 'dashboard'])->name('guru.dashboard');
        Route::get('/input-nilai/{kelas_id}/{mapel_id}', [PortalGuruController::class, 'inputNilai'])->name('guru.input_nilai');
        Route::post('/simpan-nilai/{kelas_id}/{mapel_id}', [PortalGuruController::class, 'simpanNilai'])->name('guru.simpan_nilai');
    
        // INI ADALAH RUTE YANG SEBELUMNYA HILANG
        Route::get('/ujian-list', [UjianController::class, 'index'])->name('ujian.index');
    });

    Route::prefix('wali-kelas')->group(function () {
        Route::get('/dashboard', [WaliKelasController::class, 'index'])->name('wali.dashboard');
        Route::get('/cetak-rapor/{siswa_id}', [WaliKelasController::class, 'cetak'])->name('wali.cetak');
        Route::post('/rapor/cetak', [WaliKelasController::class, 'cetakRapor'])->name('wali.cetak_rapor');
        Route::get('/wali-kelas/legger', [WaliKelasController::class, 'lihatLegger'])->name('wali.lihat_legger');
        Route::post('/wali-kelas/export-rekap', [WaliKelasController::class, 'exportRekap'])->name('wali.export_rekap');
    });
});

// --- RUTE SISWA ---
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->group(function () {
    Route::get('/dashboard', [PortalSiswaController::class, 'dashboard'])->name('siswa.dashboard');
    Route::get('/cetak-kartu', [PortalSiswaController::class, 'cetakKartu'])->name('siswa.cetak_kartu');
    Route::get('/ujian/{id}', [PortalSiswaController::class, 'kerjakanUjian'])->name('siswa.ujian.kerjakan');
    Route::post('/ujian/{id}/simpan', [PortalSiswaController::class, 'simpanJawaban'])->name('siswa.ujian.simpan');
    Route::post('/ujian/{id}/verifikasi', [PortalSiswaController::class, 'verifikasiKode'])->name('siswa.ujian.verifikasi');
    Route::post('/siswa/ujian/{id}/selesai', [PortalSiswaController::class, 'selesaiUjian'])->name('siswa.ujian.selesai');
});