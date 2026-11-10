<?php

namespace App\Models;

use App\Enums\StatusPerangkat;
use Database\Factories\PerangkatDaerahFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

#[Table('kdo_perangkat_daerah')]
#[Fillable(['kdo_nama_perangkat', 'kdo_status_perangkat'])]
class PerangkatDaerah extends Model
{
    /** @use HasFactory<PerangkatDaerahFactory> */
    use HasFactory;

    const CREATED_AT = 'kdo_created_at';

    const UPDATED_AT = 'kdo_updated_at';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kdo_status_perangkat' => StatusPerangkat::class,
        ];
    }

    /**
     * @return HasMany<Pegawai, $this>
     */
    public function pegawai(): HasMany
    {
        return $this->hasMany(Pegawai::class, 'kdo_perangkat_daerah_id');
    }

    /**
     * Kendaraan milik perangkat daerah, melalui pegawai penanggung jawabnya.
     *
     * @return HasManyThrough<Kendaraan, Pegawai, $this>
     */
    public function kendaraan(): HasManyThrough
    {
        return $this->hasManyThrough(Kendaraan::class, Pegawai::class, 'kdo_perangkat_daerah_id', 'kdo_pegawai_id');
    }
}
