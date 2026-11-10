<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\StatusJadwal;
use App\Http\Controllers\Api\V1\Concerns\MembacaParameterTabel;
use App\Http\Controllers\Controller;
use App\Http\Requests\PenjadwalanServiceRequest;
use App\Http\Resources\PenjadwalanServiceResource;
use App\Models\PenjadwalanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PenjadwalanServiceController extends Controller
{
    use MembacaParameterTabel;

    public function index(Request $request): AnonymousResourceCollection
    {
        $kata = $this->kataKunci($request);
        $status = StatusJadwal::tryFrom($request->string('kdo_status_jadwal')->toString());

        $query = PenjadwalanService::query()
            ->with(['kendaraan.pegawai.perangkatDaerah'])
            ->when($kata, fn ($query) => $query->whereHas('kendaraan', fn ($query) => $query->where('kdo_nomor_polisi', 'like', $kata)))
            ->when($request->filled('kdo_kendaraan_id'), fn ($query) => $query->where('kdo_kendaraan_id', $request->integer('kdo_kendaraan_id')))
            ->when($request->filled('kdo_perangkat_daerah_id'), fn ($query) => $query->whereHas(
                'kendaraan.pegawai',
                fn ($query) => $query->where('kdo_perangkat_daerah_id', $request->integer('kdo_perangkat_daerah_id')),
            ))
            ->when($status, fn ($query) => $query->berstatus($status))
            ->when($request->date('tanggal_dari'), fn ($query, $dari) => $query->whereDate('kdo_service_mulai', '>=', $dari))
            ->when($request->date('tanggal_sampai'), fn ($query, $sampai) => $query->whereDate('kdo_service_mulai', '<=', $sampai));

        $this->terapkanUrutan($query, $request, ['kdo_service_mulai', 'kdo_service_selesai', 'kdo_created_at'], 'kdo_service_mulai', 'desc');

        return PenjadwalanServiceResource::collection($query->paginate($this->perHalaman($request)));
    }

    public function store(PenjadwalanServiceRequest $request): JsonResponse
    {
        $jadwal = PenjadwalanService::query()->create($request->validated())->load(['kendaraan.pegawai.perangkatDaerah']);

        return (new PenjadwalanServiceResource($jadwal))
            ->additional(['message' => 'Jadwal service berhasil ditambahkan.'])
            ->response()
            ->setStatusCode(201);
    }

    public function show(PenjadwalanService $penjadwalanService): PenjadwalanServiceResource
    {
        return new PenjadwalanServiceResource($penjadwalanService->load(['kendaraan.pegawai.perangkatDaerah']));
    }

    public function update(PenjadwalanServiceRequest $request, PenjadwalanService $penjadwalanService): PenjadwalanServiceResource
    {
        $penjadwalanService->update($request->validated());

        return (new PenjadwalanServiceResource($penjadwalanService->load(['kendaraan.pegawai.perangkatDaerah'])))
            ->additional(['message' => 'Jadwal service berhasil diperbarui.']);
    }

    public function destroy(PenjadwalanService $penjadwalanService): JsonResponse
    {
        $penjadwalanService->delete();

        return response()->json(['message' => 'Jadwal service berhasil dihapus.']);
    }
}
