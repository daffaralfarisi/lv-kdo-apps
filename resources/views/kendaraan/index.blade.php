@extends('layouts.app')
@section('judul', 'Kendaraan Dinas')
@section('subjudul', 'Master data kendaraan dinas operasional')

@section('konten')
<div class="kdo-card kdo-filter-card" id="filterBar">
    <div class="kdo-filter-bar">
        <label class="kdo-field mb-0"><i class="fas fa-search kdo-field-icon"></i><input type="search" data-param="search" placeholder="Cari nopol / merek / tipe"></label>
        <div class="kdo-sep"></div>
        <div class="kdo-field"><i class="fas fa-building kdo-field-icon"></i>
            <select data-param="kdo_perangkat_daerah_id" id="filterPerangkat"><option value="">Semua perangkat daerah</option></select>
        </div>
        <div class="kdo-field"><i class="fas fa-heartbeat kdo-field-icon"></i>
            <select data-param="kdo_kondisi"><option value="">Semua kondisi</option>
                @foreach (\App\Enums\KondisiKendaraan::opsi() as $nilai => $label)<option value="{{ $nilai }}">{{ $label }}</option>@endforeach
            </select>
        </div>
        <div class="kdo-field"><i class="fas fa-toggle-on kdo-field-icon"></i>
            <select data-param="kdo_status"><option value="">Semua status</option>
                @foreach (\App\Enums\StatusKendaraan::opsi() as $nilai => $label)<option value="{{ $nilai }}">{{ $label }}</option>@endforeach
            </select>
        </div>
        <button type="button" class="kdo-btn kdo-btn-reset" data-aksi="reset"><i class="fas fa-undo"></i>Reset</button>
        <button type="button" class="kdo-btn kdo-btn-primary" data-aksi="tambah"><i class="fas fa-plus"></i>Tambah Kendaraan</button>
    </div>
</div>
<div class="kdo-card kdo-table-card">
    <table class="table kdo-table" id="tabelKendaraan">
        <thead><tr><th>Nomor Polisi</th><th>Tahun</th><th>Warna</th><th>Kondisi</th><th>Status</th><th>Perangkat Daerah</th><th>Penanggung Jawab</th><th class="text-right">Aksi</th></tr></thead>
    </table>
</div>
@endsection

@push('modal')
<div class="modal fade" id="modalKendaraan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"><form class="modal-content" id="formKendaraan" novalidate>
        <div class="modal-header"><h5 class="modal-title">Kendaraan</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button></div>
        <div class="modal-body"><div class="form-row">
            <div class="form-group col-md-4"><label>Nomor Polisi</label><input class="form-control" name="kdo_nomor_polisi" placeholder="B 1234 PQA"></div>
            <div class="form-group col-md-4"><label>Merek</label><input class="form-control" name="kdo_merek" placeholder="Toyota"></div>
            <div class="form-group col-md-4"><label>Tipe / Model</label><input class="form-control" name="kdo_tipe" placeholder="Innova 2.4 V"></div>
            <div class="form-group col-md-4"><label>Tahun Produksi</label><input type="number" class="form-control" name="kdo_tahun_produksi" min="1980" max="{{ now()->year }}"></div>
            <div class="form-group col-md-4"><label>Warna</label><input class="form-control" name="kdo_warna" placeholder="Hitam"></div>
            <div class="form-group col-md-4"><label>Kondisi</label><select class="form-control" name="kdo_kondisi">
                @foreach (\App\Enums\KondisiKendaraan::opsi() as $nilai => $label)<option value="{{ $nilai }}">{{ $label }}</option>@endforeach
            </select></div>
            <div class="form-group col-md-4"><label>Status</label><select class="form-control" name="kdo_status">
                @foreach (\App\Enums\StatusKendaraan::opsi() as $nilai => $label)<option value="{{ $nilai }}">{{ $label }}</option>@endforeach
            </select></div>
            <div class="form-group col-md-8 mb-0"><label>Penanggung Jawab</label><select class="form-control" name="kdo_pegawai_id" id="formPegawai"></select>
                <small class="form-text text-muted">Perangkat daerah kendaraan mengikuti perangkat daerah penanggung jawab.</small></div>
        </div></div>
        <div class="modal-footer"><button type="button" class="kdo-btn" data-dismiss="modal">Batal</button><button type="submit" class="kdo-btn kdo-btn-primary"><i class="fas fa-save"></i>Simpan</button></div>
    </form></div>
