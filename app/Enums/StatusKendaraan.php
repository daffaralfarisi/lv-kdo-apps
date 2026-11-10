<?php

namespace App\Enums;

use App\Enums\Concerns\MemilikiOpsi;

enum StatusKendaraan: string
{
    use MemilikiOpsi;

    case Aktif = 'aktif';
    case DalamService = 'dalam_service';
    case TidakAktif = 'tidak_aktif';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::DalamService => 'Dalam Service',
            self::TidakAktif => 'Tidak Aktif',
        };
    }
}
