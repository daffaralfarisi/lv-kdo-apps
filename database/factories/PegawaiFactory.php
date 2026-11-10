<?php

namespace Database\Factories;

use App\Models\Pegawai;
use App\Models\PerangkatDaerah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pegawai>
 */
class PegawaiFactory extends Factory
{
    private const JABATAN = [
        'Kepala Dinas', 'Sekretaris Dinas', 'Kepala Bidang Program', 'Kepala Bidang Pelayanan',
        'Kepala Sub Bagian Umum dan Kepegawaian', 'Kepala Seksi Sarana dan Prasarana',
        'Pengelola Barang Milik Daerah', 'Analis Perencanaan', 'Pengadministrasi Umum',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kdo_perangkat_daerah_id' => PerangkatDaerah::factory(),
            'kdo_nama_pegawai' => fake()->name(),
            'kdo_nip' => $this->nip(),
            'kdo_jabatan' => fake()->randomElement(self::JABATAN),
            'kdo_golongan' => fake()->randomElement(['II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c']),
            'kdo_email' => fake()->unique()->safeEmail(),
        ];
    }

    public function tanpaEmail(): static
    {
        return $this->state(fn (array $attributes) => ['kdo_email' => null]);
    }

    /**
     * NIP 18 digit: tanggal lahir (8) + TMT CPNS tahun-bulan (6) + jenis kelamin (1) + urut (3).
     */
    private function nip(): string
    {
        $lahir = fake()->dateTimeBetween('-58 years', '-25 years');
        $tmt = (int) $lahir->format('Y') + fake()->numberBetween(22, 28);

        return $lahir->format('Ymd')
            .$tmt.str_pad((string) fake()->numberBetween(1, 12), 2, '0', STR_PAD_LEFT)
            .fake()->randomElement(['1', '2'])
            .str_pad((string) fake()->unique()->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);
    }
}
