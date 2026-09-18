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
        $this->changeDeleteRule('restrict');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->changeDeleteRule('cascade');
    }

    private function changeDeleteRule(string $rule): void
    {
        foreach ([
            'absensis' => ['siswa_id', 'siswas'],
            'kelas_siswas' => ['siswa_id', 'siswas'],
            'jadwals' => ['pegawai_id', 'pegawais'],
            'jadwal_pikets' => ['pegawai_id', 'pegawais'],
            'attendance_logs' => ['pegawai_id', 'pegawais'],
            'pegawai_absensis' => ['pegawai_id', 'pegawais'],
            'pegawai_izins' => ['pegawai_id', 'pegawais'],
            'pegawai_wajib_hadirs' => ['pegawai_id', 'pegawais'],
            'pegawai_rule_allocations' => ['pegawai_id', 'pegawais'],
            'pegawai_schedule_overrides' => ['pegawai_id', 'pegawais'],
            'special_event_participants' => ['pegawai_id', 'pegawais'],
        ] as $tableName => [$column, $parent]) {
            Schema::table($tableName, function (Blueprint $table) use ($column, $parent, $rule): void {
                $table->dropForeign([$column]);
                $table->foreign($column)->references('id')->on($parent)->onDelete($rule);
            });
        }
    }
};
