<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\PerangkatDaerah;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    /**
     * 15 pegawai tersebar merata di 3 perangkat daerah (1 pegawai tanpa email).
     */
    public function run(): void
    {
        $perangkatDaerah = PerangkatDaerah::query()->orderBy('id')->get();

        Pegawai::factory()->count(14)->state(new Sequence(fn (Sequence $sequence) => [
            'kdo_perangkat_daerah_id' => $perangkatDaerah[$sequence->index % $perangkatDaerah->count()]->id,
        ]))->create();

        Pegawai::factory()->tanpaEmail()->create([
            'kdo_perangkat_daerah_id' => $perangkatDaerah->last()->id,
            'kdo_jabatan' => 'Pengemudi',
        ]);
    }
}
