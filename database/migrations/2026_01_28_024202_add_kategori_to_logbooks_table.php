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
        Schema::table('logbooks', function (Blueprint $table) {
            $table->enum('kategori', ['mapel', 'piket_masuk', 'piket_pulang'])->default('mapel')->after('id');
            $table->foreignId('kelas_id')->nullable()->after('jadwal_id')->constrained('kelas')->onDelete('cascade');
            // Make jadwal_id nullable for picket logs
            $table->unsignedBigInteger('jadwal_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->dropColumn(['kategori', 'kelas_id']);
            $table->unsignedBigInteger('jadwal_id')->nullable(false)->change();
        });
    }
};
