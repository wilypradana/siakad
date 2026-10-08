<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('nilais', function (Blueprint $table) {
            $table->text('deskripsi_tercapai')->nullable()->after('nilai_akhir');
            $table->text('deskripsi_peningkatan')->nullable()->after('deskripsi_tercapai');
        });
    }

    public function down()
    {
        Schema::table('nilais', function (Blueprint $table) {
            $table->dropColumn(['deskripsi_tercapai', 'deskripsi_peningkatan']);
        });
    }
};