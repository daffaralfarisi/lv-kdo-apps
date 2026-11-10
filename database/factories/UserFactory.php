<?php

namespace Database\Factories;

use App\Enums\RoleUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kdo_nama_user' => fake()->name(),
            'kdo_email' => fake()->unique()->safeEmail(),
            'kdo_password' => static::$password ??= Hash::make('password'),
            'kdo_role' => RoleUser::User,
            'kdo_remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['kdo_role' => RoleUser::Admin]);
    }
}
