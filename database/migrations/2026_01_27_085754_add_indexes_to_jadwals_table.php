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
        Schema::table('jadwals', function (Blueprint $table) {
            $table->index('tahun_id');
            $table->index('kelas_id');
            $table->index('pegawai_id');
            $table->index('mapel_id');
            $table->index(['hari', 'tahun_id']);
            $table->index(['mulai', 'akhir']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropIndex(['tahun_id']);
            $table->dropIndex(['kelas_id']);
            $table->dropIndex(['pegawai_id']);
            $table->dropIndex(['mapel_id']);
            $table->dropIndex(['hari', 'tahun_id']);
            $table->dropIndex(['mulai', 'akhir']);
        });
    }
};
