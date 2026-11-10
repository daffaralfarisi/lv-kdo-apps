<?php

namespace App\Http\Resources;

use App\Models\PerangkatDaerah;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PerangkatDaerah
 */
class PerangkatDaerahResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kdo_nama_perangkat' => $this->kdo_nama_perangkat,
            'kdo_status_perangkat' => $this->kdo_status_perangkat->value,
            'kdo_status_perangkat_label' => $this->kdo_status_perangkat->label(),
            'pegawai_count' => $this->whenCounted('pegawai'),
            'kendaraan_count' => $this->whenCounted('kendaraan'),
            'kendaraan' => KendaraanResource::collection($this->whenLoaded('kendaraan')),
            'kdo_created_at' => $this->kdo_created_at?->toIso8601String(),
            'kdo_updated_at' => $this->kdo_updated_at?->toIso8601String(),
        ];
    }
}
