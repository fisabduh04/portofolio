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
        // Update di tabel kelas: hubungkan ke jurusan agar bisa cascade delete
        Schema::table('kelas', function (Blueprint $table) {
            $table->foreign('jurusan_id')
                ->references('id')
                ->on('jurusans')
                ->onDelete('cascade');
        });

        // Update di tabel jadwals: hubungkan ke kelas agar bisa cascade delete
        Schema::table('jadwals', function (Blueprint $table) {
            $table->foreign('kelas_id')
                ->references('id')
                ->on('kelas')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropForeign(['jurusan_id']);
        });

        Schema::table('jadwals', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
        });
    }
};
