<?php

namespace Tests\Feature\Api;

use App\Enums\ModeEmail;
use App\Mail\KendaraanLayakServiceMail;
use App\Models\Kendaraan;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class NotifikasiServiceControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_dev_mode_redirects_email_to_dev_recipient_and_logs_it(): void
    {
        config(['kdo.email_notifikasi.mode' => 'dev', 'kdo.email_notifikasi.penerima_dev' => 'flixads999@gmail.com']);
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $kendaraan = Kendaraan::factory()->rusakBerat()->for(Pegawai::factory()->state(['kdo_email' => 'pj@pemda.test']))->create();
        Mail::fake();

        $response = $this->postJson("/api/v1/kendaraan/{$kendaraan->id}/notifikasi-service");

        $response->assertCreated()->assertJsonPath('data.kdo_email_tujuan', 'flixads999@gmail.com');
        Mail::assertSent(KendaraanLayakServiceMail::class, fn (KendaraanLayakServiceMail $mail) => $mail->hasTo('flixads999@gmail.com') && ! $mail->hasTo('pj@pemda.test'));
        $this->assertDatabaseHas('kdo_notifikasi_service', [
            'kdo_kendaraan_id' => $kendaraan->id, 'kdo_mode' => 'dev', 'kdo_email_tujuan' => 'flixads999@gmail.com', 'kdo_user_id' => $user->id,
        ]);
    }

    public function test_prod_mode_sends_email_to_pegawai_address(): void
    {
        config(['kdo.email_notifikasi.mode' => 'prod']);
        Sanctum::actingAs(User::factory()->create());
        $kendaraan = Kendaraan::factory()->rusakRingan()->for(Pegawai::factory()->state(['kdo_email' => 'pj@pemda.test']))->create();
        Mail::fake();

        $response = $this->postJson("/api/v1/kendaraan/{$kendaraan->id}/notifikasi-service");

        $response->assertCreated();
        Mail::assertSent(KendaraanLayakServiceMail::class, fn (KendaraanLayakServiceMail $mail) => $mail->hasTo('pj@pemda.test'));
    }

    public function test_pegawai_without_email_returns_422_and_sends_nothing(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $kendaraan = Kendaraan::factory()->rusakRingan()->for(Pegawai::factory()->tanpaEmail())->create();
        Mail::fake();

        $response = $this->postJson("/api/v1/kendaraan/{$kendaraan->id}/notifikasi-service");

        $response->assertUnprocessable();
        Mail::assertNothingSent();
        $this->assertDatabaseCount('kdo_notifikasi_service', 0);
    }

    public function test_kendaraan_not_eligible_for_service_returns_422(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $kendaraan = Kendaraan::factory()->create(['kdo_tahun_produksi' => now()->year]);
        Mail::fake();

        $response = $this->postJson("/api/v1/kendaraan/{$kendaraan->id}/notifikasi-service");

        $response->assertUnprocessable()->assertJsonPath('message', 'Kendaraan belum memenuhi kriteria layak service.');
        Mail::assertNothingSent();
    }

    public function test_mail_content_escapes_pegawai_name(): void
    {
        $kendaraan = Kendaraan::factory()->rusakBerat()->for(Pegawai::factory()->state(['kdo_nama_pegawai' => "Budi <script>alert('x')</script>"]))->create();
        $kendaraan->load(['pegawai.perangkatDaerah', 'jadwalTerakhir']);

        $html = (new KendaraanLayakServiceMail($kendaraan, $kendaraan->alasanService(), ModeEmail::Dev))->render();

        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString("<script>alert('x')</script>", $html);
    }
}
