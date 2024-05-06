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
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id');
            $table->foreignId('kelas_id');
            $table->foreignId('pegawai_id');
            $table->foreignId('mapel_id');
            $table->string('hari', 10);
            $table->integer('jam');
            $table->time('mulai');
            $table->time('akhir');
            $table->string('status', 100);
            $table->string('ket');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
