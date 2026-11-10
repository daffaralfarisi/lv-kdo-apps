<?php

namespace Database\Factories;

use App\Enums\KondisiKendaraan;
use App\Enums\StatusKendaraan;
use App\Models\Kendaraan;
use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kendaraan>
 */
class KendaraanFactory extends Factory
{
    private const MODEL = [
        ['Toyota', 'Innova Reborn 2.4 V'], ['Toyota', 'Avanza 1.3 G'], ['Toyota', 'Fortuner 2.4 VRZ'],
        ['Toyota', 'Hilux Double Cabin'], ['Mitsubishi', 'Pajero Sport Dakar'], ['Mitsubishi', 'L300 Pick Up'],
        ['Suzuki', 'APV Arena'], ['Suzuki', 'Ertiga GL'], ['Daihatsu', 'Gran Max Blind Van'],
        ['Isuzu', 'Elf NLR Ambulance'], ['Honda', 'Supra X 125'], ['Honda', 'Vario 125'], ['Yamaha', 'NMAX 155'],
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        [$merek, $tipe] = fake()->randomElement(self::MODEL);

        return [
            'kdo_pegawai_id' => Pegawai::factory(),
            'kdo_nomor_polisi' => fake()->randomElement(['B', 'D', 'F', 'T']).' '
                .fake()->unique()->numberBetween(1000, 9999).' '
                .fake()->randomElement(['PQ', 'PRA', 'PRB', 'PL']),
            'kdo_merek' => $merek,
            'kdo_tipe' => $tipe,
            'kdo_tahun_produksi' => now()->year - fake()->numberBetween(0, 4),
            'kdo_warna' => fake()->randomElement(['Hitam', 'Putih', 'Silver', 'Abu-abu', 'Merah Marun']),
            'kdo_kondisi' => KondisiKendaraan::Baik,
            'kdo_status' => StatusKendaraan::Aktif,
        ];
    }

    /**
     * Umur kendaraan melebihi batas service (> 5 tahun).
     */
    public function tua(): static
    {
        return $this->state(fn (array $attributes) => [
            'kdo_tahun_produksi' => now()->year - fake()->numberBetween(config('kdo.batas_umur_service') + 1, 14),
        ]);
    }

    public function rusakRingan(): static
    {
        return $this->state(fn (array $attributes) => ['kdo_kondisi' => KondisiKendaraan::RusakRingan]);
    }

    public function rusakBerat(): static
    {
        return $this->state(fn (array $attributes) => ['kdo_kondisi' => KondisiKendaraan::RusakBerat]);
    }
}
