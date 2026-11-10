<?php

namespace App\Http\Requests;

use App\Enums\RoleUser;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UserRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'kdo_nama_user' => ['required', 'string', 'max:255'],
            'kdo_email' => ['required', 'email', 'max:255', Rule::unique('kdo_user', 'kdo_email')->ignore($user)],
            'kdo_role' => ['required', Rule::enum(RoleUser::class)],
            'kdo_password' => [$user ? 'nullable' : 'required', 'string', Password::min(8), 'confirmed'],
        ];
    }

    /**
     * Admin tidak boleh menurunkan role akun sendiri agar selalu ada admin yang aktif.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $user = $this->route('user');

                if ($user instanceof User
                    && $user->is($this->user())
                    && $this->input('kdo_role') !== RoleUser::Admin->value
                    && ! $validator->errors()->has('kdo_role')) {
                    $validator->errors()->add('kdo_role', 'Anda tidak dapat menurunkan role akun sendiri.');
                }
            },
        ];
    }
}
