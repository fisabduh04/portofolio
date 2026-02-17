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
            $table->string('attendance_source')->nullable()->after('status')->comment('Fingerprint, Manual, Event, System');
            $table->unsignedBigInteger('created_by')->nullable()->after('updated_at'); // Audit trail
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawai_absensis', function (Blueprint $table) {
            $table->dropColumn(['attendance_source', 'created_by']);
        });
    }
};
