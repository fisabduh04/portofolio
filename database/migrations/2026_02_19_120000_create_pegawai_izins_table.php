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
        Schema::create('pegawai_izins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained()->cascadeOnDelete();
            $table->enum('jenis_izin', ['Sakit', 'Izin', 'Cuti', 'Dinas Luar']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->text('keterangan')->nullable();
            $table->string('bukti_dokumen')->nullable();
            $table->enum('status_approval', ['Pending', 'Approved', 'Rejected'])->default('Approved'); // Default Approved for Admin input
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_izins');
    }
};
