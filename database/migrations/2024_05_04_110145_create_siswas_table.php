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
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();
        $table->string('nama');
        $table->string('nipd',50)->unique();
        $table->enum('jk',['L','P'])->nullable();
        $table->string('nisn',50)->nullable();
        $table->string('status',50)->nullable();// santri atau bukan
        $table->string('aktif',50)->nullable();// Aktif, Berhenti, Pindah
        $table->string('tempatlahir',50)->nullable();
        $table->date('tanggallahir')->nullable();
        $table->string('nik',50)->nullable();
        $table->string('agama',15)->nullable();
        $table->string('alamat',500)->nullable();
        $table->integer('rt')->nullable();
        $table->integer('rw')->nullable();
        $table->string('dusun',100)->nullable();
        $table->string('kelurahan',100)->nullable();
        $table->string('kecamatan',100)->nullable();
        $table->integer('kode_pos')->nullable();
        $table->string('jenis_tinggal',100)->nullable();
        $table->string('alat_transportasi',100)->nullable();
        $table->string('telepon',25)->nullable();
        $table->string('hp',25)->nullable();
        $table->string('email')->nullable();
        $table->string('skhun',50)->nullable();
        $table->string('penerima_kps')->nullable();
        $table->string('nokps')->nullable();
        $table->string('ayah',50)->nullable();
        $table->year('tahunlahirayah')->nullable();
        $table->string('pendidikanayah')->nullable();
        $table->string('pekerjaanayah')->nullable();
        $table->string('penghasilanayah')->nullable();
        $table->string('nikayah',50)->nullable();
        $table->string('namaibu')->nullable();
        $table->year('tahunlahiribu')->nullable();
        $table->string('pendidikanibu')->nullable();
        $table->string('pekerjaanibu')->nullable();
        $table->string('penghasilanibu')->nullable();
        $table->string('nikibu',50)->nullable();
        $table->string('namawali')->nullable();
        $table->year('tahunlahirwali')->nullable();
        $table->string('pendidikanwali')->nullable();
        $table->string('pekerjaanwali')->nullable();
        $table->string('penghasilanwali')->nullable();
        $table->string('nikwali',50)->nullable();
        $table->string('rombelsaatini')->nullable();
        $table->string('nopesertaunas',50)->nullable();
        $table->string('noijazah',50)->nullable();
        $table->string('penerimakip')->nullable();
        $table->string('nomorkip',50)->nullable();
        $table->string('namadikip',50)->nullable();
        $table->string('nomorkks',50)->nullable();
        $table->string('noaktalahir',50)->nullable();
        $table->string('bank',50)->nullable();
        $table->string('nomor_rekening_bank',50)->nullable();
        $table->string('rekening_atas_nama')->nullable();
        $table->string('layakpip')->nullable();
        $table->string('alasanlayakpip')->nullable();
        $table->string('kebutuhankhusus')->nullable();
        $table->string('sekolahasal',50)->nullable();
        $table->integer('anakke')->nullable();
        $table->string('lintang')->nullable();
        $table->string('bujur')->nullable();
        $table->string('nokk',50)->nullable();
        $table->integer('beratbadan')->nullable();
        $table->integer('tinggibadan')->nullable();
        $table->integer('lingkarkepala')->nullable();
        $table->integer('jmlsaudara')->nullable();
        $table->integer('jarakrumah')->nullable();
        $table->text('deskripsi')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
