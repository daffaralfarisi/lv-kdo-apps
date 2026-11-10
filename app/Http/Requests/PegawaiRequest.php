<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PegawaiRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kdo_perangkat_daerah_id' => ['required', 'integer', Rule::exists('kdo_perangkat_daerah', 'id')],
            'kdo_nama_pegawai' => ['required', 'string', 'max:255'],
            'kdo_nip' => ['required', 'digits:18', Rule::unique('kdo_pegawai', 'kdo_nip')->ignore($this->route('pegawai'))],
            'kdo_jabatan' => ['required', 'string', 'max:255'],
            'kdo_golongan' => ['required', Rule::in(config('kdo.golongan'))],
            'kdo_email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
