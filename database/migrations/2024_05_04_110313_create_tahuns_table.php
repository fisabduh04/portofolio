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
        Schema::create('tahuns', function (Blueprint $table) {
            $table->id();
            $table->string('tahun')->nullable();
            $table->string('semester')->nullable();
            $table->date('tanggalmulai')->nullable();
            $table->date('tanggalakhir')->nullable();
            $table->boolean('isActive')->nullable();
            $table->text('deskripsi')->nullable();
            $table->unique(['tahun', 'semester']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahuns');
    }
};
