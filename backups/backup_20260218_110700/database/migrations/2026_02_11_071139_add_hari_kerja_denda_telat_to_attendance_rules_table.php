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
        Schema::table('attendance_rules', function (Blueprint $table) {
            // Default to Mon-Fri if not specified. Using text/json for flexibility.
            $table->json('hari_kerja')->nullable()->after('gaji_per_jam'); 
            // Default to 0 penalty
            $table->decimal('denda_telat', 10, 2)->default(0)->after('hari_kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_rules', function (Blueprint $table) {
            $table->dropColumn(['hari_kerja', 'denda_telat']);
        });
    }
};
