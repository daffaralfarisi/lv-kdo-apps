<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use App\Models\PenjadwalanService;
use Illuminate\Database\Seeder;

class PenjadwalanServiceSeeder extends Seeder
{
    /**
     * Jadwal selesai, berlangsung, dan akan datang untuk sebagian kendaraan layak service.
     */
    public function run(): void
    {
        $kendaraan = Kendaraan::query()->layakService()->orderBy('id')->take(6)->get();

        foreach ($kendaraan as $index => $item) {
            [$mulai, $selesai] = match ($index % 3) {
                0 => [today()->subDays(30), today()->subDays(27)],
                1 => [today()->subDay(), today()->addDays(2)],
                default => [today()->addDays(5), today()->addDays(7)],
            };

            PenjadwalanService::factory()->for($item, 'kendaraan')->create([
                'kdo_service_mulai' => $mulai->toDateString(),
                'kdo_service_selesai' => $selesai->toDateString(),
            ]);
        }
    }
}
