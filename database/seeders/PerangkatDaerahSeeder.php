<?php

namespace Database\Seeders;

use App\Enums\StatusPerangkat;
use App\Models\PerangkatDaerah;
use Illuminate\Database\Seeder;

class PerangkatDaerahSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Dinas Pendidikan', 'Dinas Kesehatan', 'Dinas Pekerjaan Umum dan Penataan Ruang (PUPR)'] as $nama) {
            PerangkatDaerah::query()->firstOrCreate(
                ['kdo_nama_perangkat' => $nama],
                ['kdo_status_perangkat' => StatusPerangkat::Aktif],
            );
        }
    }
}
