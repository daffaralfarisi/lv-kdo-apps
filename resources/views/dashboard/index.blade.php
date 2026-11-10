@extends('layouts.app')
@section('judul', 'Dashboard Monitoring')
@section('subjudul', 'Pantau kendaraan dinas yang layak service dan kirim notifikasi ke penanggung jawab')

@section('konten')
<div class="row">
    @foreach ([
        ['statTotal', 'Total Kendaraan', 'fa-car', ''],
        ['statLayak', 'Layak Service', 'fa-tools', 'ungu'],
        ['statUmur', 'Umur > '.config('kdo.batas_umur_service').' Tahun', 'fa-history', 'amber'],
        ['statRingan', 'Rusak Ringan', 'fa-exclamation-triangle', 'amber'],
        ['statBerat', 'Rusak Berat', 'fa-car-crash', 'merah'],
    ] as [$id, $label, $ikon, $warna])
        <div class="col-6 col-md-4 col-xl mb-3">
            <div class="kdo-card kdo-stat">
                <div class="kdo-stat-icon {{ $warna }}"><i class="fas {{ $ikon }}"></i></div>
                <div><div class="kdo-stat-val" id="{{ $id }}">—</div><div class="kdo-stat-label">{{ $label }}</div></div>
            </div>
        </div>
    @endforeach
</div>

<div class="mb-2" id="catatanLayak"></div>
<div class="mb-3" id="catatanMode"></div>

<div class="kdo-card kdo-filter-card" id="filterBar">
    <div class="kdo-filter-bar">
        <label class="kdo-field mb-0"><i class="fas fa-search kdo-field-icon"></i><input type="search" data-param="search" placeholder="Cari nopol / merek / pegawai"></label>
        <div class="kdo-sep"></div>
        <div class="kdo-field"><i class="fas fa-filter kdo-field-icon"></i>
            <select data-param="kriteria">
                <option value="">Semua kriteria</option>
                @foreach (\App\Enums\KriteriaService::opsi() as $nilai => $label)
                    <option value="{{ $nilai }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="kdo-field"><i class="fas fa-building kdo-field-icon"></i>
            <select data-param="kdo_perangkat_daerah_id" id="filterPerangkat"><option value="">Semua perangkat daerah</option></select>
        </div>
        <div class="kdo-field"><i class="fas fa-envelope kdo-field-icon"></i>
            <select data-param="status_notifikasi">
                <option value="">Semua status email</option>
                <option value="belum">Belum dinotifikasi</option>
                <option value="sudah">Sudah dinotifikasi</option>
            </select>
        </div>
        <button type="button" class="kdo-btn kdo-btn-reset" data-aksi="reset"><i class="fas fa-undo"></i>Reset</button>
    </div>
</div>

