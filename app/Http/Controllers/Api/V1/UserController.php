<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\MembacaParameterTabel;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Master user, khusus role admin (middleware can:admin).
 */
class UserController extends Controller
{
    use MembacaParameterTabel;

    public function index(Request $request): AnonymousResourceCollection
    {
        $kata = $this->kataKunci($request);

        $query = User::query()
            ->when($kata, fn ($query) => $query->where(function ($query) use ($kata): void {
                $query->where('kdo_nama_user', 'like', $kata)->orWhere('kdo_email', 'like', $kata);
            }))
            ->when($request->filled('kdo_role'), fn ($query) => $query->where('kdo_role', $request->string('kdo_role')));

        $this->terapkanUrutan($query, $request, ['kdo_nama_user', 'kdo_email', 'kdo_role', 'kdo_created_at'], 'kdo_nama_user');

        return UserResource::collection($query->paginate($this->perHalaman($request)));
    }

    public function store(UserRequest $request): JsonResponse
    {
        $user = User::query()->create($request->safe()->only(['kdo_nama_user', 'kdo_email', 'kdo_role', 'kdo_password']));

        return (new UserResource($user))
            ->additional(['message' => 'User berhasil ditambahkan.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public function update(UserRequest $request, User $user): UserResource
    {
        $data = $request->safe()->only(['kdo_nama_user', 'kdo_email', 'kdo_role']);

        if ($request->filled('kdo_password')) {
            $data['kdo_password'] = $request->validated('kdo_password');
        }

        $user->update($data);

        return (new UserResource($user))->additional(['message' => 'User berhasil diperbarui.']);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($user->is($request->user())) {
            return response()->json(['message' => 'Anda tidak dapat menghapus akun sendiri.'], 409);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus.']);
    }
}
