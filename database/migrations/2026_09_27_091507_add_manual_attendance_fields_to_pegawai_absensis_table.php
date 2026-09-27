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
        Schema::table('pegawai_absensis', function (Blueprint $table) {
            $table->boolean('is_manual')->default(false);
            $table->string('keterangan', 1000)->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai_absensis', function (Blueprint $table) {
            $table->dropColumn(['is_manual', 'keterangan', 'updated_by']);
        });
    }
};
