<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kdo_nama_user' => $this->kdo_nama_user,
            'kdo_email' => $this->kdo_email,
            'kdo_role' => $this->kdo_role->value,
            'kdo_role_label' => $this->kdo_role->label(),
            'kdo_created_at' => $this->kdo_created_at?->toIso8601String(),
            'kdo_updated_at' => $this->kdo_updated_at?->toIso8601String(),
        ];
    }
}
