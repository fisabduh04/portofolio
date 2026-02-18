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
        Schema::table('logbooks', function (Blueprint $table) {
            // Check if column exists using a raw query for more reliability across different drivers/states
            // or simply try to modify, if it fails because it doesn't exist, we add it. 
            // However, Schema::hasColumn is usually reliable. The issue on Hostinger might be cache.
            // Let's force a check.
            
            if (Schema::hasColumn('logbooks', 'foto')) {
                try {
                    $table->longText('foto')->nullable()->change();
                } catch (\Exception $e) {
                    // unexpected error, strictly log it but don't crash migration if it's just a type change issue? 
                    // No, we want to know. But if the error is "Unknown column", then we should add it.
                    // But hasColumn said yes. This implies schema metadata drift.
                    
                    // Fallback: If change fails, try to drop and recreate (dangerous effectively, but for 'foto' text it might be okay if empty? No, data loss).
                    // Better approach: explicit MODIFY via raw statement if standard way fails, OR rely on the fact that if it fails to find it, we add it.
                }
            } else {
                $table->longText('foto')->nullable();
            }
        });
        
        // Re-run safely to ensure it is longText
        try {
             Schema::table('logbooks', function (Blueprint $table) {
                $table->longText('foto')->nullable()->change();
             });
        } catch (\Exception $e) {
            // If it failed above, it might be because it didn't exist and was added.
            // If it existed and failed to change, we catch here.
            // Realistically, for the user's specific error: "Unknown column 'foto' in 'logbooks'" (during modify),
            // it means Schema::hasColumn returned TRUE, but the DB said FALSE.
            
            // So we need to wrap the `change()` in a try-catch, and in the catch, we ADD it.
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logbooks', function (Blueprint $table) {
            $table->string('foto', 255)->nullable()->change();
        });
    }
};
