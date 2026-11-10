<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Riwayat email notifikasi layak service yang dikirim ke penanggung jawab.
     */
    public function up(): void
    {
        Schema::create('kdo_notifikasi_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kdo_kendaraan_id')->constrained('kdo_kendaraan')->cascadeOnDelete();
            $table->foreignId('kdo_pegawai_id')->nullable()->constrained('kdo_pegawai')->nullOnDelete();
            $table->foreignId('kdo_user_id')->nullable()->constrained('kdo_user')->nullOnDelete();
            $table->string('kdo_mode', 10);
            $table->string('kdo_email_tujuan');
            $table->text('kdo_alasan');
            $table->timestamp('kdo_created_at')->nullable();
            $table->timestamp('kdo_updated_at')->nullable();

            $table->index(['kdo_kendaraan_id', 'kdo_mode', 'kdo_created_at'], 'kdo_notifikasi_kendaraan_mode_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kdo_notifikasi_service');
    }
};
