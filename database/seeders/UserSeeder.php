<?php

namespace Database\Seeders;

use App\Enums\RoleUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Dua akun bawaan: admin (semua menu + kelola user) dan user (semua menu operasional).
     */
    public function run(): void
    {
        User::query()->updateOrCreate(['kdo_email' => 'admin@kdo-apps.test'], [
            'kdo_nama_user' => 'Administrator KDO',
            'kdo_password' => 'password',
            'kdo_role' => RoleUser::Admin,
        ]);

        User::query()->updateOrCreate(['kdo_email' => 'user@kdo-apps.test'], [
            'kdo_nama_user' => 'Operator KDO',
            'kdo_password' => 'password',
            'kdo_role' => RoleUser::User,
        ]);
    }
}
