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
    Schema::create('sekolahs', function (Blueprint $table) {
            $table->id();

            // 1. Identitas Utama
            $table->string('nama_sekolah')->nullable();
            $table->string('npsn', 8)->nullable()->unique(); // 8 digit
            $table->string('nss', 30)->nullable();
            $table->string('bentuk_pendidikan', 50)->nullable(); // SD/SMP/SMA/SMK
            $table->enum('status_sekolah', ['Negeri', 'Swasta'])->nullable();

            // 2. Lokasi & Alamat Detil
            $table->text('alamat')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('dusun')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->decimal('lintang', 10, 7)->nullable(); // Latitude
            $table->decimal('bujur', 10, 7)->nullable(); // Longitude

            // 3. Data Pelengkap & Kontak
            $table->string('no_telp', 20)->nullable();
            $table->string('no_fax', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('kebutuhan_khusus')->nullable(); // Inklusi dll
            
            // Pajak
            $table->string('nama_wajib_pajak')->nullable();
            $table->string('npwp', 20)->nullable();

            // 4. Data Periodik
            $table->string('waktu_penyelenggaraan')->nullable(); // Pagi/6 hari
            $table->string('sumber_listrik')->nullable(); // PLN/Diesel
            $table->string('daya_listrik')->nullable(); // Watt
            $table->string('akses_internet')->nullable(); // Telkomsel/Indihome
            $table->string('sertifikasi_iso')->nullable(); 

            // Legalitas
            $table->string('sk_pendirian')->nullable();
            $table->date('tgl_sk_pendirian')->nullable();
            $table->string('sk_izin_operasional')->nullable();
            $table->date('tgl_sk_izin_operasional')->nullable();
            $table->string('akreditasi', 5)->nullable();

            // Sarpras Lainnya
            $table->unsignedInteger('luas_tanah')->nullable();
            $table->unsignedInteger('luas_bangunan')->nullable();
            $table->string('sumber_air')->nullable();

            // Identitas Kepala & Operator (Opsional jika relasi ke User/Pegawai, tapi untuk data statis sekolah bisa ditaruh sini)
            $table->string('kepala_sekolah')->nullable();
            $table->string('nip_kepala_sekolah')->nullable();

            // Media
            $table->string('logo')->nullable();
            $table->string('kop_surat')->nullable(); // Tambahan untuk cetak surat
            
            $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sekolahs');
    }
};
