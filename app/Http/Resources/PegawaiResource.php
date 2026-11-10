<?php

namespace App\Http\Resources;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Pegawai
 */
class PegawaiResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kdo_perangkat_daerah_id' => $this->kdo_perangkat_daerah_id,
            'kdo_nama_pegawai' => $this->kdo_nama_pegawai,
            'kdo_nip' => $this->kdo_nip,
            'kdo_jabatan' => $this->kdo_jabatan,
            'kdo_golongan' => $this->kdo_golongan,
            'kdo_email' => $this->kdo_email,
            'perangkat_daerah' => new PerangkatDaerahResource($this->whenLoaded('perangkatDaerah')),
            'kendaraan_count' => $this->whenCounted('kendaraan'),
            'kendaraan' => KendaraanResource::collection($this->whenLoaded('kendaraan')),
            'kdo_created_at' => $this->kdo_created_at?->toIso8601String(),
            'kdo_updated_at' => $this->kdo_updated_at?->toIso8601String(),
        ];
    }
}
