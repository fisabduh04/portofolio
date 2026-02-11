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
        Schema::table('pegawais', function (Blueprint $table) {
            $table->foreignId('attendance_rule_id')->nullable()->constrained('attendance_rules')->nullOnDelete();
            $table->string('fingerprint_id')->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropForeign(['attendance_rule_id']);
            $table->dropColumn(['attendance_rule_id', 'fingerprint_id']);
        });
    }
};
