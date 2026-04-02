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
        Schema::create('fingerprint_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->foreignId('fingerprint_machine_id')->nullable()->constrained('fingerprint_machines')->onDelete('cascade');
            $table->string('fingerprint_user_id'); // ID on the machine
            $table->timestamps();

            // Unique constraint: One specific ID per machine per user? 
            // Actually, a user can have ID '101' on Machine A and '101' on Machine B.
            // But on a SINGLE machine, an ID belongs to ONE user.
            // However, we are linking Pegawai -> (Machine, ID).
            // So a Pegawai can have (Machine A, 101) and (Machine B, 202).
            // A (Machine A, 101) pair must be unique to one Pegawai.
            $table->unique(['fingerprint_machine_id', 'fingerprint_user_id'], 'unique_machine_user_id');
        });

        // Data migration dihapus karena kolom fingerprint_id sudah di-drop dari skema pegawais
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fingerprint_enrollments');
    }
};
