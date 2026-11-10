<?php

namespace App\Http\Resources;

use App\Models\NotifikasiService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin NotifikasiService
 */
class NotifikasiServiceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kdo_kendaraan_id' => $this->kdo_kendaraan_id,
            'kdo_pegawai_id' => $this->kdo_pegawai_id,
            'kdo_user_id' => $this->kdo_user_id,
            'kdo_mode' => $this->kdo_mode->value,
            'kdo_mode_label' => $this->kdo_mode->label(),
            'kdo_email_tujuan' => $this->kdo_email_tujuan,
            'kdo_alasan' => $this->kdo_alasan,
            'pengirim' => new UserResource($this->whenLoaded('pengirim')),
            'kdo_created_at' => $this->kdo_created_at?->toIso8601String(),
        ];
    }
}
