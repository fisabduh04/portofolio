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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id'); //id jadwal
            $table->string('jadwal_status')->nullable();// terjadwal, BP, Piket, diluar piket
            $table->string('pegawai_id')->nullable();//pengabsen
            $table->integer('masuk')->nullable();//pengabsen
            $table->integer('sakit')->nullable();//pengabsen
            $table->integer('izin')->nullable();//pengabsen
            $table->integer('alpha')->nullable();//pengabsen
            $table->integer('pulang')->nullable();//pengabsen
            $table->integer('lainnya')->nullable();//pengabsen
            $table->string('materi')->nullable();//pengabsen
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
