<?php

namespace App\Models;

use App\Enums\RoleUser;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

#[Table('kdo_user')]
#[Fillable(['kdo_nama_user', 'kdo_email', 'kdo_password', 'kdo_role'])]
#[Hidden(['kdo_password', 'kdo_remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory;

    const CREATED_AT = 'kdo_created_at';

    const UPDATED_AT = 'kdo_updated_at';

    /**
     * Kolom password & remember token mengikuti konvensi prefix kdo_.
     */
    protected $authPasswordName = 'kdo_password';

    protected $rememberTokenName = 'kdo_remember_token';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kdo_password' => 'hashed',
            'kdo_role' => RoleUser::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->kdo_role === RoleUser::Admin;
    }
}
