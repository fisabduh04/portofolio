<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function (): void {
            $orphans = DB::table('users')->whereNotNull('pegawai_id')
                ->whereNotIn('pegawai_id', DB::table('pegawais')->select('id'))
                ->lockForUpdate()->get(['id', 'pegawai_id']);

            foreach ($orphans as $user) {
                Log::warning('Referensi pegawai akun tidak valid dikosongkan', [
                    'user_id' => $user->id,
                    'previous_pegawai_id' => $user->pegawai_id,
                ]);
                DB::table('users')->where('id', $user->id)->update(['pegawai_id' => null]);
            }
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['pegawai_id']);
        });
    }
};
