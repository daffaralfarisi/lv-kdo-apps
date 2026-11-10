<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\MembacaParameterTabel;
use App\Http\Controllers\Controller;
use App\Http\Requests\PerangkatDaerahRequest;
use App\Http\Resources\PerangkatDaerahResource;
use App\Models\PerangkatDaerah;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PerangkatDaerahController extends Controller
{
    use MembacaParameterTabel;

    public function index(Request $request): AnonymousResourceCollection
    {
        $kata = $this->kataKunci($request);

        $query = PerangkatDaerah::query()
            ->withCount(['pegawai', 'kendaraan'])
            ->when($kata, fn ($query) => $query->where('kdo_nama_perangkat', 'like', $kata))
            ->when($request->filled('kdo_status_perangkat'), fn ($query) => $query->where('kdo_status_perangkat', $request->string('kdo_status_perangkat')));

        $this->terapkanUrutan($query, $request, ['kdo_nama_perangkat', 'kdo_status_perangkat', 'pegawai_count', 'kendaraan_count', 'kdo_created_at'], 'kdo_nama_perangkat');

        return PerangkatDaerahResource::collection($query->paginate($this->perHalaman($request)));
    }

    public function store(PerangkatDaerahRequest $request): JsonResponse
    {
        $perangkatDaerah = PerangkatDaerah::query()->create($request->validated());

        return (new PerangkatDaerahResource($perangkatDaerah))
            ->additional(['message' => 'Perangkat daerah berhasil ditambahkan.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(PerangkatDaerah $perangkatDaerah): PerangkatDaerahResource
    {
        return new PerangkatDaerahResource($perangkatDaerah->loadCount(['pegawai', 'kendaraan'])->load([
            'kendaraan' => fn ($query) => $query->with('pegawai')->orderBy('kdo_nomor_polisi'),
        ]));
    }

    public function update(PerangkatDaerahRequest $request, PerangkatDaerah $perangkatDaerah): PerangkatDaerahResource
    {
        $perangkatDaerah->update($request->validated());

        return (new PerangkatDaerahResource($perangkatDaerah))->additional(['message' => 'Perangkat daerah berhasil diperbarui.']);
    }

    public function destroy(PerangkatDaerah $perangkatDaerah): JsonResponse
    {
        $jumlah = $perangkatDaerah->pegawai()->count();

        if ($jumlah > 0) {
            return response()->json([
                'message' => "Perangkat daerah masih memiliki {$jumlah} pegawai. Pindahkan atau hapus pegawai terlebih dahulu.",
            ], 409);
        }

        $perangkatDaerah->delete();

        return response()->json(['message' => 'Perangkat daerah berhasil dihapus.']);
    }
}
