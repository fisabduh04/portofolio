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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('nuptk')->nullable();
            $table->string('jk')->nullable();
            $table->string('kotalahir')->nullable();
            $table->date('tanggallahir')->nullable();
            $table->string('jenisptk')->nullable();
            $table->string('agama')->nullable();
            $table->string('alamat')->nullable();
            $table->tinyInteger('rt')->nullable();
            $table->tinyInteger('rw')->nullable();
            $table->string('hp')->nullable();
            $table->string('skpengangkatan')->nullable();
            $table->string('lembagapengangkatan')->nullable();
            $table->string('PangkatGolongan')->nullable();
            $table->string('sumbergaji')->nullable();
            $table->string('ibukandung')->nullable();
            $table->string('statusPerkawinan')->nullable();
            $table->string('suamiistri')->nullable();
            $table->string('pekerjaansuamiIstri')->nullable();
            $table->string('npwp')->nullable();
            $table->string('nonik')->nullable();
            $table->string('nokk')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
