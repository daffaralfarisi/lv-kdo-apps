<?php

namespace Database\Seeders;

use App\Enums\StatusKendaraan;
use App\Models\Kendaraan;
use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class KendaraanSeeder extends Seeder
{
    /**
     * 24 kendaraan dibagi ke semua pegawai (perangkat daerah mengikuti pegawai) dengan campuran kriteria layak service.
     */
    public function run(): void
    {
        $pegawai = Pegawai::query()->orderBy('id')->get();

        $penanggungJawab = new Sequence(fn (Sequence $sequence) => [
            'kdo_pegawai_id' => $pegawai[$sequence->index % $pegawai->count()]->id,
        ]);

        Kendaraan::factory()->count(10)->state($penanggungJawab)->create();
        Kendaraan::factory()->count(5)->tua()->state($penanggungJawab)->create();
        Kendaraan::factory()->count(4)->rusakRingan()->state($penanggungJawab)->create();
        Kendaraan::factory()->count(2)->tua()->rusakRingan()->state($penanggungJawab)->create();
        Kendaraan::factory()->count(2)->rusakBerat()->state($penanggungJawab)
            ->create(['kdo_status' => StatusKendaraan::DalamService]);
        Kendaraan::factory()->tua()->rusakBerat()->create([
            'kdo_pegawai_id' => $pegawai->last()->id,
            'kdo_status' => StatusKendaraan::TidakAktif,
        ]);
    }
}
