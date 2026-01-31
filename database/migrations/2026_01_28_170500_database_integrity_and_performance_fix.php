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
        // 1. Fix Relasi Gantung di Tabel jadwals
        Schema::table('jadwals', function (Blueprint $table) {
            $table->foreign('tahun_id')->references('id')->on('tahuns')->onDelete('cascade');
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->onDelete('cascade');
            $table->foreign('mapel_id')->references('id')->on('mapels')->onDelete('cascade');
        });

        // 2. Fix Relasi Gantung di Tabel mapels
        Schema::table('mapels', function (Blueprint $table) {
            $table->foreign('jurusan_id')->references('id')->on('jurusans')->onDelete('set null');
        });

        // 3. Fix Relasi Gantung di Tabel jenispilihans
        Schema::table('jenispilihans', function (Blueprint $table) {
            $table->foreign('pilihan_id')->references('id')->on('pilihans')->onDelete('cascade');
        });

        // 4. Hapus Kolom Redundansi di pegawais
        Schema::table('pegawais', function (Blueprint $table) {
            if (Schema::hasColumn('pegawais', 'password')) {
                $table->dropColumn('password');
            }
        });

        // 5. Optimasi Performa Pencarian (Indexing)
        Schema::table('siswas', function (Blueprint $table) {
            $table->index('nama');
            $table->index('rombelsaatini');
        });
        
        Schema::table('pegawais', function (Blueprint $table) {
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['tahun_id']);
            $table->dropForeign(['pegawai_id']);
            $table->dropForeign(['mapel_id']);
        });

        Schema::table('mapels', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
        });

        Schema::table('jenispilihans', function (Blueprint $table) {
            $table->dropForeign(['pilihan_id']);
        });

        Schema::table('pegawais', function (Blueprint $table) {
            $table->string('password', 50)->nullable();
            $table->dropIndex(['name']);
        });

        Schema::table('siswas', function (Blueprint $table) {
            $table->dropIndex(['nama']);
            $table->dropIndex(['rombelsaatini']);
        });
    }
};
