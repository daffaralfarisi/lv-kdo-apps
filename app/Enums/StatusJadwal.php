<?php

namespace App\Enums;

use App\Enums\Concerns\MemilikiOpsi;

enum StatusJadwal: string
{
    use MemilikiOpsi;

    case Dijadwalkan = 'dijadwalkan';
    case Berlangsung = 'berlangsung';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Dijadwalkan => 'Dijadwalkan',
            self::Berlangsung => 'Berlangsung',
            self::Selesai => 'Selesai',
        };
    }
}
