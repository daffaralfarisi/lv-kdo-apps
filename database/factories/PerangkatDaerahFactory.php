<?php

namespace Database\Factories;

use App\Enums\StatusPerangkat;
use App\Models\PerangkatDaerah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PerangkatDaerah>
 */
class PerangkatDaerahFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kdo_nama_perangkat' => 'Dinas '.fake()->unique()->words(2, true),
            'kdo_status_perangkat' => StatusPerangkat::Aktif,
        ];
    }
}
