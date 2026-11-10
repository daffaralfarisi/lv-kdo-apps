<?php

namespace App\Http\Controllers\Api\V1\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait MembacaParameterTabel
{
    /**
     * Jumlah baris per halaman (?per_page=), dibatasi 1–500.
     */
    protected function perHalaman(Request $request): int
    {
        return min(max($request->integer('per_page', 10), 1), 500);
    }

    /**
     * Urutan dari ?sort=&direction= yang dicocokkan ke allow-list kolom.
     *
     * @param  Builder<Model>  $query
     * @param  list<string>  $kolomBoleh
     */
    protected function terapkanUrutan(Builder $query, Request $request, array $kolomBoleh, string $default, string $arahDefault = 'asc'): void
    {
        $kolom = in_array($request->query('sort'), $kolomBoleh, true) ? $request->query('sort') : $default;
        $arah = in_array($request->query('direction'), ['asc', 'desc'], true) ? $request->query('direction') : $arahDefault;

        $query->orderBy($kolom, $arah)->orderBy($query->getModel()->getQualifiedKeyName(), $arah);
    }

    protected function kataKunci(Request $request): ?string
    {
        $kata = trim($request->string('search')->toString());

        return $kata === '' ? null : '%'.addcslashes($kata, '%_\\').'%';
    }
}
