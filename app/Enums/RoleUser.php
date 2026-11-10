<?php

namespace App\Enums;

use App\Enums\Concerns\MemilikiOpsi;

enum RoleUser: string
{
    use MemilikiOpsi;

    case Admin = 'admin';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::User => 'User',
        };
    }
}
