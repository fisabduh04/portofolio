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
            $table->foreignId('tahun_id')->constrained('tahuns')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->foreignId('mapel_id')->constrained('mapels')->onDelete('cascade');
            $table->string('hari', 10)->nullable();
            $table->integer('jam');
            $table->time('mulai');
            $table->time('akhir');
            $table->string('status', 100)->nullable();
            $table->string('ket')->nullable();
            $table->timestamps();

            // Index untuk performa query
            $table->index(['hari', 'tahun_id']);
            $table->index(['mulai', 'akhir']);
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
