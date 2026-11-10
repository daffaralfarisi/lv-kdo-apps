<?php

namespace App\Models;

use App\Enums\ModeEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('kdo_notifikasi_service')]
#[Fillable(['kdo_kendaraan_id', 'kdo_pegawai_id', 'kdo_user_id', 'kdo_mode', 'kdo_email_tujuan', 'kdo_alasan'])]
class NotifikasiService extends Model
{
    const CREATED_AT = 'kdo_created_at';

    const UPDATED_AT = 'kdo_updated_at';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kdo_mode' => ModeEmail::class,
        ];
    }

    /**
     * @return BelongsTo<Kendaraan, $this>
     */
    public function kendaraan(): BelongsTo
    {
        return $this->belongsTo(Kendaraan::class, 'kdo_kendaraan_id');
    }

    /**
     * @return BelongsTo<Pegawai, $this>
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'kdo_pegawai_id');
    }

    /**
     * Pengguna yang mengirim notifikasi.
     *
     * @return BelongsTo<User, $this>
     */
    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kdo_user_id');
    }
}
