<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_external_client_receives_bearer_token_with_valid_credentials(): void
    {
        $user = User::factory()->create(['kdo_email' => 'operator@kdo-apps.test']);

        $response = $this->postJson('/api/v1/auth/login', [
            'kdo_email' => 'operator@kdo-apps.test',
            'kdo_password' => 'password',
            'device_name' => 'postman',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.id', $user->id);
        $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user->id, 'name' => 'postman']);
    }

    public function test_wrong_password_returns_422_with_indonesian_message(): void
    {
        User::factory()->create(['kdo_email' => 'operator@kdo-apps.test']);

        $response = $this->postJson('/api/v1/auth/login', ['kdo_email' => 'operator@kdo-apps.test', 'kdo_password' => 'salah']);

        $response->assertUnprocessable()->assertJsonValidationErrors(['kdo_email' => 'Email atau password salah.']);
    }

    public function test_request_without_token_returns_401(): void
    {
        $response = $this->getJson('/api/v1/kendaraan');

        $response->assertUnauthorized()->assertJsonPath('message', 'Sesi berakhir atau token tidak valid. Silakan masuk kembali.');
    }
}
