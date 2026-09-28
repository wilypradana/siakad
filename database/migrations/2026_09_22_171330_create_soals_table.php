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
    Schema::create('soals', function (Blueprint $table) {
        $table->id();
        $table->foreignId('ujian_id')->constrained('ujians')->onDelete('cascade'); // Relasi ke tabel ujians yang sudah ada
        $table->longText('pertanyaan'); // Teks soal
        $table->string('gambar')->nullable(); // Path gambar jika soal berupa gambar
        
        // Pilihan Ganda (A-E untuk level SMA/SMK)
        $table->text('opsi_a');
        $table->text('opsi_b');
        $table->text('opsi_c');
        $table->text('opsi_d');
        $table->text('opsi_e')->nullable(); 
        
        $table->char('kunci_jawaban', 1); // Berisi huruf A, B, C, D, atau E
        $table->integer('bobot_nilai')->default(1); // Bobot per soal
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};
