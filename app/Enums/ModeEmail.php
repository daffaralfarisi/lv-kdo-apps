<?php

namespace App\Enums;

use App\Enums\Concerns\MemilikiOpsi;

enum ModeEmail: string
{
    use MemilikiOpsi;

    case Dev = 'dev';
    case Prod = 'prod';

    /**
     * Mode yang berlaku menurut config/kdo.php; nilai tidak dikenal dianggap dev (aman).
     */
    public static function aktif(): self
    {
        return self::tryFrom((string) config('kdo.email_notifikasi.mode')) ?? self::Dev;
    }

    /**
     * Alamat tujuan email notifikasi sesuai mode.
     */
    public static function alamatTujuan(string $emailPegawai): string
    {
        if (self::aktif() === self::Prod) {
            return $emailPegawai;
        }

        return (string) config('kdo.email_notifikasi.penerima_dev');
    }

    public function label(): string
    {
        return match ($this) {
            self::Dev => 'Development',
            self::Prod => 'Production',
        };
    }
}
