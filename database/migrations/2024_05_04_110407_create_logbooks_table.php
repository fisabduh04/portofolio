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
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori', ['mapel', 'piket_masuk', 'piket_pulang'])->default('mapel');
            $table->date('tanggal')->nullable();
            $table->foreignId('jadwal_id')->nullable()->constrained('jadwals');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('cascade');
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais');
            $table->text('materi')->nullable();
            $table->text('catatan')->nullable();
            $table->longText('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logbooks');
    }
};
