<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('absensi_siswas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('mapel_id');
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('guru_id');
            $table->date('tanggal');
            $table->enum('status', ['H', 'S', 'I', 'A'])->default('H'); // H: Hadir, S: Sakit, I: Izin, A: Alfa
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensi_siswas');
    }
};