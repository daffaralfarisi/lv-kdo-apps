@php
    $pegawai = $kendaraan->pegawai;
    $jadwal = $kendaraan->jadwalTerakhir;
@endphp
<x-mail::message>
# Pemberitahuan Kendaraan Layak Service

@if ($mode === \App\Enums\ModeEmail::Dev)
<x-mail::panel>
**MODE DEVELOPMENT** — email ini dialihkan. Penerima sebenarnya: {{ $pegawai->kdo_email }}.
</x-mail::panel>
@endif

Yth. {{ $pegawai->kdo_nama_pegawai }} (NIP {{ $pegawai->kdo_nip }}),

Kendaraan dinas operasional yang menjadi tanggung jawab Bapak/Ibu telah memenuhi kriteria **layak service**.

<x-mail::table>
| Data | Keterangan |
|:--|:--|
| Nomor Polisi | {{ $kendaraan->kdo_nomor_polisi }} |
| Merek / Tipe | {{ $kendaraan->kdo_merek }} {{ $kendaraan->kdo_tipe }} |
| Tahun Produksi | {{ $kendaraan->kdo_tahun_produksi }} ({{ $kendaraan->umur() }} tahun) |
| Kondisi | {{ $kendaraan->kdo_kondisi->label() }} |
| Perangkat Daerah | {{ $pegawai->perangkatDaerah->kdo_nama_perangkat }} |
</x-mail::table>

**Alasan layak service:**

@foreach ($alasan as $item)
- {{ $item['label'] }}
@endforeach

@if ($jadwal && $jadwal->statusJadwal() !== \App\Enums\StatusJadwal::Selesai)
Jadwal service: **{{ $jadwal->kdo_service_mulai->format('d/m/Y') }}{{ $jadwal->kdo_service_selesai ? ' s.d. '.$jadwal->kdo_service_selesai->format('d/m/Y') : '' }}**.
@else
Kendaraan belum memiliki jadwal service aktif. Mohon koordinasi dengan pengelola KDO untuk penjadwalan.
@endif

Mohon segera ditindaklanjuti. Terima kasih.

Hormat kami,<br>
Pengelola {{ config('app.name') }}
</x-mail::message>
