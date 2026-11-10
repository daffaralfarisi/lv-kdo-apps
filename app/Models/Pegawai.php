<?php

namespace App\Models;

use Database\Factories\PegawaiFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('kdo_pegawai')]
#[Fillable(['kdo_perangkat_daerah_id', 'kdo_nama_pegawai', 'kdo_nip', 'kdo_jabatan', 'kdo_golongan', 'kdo_email'])]
class Pegawai extends Model
{
    /** @use HasFactory<PegawaiFactory> */
    use HasFactory;

    const CREATED_AT = 'kdo_created_at';

    const UPDATED_AT = 'kdo_updated_at';

    /**
     * Instansi tempat pegawai bertugas.
     *
     * @return BelongsTo<PerangkatDaerah, $this>
     */
    public function perangkatDaerah(): BelongsTo
    {
        return $this->belongsTo(PerangkatDaerah::class, 'kdo_perangkat_daerah_id');
    }

    /**
     * Kendaraan yang menjadi tanggung jawab pegawai.
     *
     * @return HasMany<Kendaraan, $this>
     */
    public function kendaraan(): HasMany
    {
        return $this->hasMany(Kendaraan::class, 'kdo_pegawai_id');
    }
}
