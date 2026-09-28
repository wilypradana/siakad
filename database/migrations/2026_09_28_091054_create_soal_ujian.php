<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('soal_ujian', function (Blueprint $table) {
            $table->id();
            
            // Kolom penghubung ke tabel ujians
            $table->foreignId('ujian_id')->constrained('ujians')->onDelete('cascade');
            
            // Kolom penghubung ke tabel soals
            $table->foreignId('soal_id')->constrained('soals')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_ujian');
    }
};
