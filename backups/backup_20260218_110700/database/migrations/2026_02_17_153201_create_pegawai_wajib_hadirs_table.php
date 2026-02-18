<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Tahun;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create the new table
        Schema::create('pegawai_wajib_hadirs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->foreignId('tahun_id')->constrained('tahuns')->onDelete('cascade');
            $table->string('hari'); // Senin, Selasa, etc.
            $table->timestamps();

            // Unique constraint to prevent duplicate entries for same person, year, and day
            $table->unique(['pegawai_id', 'tahun_id', 'hari']);
        });

        // 2. Migrate existing JSON data to the new table
        // We need the Active Year to associate the current data with.
        $activeYear = DB::table('tahuns')->where('isActive', 1)->first();

        if ($activeYear) {
            $pegawais = DB::table('pegawais')->whereNotNull('wajib_hadir')->get();

            foreach ($pegawais as $pegawai) {
                $days = json_decode($pegawai->wajib_hadir, true);
                
                if (is_array($days)) {
                    foreach ($days as $day) {
                        try {
                            DB::table('pegawai_wajib_hadirs')->insert([
                                'pegawai_id' => $pegawai->id,
                                'tahun_id' => $activeYear->id,
                                'hari' => $day,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        } catch (\Exception $e) {
                            // Ignore duplicates if any
                        }
                    }
                }
            }
        }

        // 3. Drop the old JSON column
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn('wajib_hadir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Re-add the JSON column
        Schema::table('pegawais', function (Blueprint $table) {
            $table->json('wajib_hadir')->nullable();
        });

        // 2. Restore data (Optional - logic would be complex because we might have multiple years)
        // For simple rollback, we might leave it empty or try to fetch data from the active year.
        // Simplified:
        // $activeYear = DB::table('tahuns')->where('isActive', 1)->first();
        // ... reverse logic ... 
        
        // 3. Drop the new table
        Schema::dropIfExists('pegawai_wajib_hadirs');
    }
};
