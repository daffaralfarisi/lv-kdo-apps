<?php

namespace App\Enums;

use App\Enums\Concerns\MemilikiOpsi;

enum KondisiKendaraan: string
{
    use MemilikiOpsi;

    case Baik = 'baik';
    case RusakRingan = 'rusak_ringan';
    case RusakBerat = 'rusak_berat';

    public function label(): string
    {
        return match ($this) {
            self::Baik => 'Baik',
            self::RusakRingan => 'Rusak Ringan',
            self::RusakBerat => 'Rusak Berat',
        };
    }
}
