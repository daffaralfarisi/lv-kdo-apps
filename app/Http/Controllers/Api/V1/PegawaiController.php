<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\MembacaParameterTabel;
use App\Http\Controllers\Controller;
use App\Http\Requests\PegawaiRequest;
use App\Http\Resources\PegawaiResource;
use App\Models\Pegawai;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PegawaiController extends Controller
{
    use MembacaParameterTabel;

    public function index(Request $request): AnonymousResourceCollection
    {
        $kata = $this->kataKunci($request);

        $query = Pegawai::query()
            ->with('perangkatDaerah')
            ->withCount('kendaraan')
            ->when($kata, fn ($query) => $query->where(function ($query) use ($kata): void {
                $query->where('kdo_nama_pegawai', 'like', $kata)
                    ->orWhere('kdo_nip', 'like', $kata)
                    ->orWhere('kdo_jabatan', 'like', $kata);
            }))
            ->when($request->filled('kdo_golongan'), fn ($query) => $query->where('kdo_golongan', $request->string('kdo_golongan')))
            ->when($request->filled('kdo_perangkat_daerah_id'), fn ($query) => $query->where('kdo_perangkat_daerah_id', $request->integer('kdo_perangkat_daerah_id')));

        $this->terapkanUrutan($query, $request, ['kdo_nama_pegawai', 'kdo_nip', 'kdo_jabatan', 'kdo_golongan', 'kendaraan_count', 'kdo_created_at'], 'kdo_nama_pegawai');

        return PegawaiResource::collection($query->paginate($this->perHalaman($request)));
    }

    public function store(PegawaiRequest $request): JsonResponse
    {
        $pegawai = Pegawai::query()->create($request->validated())->load('perangkatDaerah');

        return (new PegawaiResource($pegawai))
            ->additional(['message' => 'Pegawai berhasil ditambahkan.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Pegawai $pegawai): PegawaiResource
    {
        return new PegawaiResource($pegawai->loadCount('kendaraan')->load([
            'perangkatDaerah',
            'kendaraan' => fn ($query) => $query->orderBy('kdo_nomor_polisi'),
        ]));
    }

    public function update(PegawaiRequest $request, Pegawai $pegawai): PegawaiResource
    {
        $pegawai->update($request->validated());

        return (new PegawaiResource($pegawai->load('perangkatDaerah')))->additional(['message' => 'Pegawai berhasil diperbarui.']);
    }

    public function destroy(Pegawai $pegawai): JsonResponse
    {
        $jumlah = $pegawai->kendaraan()->count();

        if ($jumlah > 0) {
            return response()->json([
                'message' => "Pegawai masih menjadi penanggung jawab {$jumlah} kendaraan. Alihkan penanggung jawab terlebih dahulu.",
            ], 409);
        }

        $pegawai->delete();

        return response()->json(['message' => 'Pegawai berhasil dihapus.']);
    }
}
