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
        Schema::create('pegawai_absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();
            $table->time('durasi_kerja')->nullable();
            $table->string('status')->nullable(); // Hadir, Telat, Alpha, Izin
            $table->string('attendance_source')->nullable()->comment('Fingerprint, Manual, Event, System');
            $table->decimal('nominal_gaji', 10, 2)->default(0);
            $table->decimal('nominal_makan', 10, 2)->default(0);
            $table->decimal('total_honor', 10, 2)->default(0);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable(); // Audit trail

            $table->unique(['pegawai_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_absensis');
    }
};
