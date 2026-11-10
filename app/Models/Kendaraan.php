<?php

namespace App\Models;

use App\Enums\KondisiKendaraan;
use App\Enums\KriteriaService;
use App\Enums\StatusKendaraan;
use Database\Factories\KendaraanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Table('kdo_kendaraan')]
#[Fillable([
    'kdo_pegawai_id', 'kdo_nomor_polisi', 'kdo_merek', 'kdo_tipe',
    'kdo_tahun_produksi', 'kdo_warna', 'kdo_kondisi', 'kdo_status',
])]
class Kendaraan extends Model
{
    /** @use HasFactory<KendaraanFactory> */
    use HasFactory;

    const CREATED_AT = 'kdo_created_at';

    const UPDATED_AT = 'kdo_updated_at';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kdo_tahun_produksi' => 'integer',
            'kdo_kondisi' => KondisiKendaraan::class,
            'kdo_status' => StatusKendaraan::class,
        ];
    }

    /**
     * Penanggung jawab kendaraan.
     *
     * @return BelongsTo<Pegawai, $this>
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'kdo_pegawai_id');
    }

    /**
     * @return HasMany<PenjadwalanService, $this>
     */
    public function penjadwalanService(): HasMany
    {
        return $this->hasMany(PenjadwalanService::class, 'kdo_kendaraan_id');
    }

    /**
     * Jadwal service dengan tanggal mulai paling akhir.
     *
     * @return HasOne<PenjadwalanService, $this>
     */
    public function jadwalTerakhir(): HasOne
    {
        return $this->hasOne(PenjadwalanService::class, 'kdo_kendaraan_id')->latestOfMany('kdo_service_mulai');
    }

    /**
     * @return HasMany<NotifikasiService, $this>
     */
    public function notifikasiService(): HasMany
    {
        return $this->hasMany(NotifikasiService::class, 'kdo_kendaraan_id');
    }

    /**
     * Tahun produksi terbaru yang masih tergolong "umur > batas" (mis. 2020 pada 2026).
     */
    public static function tahunBatasUmurService(): int
    {
        return now()->year - config('kdo.batas_umur_service') - 1;
    }

    /**
     * Kendaraan yang memenuhi minimal satu kriteria layak service, atau satu kriteria tertentu.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function layakService(Builder $query, ?KriteriaService $kriteria = null): void
    {
        match ($kriteria) {
            KriteriaService::Umur => $query->where('kdo_tahun_produksi', '<=', self::tahunBatasUmurService()),
            KriteriaService::RusakRingan => $query->where('kdo_kondisi', KondisiKendaraan::RusakRingan),
            KriteriaService::RusakBerat => $query->where('kdo_kondisi', KondisiKendaraan::RusakBerat),
            null => $query->where(function (Builder $query): void {
                $query->where('kdo_tahun_produksi', '<=', self::tahunBatasUmurService())
                    ->orWhereIn('kdo_kondisi', [KondisiKendaraan::RusakRingan, KondisiKendaraan::RusakBerat]);
            }),
        };
    }

    public function umur(): int
    {
        return max(0, now()->year - $this->kdo_tahun_produksi);
    }

    /**
     * Daftar kriteria layak service yang dipenuhi kendaraan ini.
     *
     * @return list<array{kode: string, label: string}>
     */
    public function alasanService(): array
    {
        $alasan = [];

        if ($this->kdo_tahun_produksi <= self::tahunBatasUmurService()) {
            $alasan[] = [
                'kode' => KriteriaService::Umur->value,
                'label' => 'Umur '.$this->umur().' tahun (> '.config('kdo.batas_umur_service').' tahun)',
            ];
        }

        if ($this->kdo_kondisi === KondisiKendaraan::RusakRingan) {
            $alasan[] = ['kode' => KriteriaService::RusakRingan->value, 'label' => 'Kondisi rusak ringan'];
        }

        if ($this->kdo_kondisi === KondisiKendaraan::RusakBerat) {
            $alasan[] = ['kode' => KriteriaService::RusakBerat->value, 'label' => 'Kondisi rusak berat'];
        }

        return $alasan;
    }
}
