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
        Schema::create('fingerprint_machines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ip_address')->unique();
            $table->integer('port')->default(4370);
            $table->integer('comkey')->default(0);
            $table->string('location')->nullable();
            $table->boolean('status')->default(true); // Active/Inactive
            $table->boolean('is_scheduler_active')->default(false);
            $table->time('scheduler_start_time')->nullable();
            $table->time('scheduler_end_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fingerprint_machines');
    }
};
