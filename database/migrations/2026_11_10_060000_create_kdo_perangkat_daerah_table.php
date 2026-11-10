<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kdo_perangkat_daerah', function (Blueprint $table) {
            $table->id();
            $table->string('kdo_nama_perangkat')->unique();
            $table->string('kdo_status_perangkat', 20)->default('aktif');
            $table->timestamp('kdo_created_at')->nullable();
            $table->timestamp('kdo_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kdo_perangkat_daerah');
    }
};
