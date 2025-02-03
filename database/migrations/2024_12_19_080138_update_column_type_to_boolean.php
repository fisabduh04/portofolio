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
        Schema::table('tahuns', function (Blueprint $table) {
            $table->boolean('isActive')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tahuns', function (Blueprint $table) {
            $table->tinyInteger('your_column_name')->change();
        });
    }
};
