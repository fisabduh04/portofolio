<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pegawai_rule_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->foreignId('tahun_id')->constrained('tahuns')->onDelete('cascade');
            $table->foreignId('attendance_rule_id')->constrained('attendance_rules')->onDelete('cascade');
            $table->timestamps();

            // Unique constraint: One rule per employee per year
            $table->unique(['pegawai_id', 'tahun_id'], 'unique_pegawai_rule_per_year');
        });

        // SEEDING LOGIC: Snapshot current rules to Active Year
        $activeYear = DB::table('tahuns')->where('isActive', 1)->first();

        if ($activeYear) {
            $pegawais = DB::table('pegawais')
                ->whereNotNull('attendance_rule_id')
                ->get();

            foreach ($pegawais as $pegawai) {
                try {
                    DB::table('pegawai_rule_allocations')->insert([
                        'pegawai_id' => $pegawai->id,
                        'tahun_id' => $activeYear->id,
                        'attendance_rule_id' => $pegawai->attendance_rule_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    // Ignore duplicates
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai_rule_allocations');
    }
};
