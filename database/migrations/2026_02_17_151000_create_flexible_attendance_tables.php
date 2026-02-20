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
        Schema::create('special_events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->decimal('bantuan_hadir', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('special_event_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('special_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['special_event_id', 'pegawai_id']);
        });

        Schema::create('pegawai_schedule_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendance_rule_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(['pegawai_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_schedule_overrides');
        Schema::dropIfExists('special_event_participants');
        Schema::dropIfExists('special_events');
    }
};
