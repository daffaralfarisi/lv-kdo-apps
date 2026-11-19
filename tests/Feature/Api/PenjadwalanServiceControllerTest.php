<?php

namespace Tests\Feature\Api;

use App\Models\PenjadwalanService;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PenjadwalanServiceControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function jadwalTerkunci(): array
    {
        return [
            'berlangsung' => ['2026-11-15', '2026-11-20'],
            'selesai' => ['2026-11-01', '2026-11-03'],
        ];
    }

    #[DataProvider('jadwalTerkunci')]
    public function test_updating_schedule_that_is_running_or_finished_returns_409(string $mulai, string $selesai): void
    {
        $this->travelTo('2026-11-17 09:00:00');
        Sanctum::actingAs(User::factory()->create());
        $jadwal = PenjadwalanService::factory()->create(['kdo_service_mulai' => $mulai, 'kdo_service_selesai' => $selesai, 'kdo_keterangan' => 'Ganti oli']);

        $response = $this->putJson('/api/v1/penjadwalan-service/'.$jadwal->id, [
            'kdo_kendaraan_id' => $jadwal->kdo_kendaraan_id,
            'kdo_service_mulai' => $mulai,
            'kdo_service_selesai' => $selesai,
            'kdo_keterangan' => 'Diubah',
        ]);

        $response->assertConflict()->assertJsonPath('message', 'Jadwal yang sedang berlangsung atau sudah selesai tidak dapat diubah.');
        $this->assertDatabaseHas('kdo_penjadwalan_service', ['id' => $jadwal->id, 'kdo_keterangan' => 'Ganti oli']);
    }

    #[DataProvider('jadwalTerkunci')]
    public function test_deleting_schedule_that_is_running_or_finished_returns_409(string $mulai, string $selesai): void
    {
        $this->travelTo('2026-11-17 09:00:00');
        Sanctum::actingAs(User::factory()->create());
        $jadwal = PenjadwalanService::factory()->create(['kdo_service_mulai' => $mulai, 'kdo_service_selesai' => $selesai]);

        $response = $this->deleteJson('/api/v1/penjadwalan-service/'.$jadwal->id);

        $response->assertConflict();
        $this->assertModelExists($jadwal);
    }

    public function test_upcoming_schedule_can_still_be_updated_and_deleted(): void
    {
        $this->travelTo('2026-11-17 09:00:00');
        Sanctum::actingAs(User::factory()->create());
        $jadwal = PenjadwalanService::factory()->create(['kdo_service_mulai' => '2026-11-20', 'kdo_service_selesai' => '2026-11-22']);

        $this->putJson('/api/v1/penjadwalan-service/'.$jadwal->id, [
            'kdo_kendaraan_id' => $jadwal->kdo_kendaraan_id,
            'kdo_service_mulai' => '2026-11-21',
            'kdo_service_selesai' => '2026-11-23',
        ])->assertOk()->assertJsonPath('data.kdo_service_mulai', '2026-11-21');
        $this->deleteJson('/api/v1/penjadwalan-service/'.$jadwal->id)->assertOk();

        $this->assertModelMissing($jadwal);
    }

    public function test_detail_includes_vehicle_responsible_employee_and_agency(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $jadwal = PenjadwalanService::factory()->create();

        $response = $this->getJson('/api/v1/penjadwalan-service/'.$jadwal->id);

        $response->assertOk()
            ->assertJsonPath('data.kendaraan.id', $jadwal->kdo_kendaraan_id)
            ->assertJsonPath('data.kendaraan.pegawai.id', $jadwal->kendaraan->kdo_pegawai_id)
            ->assertJsonPath('data.kendaraan.pegawai.perangkat_daerah.id', $jadwal->kendaraan->pegawai->kdo_perangkat_daerah_id);
    }
}
