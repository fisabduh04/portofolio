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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logbook_id')->constrained('logbooks')->onDelete('cascade');
            $table->foreignId('siswa_id')->index()->constrained('siswas')->onDelete('cascade');
            $table->enum('status', ['Hadir', 'Sakit', 'Izin', 'Alpha', 'Pulang'])->index()->default('Hadir');
            $table->text('keterangan')->nullable(); // Diubah ke text agar bisa mencatat alasan lebih detil
            $table->timestamps();
            
            // Index komposit untuk mempercepat laporan bulanan/semester per siswa
            $table->index(['siswa_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
