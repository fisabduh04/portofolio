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
        Schema::create('attendance_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('rule_type', ['Standard', 'Teacher'])->default('Standard');
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->time('scan_masuk_start');
            $table->time('scan_pulang_end');
            $table->integer('toleransi_telat')->default(0); // Menit
            $table->decimal('bantuan_makan', 10, 2)->default(0);
            $table->decimal('gaji_harian', 10, 2)->default(0);
            $table->decimal('gaji_per_jam', 10, 2)->nullable();
            $table->json('hari_kerja')->nullable(); // Default Mon-Fri
            $table->decimal('denda_telat', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_rules');
    }
};
