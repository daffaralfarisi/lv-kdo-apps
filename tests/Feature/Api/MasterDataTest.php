<?php

namespace Tests\Feature\Api;

use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\PenjadwalanService;
use App\Models\PerangkatDaerah;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_role_creates_kendaraan_with_normalized_nomor_polisi_and_returns_201(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $pegawai = Pegawai::factory()->create();

        $response = $this->postJson('/api/v1/kendaraan', [
            'kdo_pegawai_id' => $pegawai->id,
            'kdo_nomor_polisi' => 'b1234 pqa',
            'kdo_merek' => 'Toyota',
            'kdo_tipe' => 'Innova 2.4 V',
            'kdo_tahun_produksi' => 2019,
            'kdo_warna' => 'Hitam',
            'kdo_kondisi' => 'baik',
            'kdo_status' => 'aktif',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.kdo_nomor_polisi', 'B 1234 PQA')
            ->assertJsonPath('data.pegawai.perangkat_daerah.id', $pegawai->kdo_perangkat_daerah_id);
        $this->assertDatabaseHas('kdo_kendaraan', ['kdo_nomor_polisi' => 'B 1234 PQA', 'kdo_pegawai_id' => $pegawai->id]);
    }

    public function test_invalid_kendaraan_payload_returns_422_with_indonesian_messages(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/v1/kendaraan', ['kdo_nomor_polisi' => '12-ABC', 'kdo_kondisi' => 'hilang']);

        $response->assertUnprocessable()->assertJsonValidationErrors([
            'kdo_nomor_polisi' => 'Format nomor polisi tidak valid. Contoh: B 1234 PQA.',
            'kdo_kondisi' => 'Kondisi yang dipilih tidak valid.',
            'kdo_pegawai_id' => 'Penanggung jawab wajib diisi.',
        ]);
        $this->assertDatabaseCount('kdo_kendaraan', 0);
    }

    public function test_creates_pegawai_under_perangkat_daerah_and_returns_201(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $perangkat = PerangkatDaerah::factory()->create();

        $response = $this->postJson('/api/v1/pegawai', [
            'kdo_perangkat_daerah_id' => $perangkat->id,
            'kdo_nama_pegawai' => 'Siti Rahmawati',
            'kdo_nip' => '198703152010012003',
            'kdo_jabatan' => 'Kepala Sub Bagian Umum',
            'kdo_golongan' => 'III/d',
            'kdo_email' => 'siti@pemda.test',
        ]);

        $response->assertCreated()->assertJsonPath('data.perangkat_daerah.kdo_nama_perangkat', $perangkat->kdo_nama_perangkat);
        $this->assertDatabaseHas('kdo_pegawai', ['kdo_nip' => '198703152010012003', 'kdo_perangkat_daerah_id' => $perangkat->id]);
    }

    public function test_pegawai_without_perangkat_daerah_returns_422(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/v1/pegawai', [
            'kdo_nama_pegawai' => 'Siti Rahmawati',
            'kdo_nip' => '198703152010012003',
            'kdo_jabatan' => 'Kepala Sub Bagian Umum',
            'kdo_golongan' => 'III/d',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['kdo_perangkat_daerah_id' => 'Perangkat daerah wajib diisi.']);
        $this->assertDatabaseCount('kdo_pegawai', 0);
    }

    public function test_filters_kendaraan_by_perangkat_daerah_of_its_pegawai(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $dinasKesehatan = PerangkatDaerah::factory()->create();
        $milikDinkes = Kendaraan::factory()->for(Pegawai::factory()->for($dinasKesehatan, 'perangkatDaerah'))->create();
        Kendaraan::factory()->create();

        $response = $this->getJson('/api/v1/kendaraan?kdo_perangkat_daerah_id='.$dinasKesehatan->id);

        $response->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $milikDinkes->id);
    }

    public function test_deleting_perangkat_daerah_that_still_has_pegawai_returns_409(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $pegawai = Pegawai::factory()->create();

        $response = $this->deleteJson('/api/v1/perangkat-daerah/'.$pegawai->kdo_perangkat_daerah_id);

        $response->assertConflict()->assertJsonPath('message', 'Perangkat daerah masih memiliki 1 pegawai. Pindahkan atau hapus pegawai terlebih dahulu.');
        $this->assertModelExists($pegawai->perangkatDaerah);
    }

    public function test_deleting_pegawai_still_responsible_for_kendaraan_returns_409(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $kendaraan = Kendaraan::factory()->create();

        $response = $this->deleteJson('/api/v1/pegawai/'.$kendaraan->kdo_pegawai_id);

        $response->assertConflict();
        $this->assertModelExists($kendaraan->pegawai);
    }

    public function test_deleting_kendaraan_also_removes_its_service_schedules(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $jadwal = PenjadwalanService::factory()->create();

        $response = $this->deleteJson('/api/v1/kendaraan/'.$jadwal->kdo_kendaraan_id);

        $response->assertOk();
        $this->assertModelMissing($jadwal);
    }

    public function test_service_end_date_before_start_date_returns_422(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $kendaraan = Kendaraan::factory()->create();

        $response = $this->postJson('/api/v1/penjadwalan-service', [
            'kdo_kendaraan_id' => $kendaraan->id,
            'kdo_service_mulai' => '2026-11-12',
            'kdo_service_selesai' => '2026-11-10',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['kdo_service_selesai']);
    }
}
