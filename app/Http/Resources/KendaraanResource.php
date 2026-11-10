<?php

namespace App\Http\Resources;

use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @mixin Kendaraan
 */
class KendaraanResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kdo_pegawai_id' => $this->kdo_pegawai_id,
            'kdo_nomor_polisi' => $this->kdo_nomor_polisi,
            'kdo_merek' => $this->kdo_merek,
            'kdo_tipe' => $this->kdo_tipe,
            'kdo_tahun_produksi' => $this->kdo_tahun_produksi,
            'kdo_umur' => $this->umur(),
            'kdo_warna' => $this->kdo_warna,
            'kdo_kondisi' => $this->kdo_kondisi->value,
            'kdo_kondisi_label' => $this->kdo_kondisi->label(),
            'kdo_status' => $this->kdo_status->value,
            'kdo_status_label' => $this->kdo_status->label(),
            'kdo_alasan_service' => $this->alasanService(),
            'notifikasi_terakhir_pada' => $this->when(
                array_key_exists('notifikasi_terakhir_pada', $this->resource->getAttributes()),
                fn (): ?string => $this->notifikasi_terakhir_pada
                    ? Carbon::parse($this->notifikasi_terakhir_pada)->toIso8601String()
                    : null,
            ),
            'pegawai' => new PegawaiResource($this->whenLoaded('pegawai')),
            'jadwal_terakhir' => new PenjadwalanServiceResource($this->whenLoaded('jadwalTerakhir')),
            'penjadwalan_service' => PenjadwalanServiceResource::collection($this->whenLoaded('penjadwalanService')),
            'kdo_created_at' => $this->kdo_created_at?->toIso8601String(),
            'kdo_updated_at' => $this->kdo_updated_at?->toIso8601String(),
        ];
    }
}
