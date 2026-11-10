<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\MembacaParameterTabel;
use App\Http\Controllers\Controller;
use App\Http\Requests\KendaraanRequest;
use App\Http\Resources\KendaraanResource;
use App\Models\Kendaraan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class KendaraanController extends Controller
{
    use MembacaParameterTabel;

    public function index(Request $request): AnonymousResourceCollection
    {
        $kata = $this->kataKunci($request);

        $query = Kendaraan::query()
            ->with(['pegawai.perangkatDaerah'])
            ->when($kata, fn ($query) => $query->where(function ($query) use ($kata): void {
                $query->where('kdo_nomor_polisi', 'like', $kata)
                    ->orWhere('kdo_merek', 'like', $kata)
                    ->orWhere('kdo_tipe', 'like', $kata);
            }))
            ->when($request->filled('kdo_perangkat_daerah_id'), fn ($query) => $query->whereHas(
                'pegawai',
                fn ($query) => $query->where('kdo_perangkat_daerah_id', $request->integer('kdo_perangkat_daerah_id')),
            ));

        foreach (['kdo_pegawai_id', 'kdo_kondisi', 'kdo_status'] as $filter) {
            $query->when($request->filled($filter), fn ($query) => $query->where($filter, $request->string($filter)));
        }

        $this->terapkanUrutan($query, $request, [
            'kdo_nomor_polisi', 'kdo_merek', 'kdo_tipe', 'kdo_tahun_produksi', 'kdo_warna', 'kdo_kondisi', 'kdo_status', 'kdo_created_at',
        ], 'kdo_nomor_polisi');

        return KendaraanResource::collection($query->paginate($this->perHalaman($request)));
    }

    public function store(KendaraanRequest $request): JsonResponse
    {
        $kendaraan = Kendaraan::query()->create($request->validated())->load(['pegawai.perangkatDaerah']);

        return (new KendaraanResource($kendaraan))
            ->additional(['message' => 'Kendaraan berhasil ditambahkan.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(Kendaraan $kendaraan): KendaraanResource
    {
        return new KendaraanResource($kendaraan->load([
            'pegawai.perangkatDaerah',
            'penjadwalanService' => fn ($query) => $query->orderByDesc('kdo_service_mulai'),
        ]));
    }

    public function update(KendaraanRequest $request, Kendaraan $kendaraan): KendaraanResource
    {
        $kendaraan->update($request->validated());

        return (new KendaraanResource($kendaraan->load(['pegawai.perangkatDaerah'])))
            ->additional(['message' => 'Kendaraan berhasil diperbarui.']);
    }

    /**
     * Jadwal service dan riwayat notifikasi ikut terhapus (cascade).
     */
    public function destroy(Kendaraan $kendaraan): JsonResponse
    {
        $kendaraan->delete();

        return response()->json(['message' => 'Kendaraan berhasil dihapus.']);
    }
}
