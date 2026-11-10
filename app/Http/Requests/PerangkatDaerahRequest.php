<?php

namespace App\Http\Requests;

use App\Enums\StatusPerangkat;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PerangkatDaerahRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kdo_nama_perangkat' => [
                'required', 'string', 'max:255',
                Rule::unique('kdo_perangkat_daerah', 'kdo_nama_perangkat')->ignore($this->route('perangkatDaerah')),
            ],
            'kdo_status_perangkat' => ['required', Rule::enum(StatusPerangkat::class)],
        ];
    }
}
