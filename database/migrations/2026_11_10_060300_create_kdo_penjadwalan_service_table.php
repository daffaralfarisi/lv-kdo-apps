<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kdo_penjadwalan_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kdo_kendaraan_id')->constrained('kdo_kendaraan')->cascadeOnDelete();
            $table->date('kdo_service_mulai');
            $table->date('kdo_service_selesai')->nullable();
            // Tambahan di luar mapping awal: catatan pekerjaan service.
            $table->text('kdo_keterangan')->nullable();
            $table->timestamp('kdo_created_at')->nullable();
            $table->timestamp('kdo_updated_at')->nullable();

            $table->index(['kdo_kendaraan_id', 'kdo_service_mulai'], 'kdo_jadwal_kendaraan_mulai_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kdo_penjadwalan_service');
    }
};
