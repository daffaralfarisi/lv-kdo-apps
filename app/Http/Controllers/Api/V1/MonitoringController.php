<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\KriteriaService;
use App\Enums\ModeEmail;
use App\Http\Controllers\Api\V1\Concerns\MembacaParameterTabel;
use App\Http\Controllers\Controller;
use App\Http\Resources\KendaraanResource;
use App\Models\Kendaraan;
use App\Models\PerangkatDaerah;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Monitoring kendaraan layak service: umur > batas, rusak ringan, atau rusak berat.
 */
class MonitoringController extends Controller
{
    use MembacaParameterTabel;

    public function index(Request $request): AnonymousResourceCollection
    {
        $mode = ModeEmail::aktif();
        $kata = $this->kataKunci($request);
        $notifikasiModeAktif = fn (Builder $query) => $query->where('kdo_mode', $mode);

        $query = Kendaraan::query()
            ->layakService(KriteriaService::tryFrom($request->string('kriteria')->toString()))
            ->with(['pegawai.perangkatDaerah', 'jadwalTerakhir'])
            ->withMax(['notifikasiService as notifikasi_terakhir_pada' => $notifikasiModeAktif], 'kdo_created_at')
            ->when($kata, fn ($query) => $query->where(function ($query) use ($kata): void {
                $query->where('kdo_nomor_polisi', 'like', $kata)
                    ->orWhere('kdo_merek', 'like', $kata)
                    ->orWhere('kdo_tipe', 'like', $kata)
                    ->orWhereHas('pegawai', fn ($query) => $query->where('kdo_nama_pegawai', 'like', $kata));
            }))
            ->when($request->filled('kdo_perangkat_daerah_id'), fn ($query) => $query->whereHas(
                'pegawai',
                fn ($query) => $query->where('kdo_perangkat_daerah_id', $request->integer('kdo_perangkat_daerah_id')),
            ))
            ->when($request->query('status_notifikasi') === 'belum', fn ($query) => $query->whereDoesntHave('notifikasiService', $notifikasiModeAktif))
            ->when($request->query('status_notifikasi') === 'sudah', fn ($query) => $query->whereHas('notifikasiService', $notifikasiModeAktif));

        $this->terapkanUrutan($query, $request, ['kdo_nomor_polisi', 'kdo_tahun_produksi', 'kdo_kondisi', 'notifikasi_terakhir_pada'], 'kdo_tahun_produksi');

        return KendaraanResource::collection($query->paginate($this->perHalaman($request)));
    }

    public function ringkasan(): JsonResponse
    {
        $mode = ModeEmail::aktif();

        $perPerangkat = PerangkatDaerah::query()
            ->orderBy('kdo_nama_perangkat')
            ->withCount([
                'kendaraan as total',
                'kendaraan as layak_service' => fn ($query) => $query->layakService(),
                'kendaraan as umur' => fn ($query) => $query->layakService(KriteriaService::Umur),
                'kendaraan as rusak_ringan' => fn ($query) => $query->layakService(KriteriaService::RusakRingan),
                'kendaraan as rusak_berat' => fn ($query) => $query->layakService(KriteriaService::RusakBerat),
            ])
            ->get();

        $belumDinotifikasi = Kendaraan::query()
            ->layakService()
            ->whereDoesntHave('notifikasiService', fn ($query) => $query->where('kdo_mode', $mode))
            ->count();

        return response()->json(['data' => [
            'total_kendaraan' => $perPerangkat->sum('total'),
            'layak_service' => $perPerangkat->sum('layak_service'),
            'umur' => $perPerangkat->sum('umur'),
            'rusak_ringan' => $perPerangkat->sum('rusak_ringan'),
            'rusak_berat' => $perPerangkat->sum('rusak_berat'),
            'belum_dinotifikasi' => $belumDinotifikasi,
            'batas_umur_service' => config('kdo.batas_umur_service'),
            'mode_email' => [
                'kode' => $mode->value,
                'label' => $mode->label(),
                'penerima_dev' => $mode === ModeEmail::Dev ? config('kdo.email_notifikasi.penerima_dev') : null,
            ],
            'per_perangkat_daerah' => $perPerangkat->map(fn (PerangkatDaerah $perangkat): array => [
                'id' => $perangkat->id,
                'kdo_nama_perangkat' => $perangkat->kdo_nama_perangkat,
                'total' => $perangkat->total,
                'layak_service' => $perangkat->layak_service,
                'umur' => $perangkat->umur,
                'rusak_ringan' => $perangkat->rusak_ringan,
                'rusak_berat' => $perangkat->rusak_berat,
            ])->values(),
        ]]);
    }
}
