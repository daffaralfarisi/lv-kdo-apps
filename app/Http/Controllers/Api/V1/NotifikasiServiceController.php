<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ModeEmail;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotifikasiServiceResource;
use App\Mail\KendaraanLayakServiceMail;
use App\Models\Kendaraan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class NotifikasiServiceController extends Controller
{
    /**
     * Riwayat email notifikasi sebuah kendaraan (semua mode).
     */
    public function index(Kendaraan $kendaraan): AnonymousResourceCollection
    {
        return NotifikasiServiceResource::collection(
            $kendaraan->notifikasiService()->with('pengirim')->latest('kdo_created_at')->latest('id')->limit(100)->get(),
        );
    }

    /**
     * Kirim email layak service ke penanggung jawab. Mode dev mengalihkan email ke penerima dev.
     */
    public function store(Request $request, Kendaraan $kendaraan): JsonResponse
    {
        $kendaraan->load(['pegawai.perangkatDaerah', 'jadwalTerakhir']);
        $alasan = $kendaraan->alasanService();
        $pegawai = $kendaraan->pegawai;

        if ($alasan === []) {
            return response()->json(['message' => 'Kendaraan belum memenuhi kriteria layak service.'], 422);
        }

        if (blank($pegawai->kdo_email)) {
            return response()->json(['message' => 'Penanggung jawab belum memiliki alamat email. Lengkapi data pegawai terlebih dahulu.'], 422);
        }

        $mode = ModeEmail::aktif();
        $tujuan = ModeEmail::alamatTujuan($pegawai->kdo_email);

        try {
            Mail::to($tujuan)->send(new KendaraanLayakServiceMail($kendaraan, $alasan, $mode));
        } catch (TransportExceptionInterface $exception) {
            report($exception);

            return response()->json(['message' => 'Email gagal dikirim. Periksa konfigurasi MAIL_* di file .env.'], 502);
        }

        $notifikasi = $kendaraan->notifikasiService()->create([
            'kdo_pegawai_id' => $pegawai->id,
            'kdo_user_id' => $request->user()->id,
            'kdo_mode' => $mode,
            'kdo_email_tujuan' => $tujuan,
            'kdo_alasan' => collect($alasan)->pluck('label')->implode('; '),
        ]);

        $pesan = $mode === ModeEmail::Dev
            ? "Mode development: email dialihkan ke {$tujuan} (penerima asli {$pegawai->kdo_email})."
            : "Email notifikasi terkirim ke {$tujuan}.";

        return (new NotifikasiServiceResource($notifikasi))
            ->additional(['message' => $pesan])
            ->response()
            ->setStatusCode(201);
    }
}
