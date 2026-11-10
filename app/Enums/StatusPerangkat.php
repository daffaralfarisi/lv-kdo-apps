<?php

namespace App\Enums;

use App\Enums\Concerns\MemilikiOpsi;

enum StatusPerangkat: string
{
    use MemilikiOpsi;

    case Aktif = 'aktif';
    case TidakAktif = 'tidak_aktif';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::TidakAktif => 'Tidak Aktif',
        };
    }
}
