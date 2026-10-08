<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('catatan_rapors', function (Blueprint $table) {
            $table->id();
            // Menyambungkan data dengan siswa dan semester/jenis ujian
            $table->unsignedBigInteger('siswa_id');
            $table->unsignedBigInteger('jenis_ujian_id')->nullable();
            
            // Kolom kehadiran dan catatan
            $table->integer('sakit')->nullable()->default(0);
            $table->integer('izin')->nullable()->default(0);
            $table->integer('alfa')->nullable()->default(0);
            $table->text('catatan_wali_kelas')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('catatan_rapors');
    }
};