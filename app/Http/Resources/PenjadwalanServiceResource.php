<?php

namespace App\Http\Resources;

use App\Models\PenjadwalanService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PenjadwalanService
 */
class PenjadwalanServiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->statusJadwal();

        return [
            'id' => $this->id,
            'kdo_kendaraan_id' => $this->kdo_kendaraan_id,
            'kdo_service_mulai' => $this->kdo_service_mulai?->toDateString(),
            'kdo_service_selesai' => $this->kdo_service_selesai?->toDateString(),
            'kdo_keterangan' => $this->kdo_keterangan,
            'kdo_status_jadwal' => $status->value,
            'kdo_status_jadwal_label' => $status->label(),
            'kendaraan' => new KendaraanResource($this->whenLoaded('kendaraan')),
            'kdo_created_at' => $this->kdo_created_at?->toIso8601String(),
            'kdo_updated_at' => $this->kdo_updated_at?->toIso8601String(),
        ];
    }
}