<div class="kdo-card kdo-table-card">
    <table class="table kdo-table" id="tabelMonitoring">
        <thead><tr>
            <th>Kendaraan</th><th>Tahun</th><th>Kondisi</th><th>Perangkat Daerah</th><th>Penanggung Jawab</th>
            <th>Alasan Service</th><th>Jadwal Service</th><th>Email Terakhir</th><th class="text-right">Aksi</th>
        </tr></thead>
    </table>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var ringkasan = null;

    function muatRingkasan() {
        $.getJSON('/api/v1/monitoring/ringkasan').done(function (res) {
            var d = ringkasan = res.data;
            $('#statTotal').text(KDO.angka(d.total_kendaraan));
            $('#statLayak').text(KDO.angka(d.layak_service));
            $('#statUmur').text(KDO.angka(d.umur));
            $('#statRingan').text(KDO.angka(d.rusak_ringan));
            $('#statBerat').text(KDO.angka(d.rusak_berat));
            $('#catatanLayak').html(d.layak_service > 0
                ? '<div class="kdo-note"><i class="fas fa-info-circle"></i><div>Terdapat <b>' + KDO.angka(d.layak_service) + ' kendaraan</b> layak service, <b>'
                    + KDO.angka(d.belum_dinotifikasi) + '</b> di antaranya belum dinotifikasi ke penanggung jawab. '
                    + '<a class="kdo-note-link" id="lihatRekap" tabindex="0">Lihat Selengkapnya →</a>'
                    + '<span class="kdo-note-small">Kriteria: tahun produksi &gt; ' + d.batas_umur_service + ' tahun, kondisi rusak ringan, atau rusak berat.</span></div></div>'
                : '<div class="kdo-note aman"><i class="fas fa-check-circle"></i><div>Tidak ada kendaraan yang memenuhi kriteria layak service.</div></div>');
            
        });
    }

    $(document).on('click keydown', '#lihatRekap', function (e) {
        if (e.type === 'keydown' && e.key !== 'Enter') { return; }
        var baris = ringkasan.per_perangkat_daerah;
        var jumlah = function (k) { return baris.reduce(function (t, b) { return t + b[k]; }, 0); };
        var kolomAngka = ['total', 'umur', 'rusak_ringan', 'rusak_berat', 'layak_service'];
        KDO.popup({
            judul: 'Rekap Kendaraan Layak Service',
            subjudul: 'Per perangkat daerah · ' + baris.length + ' perangkat · ' + KDO.angka(ringkasan.layak_service) + ' layak service',
            ikon: 'fa-chart-bar',
            kolom: [
                { label: 'Perangkat Daerah', key: 'kdo_nama_perangkat' },
                { label: 'Total', kanan: true, html: function (b) { return KDO.angka(b.total); } },
                { label: 'Umur > ' + ringkasan.batas_umur_service + ' th', kanan: true, html: function (b) { return KDO.angka(b.umur); } },
                { label: 'Rusak Ringan', kanan: true, html: function (b) { return KDO.angka(b.rusak_ringan); } },
                { label: 'Rusak Berat', kanan: true, html: function (b) { return KDO.angka(b.rusak_berat); } },
                { label: 'Layak Service', kanan: true, html: function (b) { return '<b>' + KDO.angka(b.layak_service) + '</b>'; } }
            ],
            baris: baris,
            tfoot: '<tfoot><tr><td>Total</td>' + kolomAngka.map(function (k) { return '<td class="text-right">' + KDO.angka(jumlah(k)) + '</td>'; }).join('') + '</tr></tfoot>',
            kaki: 'Satu kendaraan dapat memenuhi lebih dari satu kriteria.'
        });
    });

    var ambilFilter = KDO.filter($('#filterBar'), function () { tabel.ajax.reload(); });
    KDO.isiSelect(document.getElementById('filterPerangkat'), '/api/v1/perangkat-daerah', function (r) { return r.kdo_nama_perangkat; }, 'Semua perangkat daerah');

    var tabel = KDO.tabel('#tabelMonitoring', {
        url: '/api/v1/monitoring/kendaraan',
        params: ambilFilter,
        order: [[1, 'asc']],
        columns: [
            { data: 'kdo_nomor_polisi', name: 'kdo_nomor_polisi', render: function (v, t, r) { return '<span class="kdo-kode">' + KDO.esc(v) + '</span>' + KDO.chip(r.kdo_merek + ' ' + r.kdo_tipe); } },
            { data: 'kdo_tahun_produksi', name: 'kdo_tahun_produksi', render: function (v, t, r) { return KDO.esc(v) + '<span class="kdo-sub">' + r.kdo_umur + ' tahun</span>'; } },
            { data: 'kdo_kondisi', name: 'kdo_kondisi', render: function (v, t, r) { return KDO.chipKode(v, r.kdo_kondisi_label); } },
            { data: 'pegawai', orderable: false, render: function (v) { return v && v.perangkat_daerah ? KDO.esc(v.perangkat_daerah.kdo_nama_perangkat) : KDO.kosong; } },
            { data: 'pegawai', orderable: false, render: function (v) {
                if (!v) { return KDO.kosong; }
                return KDO.orang(v.kdo_nama_pegawai, 'NIP ' + v.kdo_nip) + (v.kdo_email ? '' : '<span class="kdo-status-line"><span class="kritis">Belum ada email</span></span>');
            } },
            { data: 'kdo_alasan_service', orderable: false, render: function (v) { return v.map(function (a) { return KDO.chipKode(a.kode, a.label); }).join('<br>'); } },
            { data: 'jadwal_terakhir', orderable: false, render: function (v) {
                if (!v) { return KDO.kosong; }
                return KDO.tanggal(v.kdo_service_mulai) + (v.kdo_service_selesai ? ' – ' + KDO.tanggal(v.kdo_service_selesai) : '') + '<br>' + KDO.chipKode(v.kdo_status_jadwal, v.kdo_status_jadwal_label);
            } },
            { data: 'notifikasi_terakhir_pada', name: 'notifikasi_terakhir_pada', render: function (v) { return KDO.tanggalJam(v); } },
            { data: null, orderable: false, className: 'text-right', render: function (v, t, r) {
                var bisaKirim = r.pegawai && r.pegawai.kdo_email;
                return '<div class="kdo-aksi">'
                    + '<button type="button" class="kdo-btn-lihat" data-aksi="riwayat"><i class="fas fa-file-alt"></i>Lihat</button>'
                    + '<a class="kdo-btn-aksi" href="/penjadwalan-service?kendaraan=' + r.id + '" title="Jadwalkan service"><i class="fas fa-calendar-plus"></i></a>'
                    + '<button type="button" class="kdo-btn-aksi kirim" data-aksi="kirim"' + (bisaKirim ? '' : ' disabled title="Penanggung jawab/email belum ada"') + '><i class="fas fa-paper-plane"></i>Kirim Email</button></div>';
            } }
        ]
    });

    function baris(el) { return tabel.row($(el).closest('tr')).data(); }

    $('#tabelMonitoring').on('click', '[data-aksi=riwayat]', function () {
        var r = baris(this);
        $.getJSON('/api/v1/kendaraan/' + r.id + '/notifikasi-service').done(function (res) {
            KDO.popup({
                judul: 'Riwayat Email · ' + r.kdo_nomor_polisi,
                subjudul: r.pegawai.kdo_nama_pegawai + ' · ' + res.data.length + ' email',
                ikon: 'fa-envelope-open-text',
                kolom: [
                    { label: 'Dikirim', html: function (b) { return KDO.tanggalJam(b.kdo_created_at); } },
                    { label: 'Mode', html: function (b) { return KDO.chipKode(b.kdo_mode, b.kdo_mode_label); } },
                    { label: 'Tujuan', key: 'kdo_email_tujuan' },
                    { label: 'Alasan', key: 'kdo_alasan' },
                    { label: 'Oleh', html: function (b) { return b.pengirim ? KDO.esc(b.pengirim.kdo_nama_user) : KDO.kosong; } }
                ],
                baris: res.data,
                kaki: 'Menampilkan maksimal 100 email terakhir.'
            });
        }).fail(KDO.gagal);
    });

    $('#tabelMonitoring').on('click', '[data-aksi=kirim]', function () {
        var r = baris(this);
        var dev = ringkasan && ringkasan.mode_email.kode === 'dev';
        var isi = 'Kepada : ' + r.pegawai.kdo_nama_pegawai + ' <' + r.pegawai.kdo_email + '>\n'
            + (dev ? 'Dialihkan: ' + ringkasan.mode_email.penerima_dev + ' (mode development)\n' : '')
            + '\nKendaraan dinas ' + r.kdo_nomor_polisi + ' (' + r.kdo_merek + ' ' + r.kdo_tipe + ', ' + r.kdo_tahun_produksi + ') layak service:\n'
            + r.kdo_alasan_service.map(function (a) { return '  - ' + a.label; }).join('\n');
        KDO.teksPanjang({
            judul: 'Kirim email notifikasi?',
            isi: isi,
            meta: 'Mode: ' + (dev ? 'Development' : 'Production') + ' · ' + KDO.tanggalKalimat(new Date()) + ' ' + moment().format('HH:mm'),
            opsi: { showCancelButton: true, confirmButtonText: 'Kirim Email', reverseButtons: true }
        }).then(function (hasil) {
            if (!hasil.isConfirmed) { return; }
            KDO.api('POST', '/api/v1/kendaraan/' + r.id + '/notifikasi-service').done(function (res) {
                KDO.swalModern('info', { icon: 'success', title: 'Email terkirim', text: res.message });
                tabel.ajax.reload(null, false);
                muatRingkasan();
            }).fail(KDO.gagal);
        });
    });

    muatRingkasan();
})();
</script>
@endpush
