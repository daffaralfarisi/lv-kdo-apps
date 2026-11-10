<?php

namespace App\Enums;

use App\Enums\Concerns\MemilikiOpsi;

enum KriteriaService: string
{
    use MemilikiOpsi;

    case Umur = 'umur';
    case RusakRingan = 'rusak_ringan';
    case RusakBerat = 'rusak_berat';

    public function label(): string
    {
        return match ($this) {
            self::Umur => 'Tahun produksi > '.config('kdo.batas_umur_service').' tahun',
            self::RusakRingan => 'Kondisi rusak ringan',
            self::RusakBerat => 'Kondisi rusak berat',
        };
    }
}
