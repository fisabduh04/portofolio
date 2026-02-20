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
        Schema::table('fingerprint_machines', function (Blueprint $table) {
            $table->boolean('is_scheduler_active')->default(false)->after('status');
            $table->time('scheduler_start_time')->nullable()->after('is_scheduler_active');
            $table->time('scheduler_end_time')->nullable()->after('scheduler_start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fingerprint_machines', function (Blueprint $table) {
            $table->dropColumn(['is_scheduler_active', 'scheduler_start_time', 'scheduler_end_time']);
        });
    }
};
