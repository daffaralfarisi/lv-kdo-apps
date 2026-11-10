<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenjadwalanServiceRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kdo_kendaraan_id' => ['required', 'integer', Rule::exists('kdo_kendaraan', 'id')],
            'kdo_service_mulai' => ['required', 'date_format:Y-m-d'],
            'kdo_service_selesai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:kdo_service_mulai'],
            'kdo_keterangan' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
