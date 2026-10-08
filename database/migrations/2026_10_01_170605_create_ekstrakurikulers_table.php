<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('ekstrakurikulers', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('siswa_id');
        $table->unsignedBigInteger('jenis_ujian_id'); // Untuk deteksi semester
        $table->string('nama_ekskul');
        $table->string('predikat'); // Sangat Baik, Baik, Cukup, Kurang
        $table->string('keterangan')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikulers');
    }
};
