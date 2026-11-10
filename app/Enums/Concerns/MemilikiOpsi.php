<?php

namespace App\Enums\Concerns;

trait MemilikiOpsi
{
    /**
     * Daftar opsi untuk select/filter.
     *
     * @return array<string, string>
     */
    public static function opsi(): array
    {
        $opsi = [];

        foreach (self::cases() as $case) {
            $opsi[$case->value] = $case->label();
        }

        return $opsi;
    }

    /**
     * @return list<string>
     */
    public static function nilai(): array
    {
        return array_column(self::cases(), 'value');
    }
}
