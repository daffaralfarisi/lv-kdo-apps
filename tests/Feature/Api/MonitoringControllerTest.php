<?php

namespace Tests\Feature\Api;

use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class MonitoringControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @return array<string, array{0: string, 1: list<string>}>
     */
    public static function kriteria(): array
    {
        return [
            'umur lebih dari 5 tahun' => ['umur', ['B 2020 TUA']],
            'rusak ringan' => ['rusak_ringan', ['B 1 RR']],
            'rusak berat' => ['rusak_berat', ['B 2 RB']],
            'semua kriteria' => ['', ['B 1 RR', 'B 2 RB', 'B 2020 TUA']],
        ];
    }

    /**
     * @param  list<string>  $nomorPolisi
     */
    #[DataProvider('kriteria')]
    public function test_lists_only_kendaraan_matching_service_criteria(string $kriteria, array $nomorPolisi): void
    {
        $this->travelTo('2026-11-10 09:00:00');
        Sanctum::actingAs(User::factory()->create());
        Kendaraan::factory()->create(['kdo_nomor_polisi' => 'B 2021 PAS', 'kdo_tahun_produksi' => 2021]);
        Kendaraan::factory()->create(['kdo_nomor_polisi' => 'B 2020 TUA', 'kdo_tahun_produksi' => 2020]);
        Kendaraan::factory()->rusakRingan()->create(['kdo_nomor_polisi' => 'B 1 RR', 'kdo_tahun_produksi' => 2024]);
        Kendaraan::factory()->rusakBerat()->create(['kdo_nomor_polisi' => 'B 2 RB', 'kdo_tahun_produksi' => 2025]);

        $response = $this->getJson('/api/v1/monitoring/kendaraan?sort=kdo_nomor_polisi&kriteria='.$kriteria);

        $response->assertOk();
        $this->assertSame($nomorPolisi, array_column($response->json('data'), 'kdo_nomor_polisi'));
    }

    public function test_filters_monitoring_by_perangkat_daerah_of_the_pegawai(): void
    {
        $this->travelTo('2026-11-10 09:00:00');
        Sanctum::actingAs(User::factory()->create());
        $pegawai = Pegawai::factory()->create();
        Kendaraan::factory()->rusakBerat()->for($pegawai)->create(['kdo_nomor_polisi' => 'B 7 DIN']);
        Kendaraan::factory()->rusakBerat()->create(['kdo_nomor_polisi' => 'B 8 LAIN']);

        $response = $this->getJson('/api/v1/monitoring/kendaraan?kdo_perangkat_daerah_id='.$pegawai->kdo_perangkat_daerah_id);

        $response->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.kdo_nomor_polisi', 'B 7 DIN');
    }

    public function test_ringkasan_counts_each_criteria_and_exposes_email_mode(): void
    {
        $this->travelTo('2026-11-10 09:00:00');
        Sanctum::actingAs(User::factory()->create());
        Kendaraan::factory()->tua()->rusakRingan()->create();
        Kendaraan::factory()->create(['kdo_tahun_produksi' => 2025]);

        $response = $this->getJson('/api/v1/monitoring/ringkasan');

        $response->assertOk()
            ->assertJsonPath('data.total_kendaraan', 2)
            ->assertJsonPath('data.layak_service', 1)
            ->assertJsonPath('data.umur', 1)
            ->assertJsonPath('data.rusak_ringan', 1)
            ->assertJsonPath('data.rusak_berat', 0)
            ->assertJsonPath('data.mode_email.kode', 'dev');
    }
}
