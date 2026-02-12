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
        if (!Schema::hasColumn('logbooks', 'jadwal_id')) {
            Schema::table('logbooks', function (Blueprint $table) {
                $table->foreignId('jadwal_id')->nullable()->constrained('jadwals');
            });
        } else {
            Schema::table('logbooks', function (Blueprint $table) {
                $table->foreignId('jadwal_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            // WARNING: Reverting this might fail if there are records with null jadwal_id
            $table->foreignId('jadwal_id')->nullable(false)->change();
        });
    }
};
