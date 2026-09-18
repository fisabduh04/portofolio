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
            ['kelas_siswas', 'kelas_id', 'kelas'],
            ['jadwals', 'kelas_id', 'kelas'],
            ['logbooks', 'kelas_id', 'kelas'],
            ['kelas', 'jurusan_id', 'jurusans'],
            ['jadwals', 'mapel_id', 'mapels'],
        ] as [$tableName, $column, $parent]) {
            Schema::table($tableName, function (Blueprint $table) use ($column, $parent, $rule): void {
                $table->dropForeign([$column]);
                $table->foreign($column)->references('id')->on($parent)->onDelete($rule);
            });
        }
    }
};
