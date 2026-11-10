<?php

namespace App\Http\Requests;

use App\Enums\KondisiKendaraan;
use App\Enums\StatusKendaraan;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KendaraanRequest extends FormRequest
{
    /**
     * Normalisasi nomor polisi: huruf besar dan spasi tunggal antar-kelompok (b1234pqa -> B 1234 PQA).
     */
    protected function prepareForValidation(): void
    {
        if (! is_string($this->input('kdo_nomor_polisi'))) {
            return;
        }

        $nomor = Str::upper(preg_replace('/\s+/', '', $this->input('kdo_nomor_polisi')));
        $nomor = preg_replace('/^([A-Z]{1,2})(\d{1,4})([A-Z]{0,3})$/', '$1 $2 $3', $nomor);

        $this->merge(['kdo_nomor_polisi' => trim($nomor)]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kdo_pegawai_id' => ['required', 'integer', Rule::exists('kdo_pegawai', 'id')],
            'kdo_nomor_polisi' => [
                'required', 'string', 'max:15', 'regex:/^[A-Z]{1,2} \d{1,4}( [A-Z]{1,3})?$/',
                Rule::unique('kdo_kendaraan', 'kdo_nomor_polisi')->ignore($this->route('kendaraan')),
            ],
            'kdo_merek' => ['required', 'string', 'max:50'],
            'kdo_tipe' => ['required', 'string', 'max:100'],
            'kdo_tahun_produksi' => ['required', 'integer', 'min:1980', 'max:'.now()->year],
            'kdo_warna' => ['required', 'string', 'max:30'],
            'kdo_kondisi' => ['required', Rule::enum(KondisiKendaraan::class)],
            'kdo_status' => ['required', Rule::enum(StatusKendaraan::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kdo_nomor_polisi.regex' => 'Format nomor polisi tidak valid. Contoh: B 1234 PQA.',
        ];
    }
}
