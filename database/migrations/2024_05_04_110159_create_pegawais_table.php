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
            $table->string('status',50)->nullable();//sertifikasi atau tidak?
            $table->string('aktif',50)->nullable();//aktif, cuti, berhenti dll?
            $table->string('email')->nullable();
            $table->string('nuptk')->nullable();
            $table->enum('jk',['L','P'])->nullable();
            $table->string('kotalahir',50)->nullable();
            $table->date('tanggallahir')->nullable();
            $table->string('jenisptk')->nullable();
            $table->string('agama',15)->nullable();
            $table->string('alamat')->nullable();
            $table->tinyInteger('rt')->nullable();
            $table->tinyInteger('rw')->nullable();
            $table->string('hp',50)->nullable();
            $table->string('skpengangkatan')->nullable();
            $table->string('lembagapengangkatan')->nullable();
            $table->string('PangkatGolongan')->nullable();
            $table->string('sumbergaji')->nullable();
            $table->string('ibukandung')->nullable();
            $table->string('kawin',15)->nullable();
            $table->string('suamiistri')->nullable();
            $table->string('pekerjaansuamiIstri',50)->nullable();
            $table->string('npwp',50)->nullable();
            $table->string('nonik',50)->nullable();
            $table->string('nokk',50)->nullable();
            $table->string('foto')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('password',50)->nullable(); // Updated line for password field
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
