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
        Schema::table('ujians', function (Blueprint $table) {
        $table->enum('metode_ujian', ['gform', 'cbt'])->default('gform')->after('judul_ujian');
        $table->string('link_gform')->nullable()->change(); // Pastikan link boleh kosong saat mode CBT
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ujians', function (Blueprint $table) {
            //
        });
    }
};