</div>
@endpush

@push('scripts')
<script>
(function () {
    KDO.isiSelect(document.getElementById('filterPerangkat'), '/api/v1/perangkat-daerah', function (r) { return r.kdo_nama_perangkat; }, 'Semua perangkat daerah');
    KDO.isiSelect(document.getElementById('formPegawai'), '/api/v1/pegawai', function (r) {
        return r.kdo_nama_pegawai + ' — ' + (r.perangkat_daerah ? r.perangkat_daerah.kdo_nama_perangkat : 'NIP ' + r.kdo_nip);
    }, 'Pilih penanggung jawab');

    KDO.crud({
        url: '/api/v1/kendaraan', tabel: '#tabelKendaraan', filterBar: '#filterBar', modal: '#modalKendaraan', form: '#formKendaraan',
        judulTambah: 'Tambah Kendaraan', judulEdit: 'Ubah Kendaraan', order: [[0, 'asc']],
        namaBaris: function (r) { return r.kdo_nomor_polisi; },
        catatanHapus: 'Jadwal service dan riwayat email kendaraan ini ikut terhapus.',
        columns: [
            { data: 'kdo_nomor_polisi', name: 'kdo_nomor_polisi', render: function (v, t, r) { return '<span class="kdo-kode">' + KDO.esc(v) + '</span>' + KDO.chip(r.kdo_merek + ' ' + r.kdo_tipe); } },
            { data: 'kdo_tahun_produksi', name: 'kdo_tahun_produksi', render: function (v, t, r) { return KDO.esc(v) + '<span class="kdo-sub">' + r.kdo_umur + ' tahun</span>'; } },
            { data: 'kdo_warna', name: 'kdo_warna', render: KDO.atauKosong },
            { data: 'kdo_kondisi', name: 'kdo_kondisi', render: function (v, t, r) { return KDO.chipKode(v, r.kdo_kondisi_label); } },
            { data: 'kdo_status', name: 'kdo_status', render: function (v, t, r) { return KDO.chipKode(v, r.kdo_status_label); } },
            { data: 'pegawai', orderable: false, render: function (v) { return v && v.perangkat_daerah ? KDO.esc(v.perangkat_daerah.kdo_nama_perangkat) : KDO.kosong; } },
            { data: 'pegawai', orderable: false, render: function (v) { return v ? KDO.orang(v.kdo_nama_pegawai, 'NIP ' + v.kdo_nip) : KDO.kosong; } },
            { data: null, orderable: false, className: 'text-right', render: function () { return KDO.tombolAksi(true); } }
        ],
        lihat: function (r) {
            $.getJSON('/api/v1/kendaraan/' + r.id).done(function (res) {
                var k = res.data;
                KDO.popup({
                    judul: k.kdo_nomor_polisi + ' · ' + k.kdo_merek + ' ' + k.kdo_tipe,
                    subjudul: k.kdo_tahun_produksi + ' · ' + k.kdo_warna + ' · ' + k.kdo_kondisi_label + ' · ' + k.pegawai.kdo_nama_pegawai + (k.pegawai.perangkat_daerah ? ' (' + k.pegawai.perangkat_daerah.kdo_nama_perangkat + ')' : ''),
                    ikon: 'fa-car',
                    htmlAtas: k.kdo_alasan_service.length
                        ? '<div class="px-3 pt-3"><div class="kdo-note"><i class="fas fa-info-circle"></i><div>Layak service: <b>' + k.kdo_alasan_service.map(function (a) { return KDO.esc(a.label); }).join(', ') + '</b></div></div></div>'
                        : '<div class="px-3 pt-3"><div class="kdo-note aman"><i class="fas fa-check-circle"></i><div>Kendaraan belum memenuhi kriteria layak service.</div></div></div>',
                    kolom: [
                        { label: 'Mulai', html: function (b) { return KDO.tanggal(b.kdo_service_mulai); } },
                        { label: 'Selesai', html: function (b) { return KDO.tanggal(b.kdo_service_selesai); } },
                        { label: 'Status', html: function (b) { return KDO.chipKode(b.kdo_status_jadwal, b.kdo_status_jadwal_label); } },
                        { label: 'Keterangan', key: 'kdo_keterangan' }
                    ],
                    baris: k.penjadwalan_service,
                    kaki: 'Riwayat jadwal service · ' + k.penjadwalan_service.length + ' jadwal'
                });
            }).fail(KDO.gagal);
        }
    });
})();
</script>
@endpush
