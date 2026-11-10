<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kdo_pegawai', function (Blueprint $table) {
            $table->id();
            // Instansi tempat pegawai bertugas; kendaraan mengikuti perangkat daerah pegawai penanggung jawabnya.
            $table->foreignId('kdo_perangkat_daerah_id')->constrained('kdo_perangkat_daerah')->restrictOnDelete();
            $table->string('kdo_nama_pegawai');
            $table->string('kdo_nip', 18)->unique();
            $table->string('kdo_jabatan');
            $table->string('kdo_golongan', 10);
            // Tambahan di luar mapping awal: tujuan email notifikasi service.
            $table->string('kdo_email')->nullable();
            $table->timestamp('kdo_created_at')->nullable();
            $table->timestamp('kdo_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kdo_pegawai');
    }
};
