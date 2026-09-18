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
            'jadwals',
            'kelas_siswas',
            'jadwal_pikets',
            'hari_liburs',
            'pegawai_wajib_hadirs',
            'pegawai_rule_allocations',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($rule): void {
                $table->dropForeign(['tahun_id']);
                $table->foreign('tahun_id')->references('id')->on('tahuns')->onDelete($rule);
            });
        }
    }
};
