<?php

namespace App\Models;

use App\Enums\StatusJadwal;
use Database\Factories\PenjadwalanServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('kdo_penjadwalan_service')]
#[Fillable(['kdo_kendaraan_id', 'kdo_service_mulai', 'kdo_service_selesai', 'kdo_keterangan'])]
class PenjadwalanService extends Model
{
    /** @use HasFactory<PenjadwalanServiceFactory> */
    use HasFactory;

    const CREATED_AT = 'kdo_created_at';

    const UPDATED_AT = 'kdo_updated_at';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kdo_service_mulai' => 'date',
            'kdo_service_selesai' => 'date',
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
     * Status jadwal diturunkan dari tanggal (tidak disimpan di database).
     */
    public function statusJadwal(): StatusJadwal
    {
        $hariIni = today();

        if ($this->kdo_service_mulai->greaterThan($hariIni)) {
            return StatusJadwal::Dijadwalkan;
        }

        if ($this->kdo_service_selesai !== null && $this->kdo_service_selesai->lessThan($hariIni)) {
            return StatusJadwal::Selesai;
        }

        return StatusJadwal::Berlangsung;
    }

    /**
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function berstatus(Builder $query, StatusJadwal $status): void
    {
        $hariIni = today()->toDateString();

        match ($status) {
            StatusJadwal::Dijadwalkan => $query->whereDate('kdo_service_mulai', '>', $hariIni),
            StatusJadwal::Selesai => $query->whereDate('kdo_service_selesai', '<', $hariIni),
            StatusJadwal::Berlangsung => $query->whereDate('kdo_service_mulai', '<=', $hariIni)
                ->where(function (Builder $query) use ($hariIni): void {
                    $query->whereNull('kdo_service_selesai')->orWhereDate('kdo_service_selesai', '>=', $hariIni);
                }),
        };
    }
}
