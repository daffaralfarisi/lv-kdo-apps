<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * Login dua mode: request dari frontend (stateful) membuat session,
     * klien eksternal (tanpa session) menerima token Bearer Sanctum.
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kdo_email' => ['required', 'email'],
            'kdo_password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        if ($request->hasSession()) {
            $berhasil = Auth::guard('web')->attempt(
                ['kdo_email' => $data['kdo_email'], 'password' => $data['kdo_password']],
                $request->boolean('ingat_saya'),
            );

            if (! $berhasil) {
                throw ValidationException::withMessages(['kdo_email' => __('auth.failed')]);
            }

            $request->session()->regenerate();

            return response()->json([
                'message' => 'Berhasil masuk.',
                'data' => new UserResource(Auth::guard('web')->user()),
            ]);
        }

        $user = User::query()->where('kdo_email', $data['kdo_email'])->first();

        if (! $user || ! Hash::check($data['kdo_password'], $user->kdo_password)) {
            throw ValidationException::withMessages(['kdo_email' => __('auth.failed')]);
        }

        return response()->json([
            'message' => 'Berhasil masuk.',
            'data' => [
                'token' => $user->createToken($data['device_name'] ?? 'api')->plainTextToken,
                'token_type' => 'Bearer',
                'user' => new UserResource($user),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->user()->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        if ($request->hasSession()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['message' => 'Berhasil keluar.']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
}
