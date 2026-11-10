<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_role_is_forbidden_from_master_user_with_403(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/v1/users', []);

        $response->assertForbidden();
    }

    public function test_admin_creates_user_with_hashed_password_and_returns_201(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $response = $this->postJson('/api/v1/users', [
            'kdo_nama_user' => 'Operator Baru',
            'kdo_email' => 'baru@kdo-apps.test',
            'kdo_role' => 'user',
            'kdo_password' => 'rahasia123',
            'kdo_password_confirmation' => 'rahasia123',
        ]);

        $response->assertCreated()->assertJsonMissingPath('data.kdo_password');
        $baru = User::query()->where('kdo_email', 'baru@kdo-apps.test')->firstOrFail();
        $this->assertTrue(password_verify('rahasia123', $baru->kdo_password));
    }

    public function test_admin_cannot_delete_own_account_and_receives_409(): void
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $response = $this->deleteJson('/api/v1/users/'.$admin->id);

        $response->assertConflict();
        $this->assertModelExists($admin);
    }

    public function test_admin_cannot_demote_own_role(): void
    {
        $admin = User::factory()->admin()->create();
        Sanctum::actingAs($admin);

        $response = $this->putJson('/api/v1/users/'.$admin->id, [
            'kdo_nama_user' => $admin->kdo_nama_user,
            'kdo_email' => $admin->kdo_email,
            'kdo_role' => 'user',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['kdo_role' => 'Anda tidak dapat menurunkan role akun sendiri.']);
    }
}
