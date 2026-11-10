<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kdo_kendaraan', function (Blueprint $table) {
            $table->id();
            // Perangkat daerah kendaraan diturunkan dari pegawai penanggung jawab (kdo_pegawai.kdo_perangkat_daerah_id).
            $table->foreignId('kdo_pegawai_id')->constrained('kdo_pegawai')->restrictOnDelete();
            $table->string('kdo_nomor_polisi', 15)->unique();
            $table->string('kdo_merek', 50);
            $table->string('kdo_tipe', 100);
            $table->unsignedSmallInteger('kdo_tahun_produksi')->index();
            $table->string('kdo_warna', 30);
            $table->string('kdo_kondisi', 20)->index();
            $table->string('kdo_status', 20)->default('aktif');
            $table->timestamp('kdo_created_at')->nullable();
            $table->timestamp('kdo_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kdo_kendaraan');
    }
};
