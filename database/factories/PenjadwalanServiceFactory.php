<?php

namespace Database\Factories;

use App\Models\Kendaraan;
use App\Models\PenjadwalanService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PenjadwalanService>
 */
class PenjadwalanServiceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mulai = today()->addDays(fake()->numberBetween(1, 20));

        return [
            'kdo_kendaraan_id' => Kendaraan::factory(),
            'kdo_service_mulai' => $mulai->toDateString(),
            'kdo_service_selesai' => $mulai->copy()->addDays(fake()->numberBetween(1, 5))->toDateString(),
            'kdo_keterangan' => fake()->randomElement([
                'Servis berkala dan ganti oli mesin', 'Perbaikan sistem rem dan kaki-kaki',
                'Tune up mesin dan ganti filter', 'Perbaikan kelistrikan dan AC', 'Penggantian ban dan spooring',
            ]),
        ];
    }
}
