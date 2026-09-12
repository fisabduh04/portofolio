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
        Schema::create('wali_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_id')->constrained('tahuns')->restrictOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->restrictOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->restrictOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('keterangan')->nullable();
            $table->unsignedTinyInteger('active_slot')->nullable()
                ->virtualAs('CASE WHEN is_active = 1 THEN 1 ELSE NULL END');
            $table->timestamps();
            $table->unique(['tahun_id', 'kelas_id', 'pegawai_id'], 'wali_kelas_assignment_unique');
            $table->unique(['tahun_id', 'kelas_id', 'active_slot'], 'wali_kelas_active_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wali_kelas');
    }
};
