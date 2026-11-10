@extends('layouts.app')
@section('judul', 'Penjadwalan Service')
@section('subjudul', 'Jadwal service kendaraan dinas')

@section('konten')
<div class="kdo-card kdo-filter-card" id="filterBar">
    <div class="kdo-filter-bar">
        <label class="kdo-field mb-0"><i class="fas fa-search kdo-field-icon"></i><input type="search" data-param="search" placeholder="Cari nomor polisi"></label>
        <div class="kdo-sep"></div>
        <div class="kdo-field"><i class="fas fa-tasks kdo-field-icon"></i>
            <select data-param="kdo_status_jadwal"><option value="">Semua status</option>
                @foreach (\App\Enums\StatusJadwal::opsi() as $nilai => $label)<option value="{{ $nilai }}">{{ $label }}</option>@endforeach
            </select>
        </div>
        <div class="kdo-field"><i class="fas fa-building kdo-field-icon"></i>
            <select data-param="kdo_perangkat_daerah_id" id="filterPerangkat"><option value="">Semua perangkat daerah</option></select>
        </div>
        <label class="kdo-field mb-0"><i class="fas fa-calendar-alt kdo-field-icon"></i><input type="text" readonly data-param="tanggal" data-rentang="1" id="filterTanggal" placeholder="Semua tanggal"><i class="fas fa-chevron-down kdo-field-chevron"></i></label>
        <button type="button" class="kdo-btn kdo-btn-reset" data-aksi="reset"><i class="fas fa-undo"></i>Reset</button>
        <button type="button" class="kdo-btn kdo-btn-primary" data-aksi="tambah"><i class="fas fa-plus"></i>Tambah Jadwal</button>
    </div>
</div>
<div class="kdo-card kdo-table-card">
    <table class="table kdo-table" id="tabelJadwal">
        <thead><tr><th>Kendaraan</th><th>Perangkat Daerah</th><th>Penanggung Jawab</th><th>Mulai</th><th>Selesai</th><th>Status</th><th>Keterangan</th><th class="text-right">Aksi</th></tr></thead>
    </table>
</div>
@endsection

@push('modal')
<div class="modal fade" id="modalJadwal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><form class="modal-content" id="formJadwal" novalidate>
        <div class="modal-header"><h5 class="modal-title">Jadwal Service</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button></div>
        <div class="modal-body">
            <div class="form-group"><label>Kendaraan</label><select class="form-control" name="kdo_kendaraan_id" id="formKendaraan"></select></div>
            <div class="form-row">
                <div class="form-group col-md-6"><label>Tanggal Mulai</label><input type="date" class="form-control" name="kdo_service_mulai"></div>
                <div class="form-group col-md-6"><label>Tanggal Selesai</label><input type="date" class="form-control" name="kdo_service_selesai"></div>
            </div>
            <div class="form-group mb-0"><label>Keterangan</label><textarea class="form-control" name="kdo_keterangan" rows="3" placeholder="Servis berkala, perbaikan rem, dsb."></textarea></div>
        </div>
        <div class="modal-footer"><button type="button" class="kdo-btn" data-dismiss="modal">Batal</button><button type="submit" class="kdo-btn kdo-btn-primary"><i class="fas fa-save"></i>Simpan</button></div>
    </form></div>
</div>
@endpush

@push('scripts')
<script>
(function () {
    KDO.isiSelect(document.getElementById('filterPerangkat'), '/api/v1/perangkat-daerah', function (r) { return r.kdo_nama_perangkat; }, 'Semua perangkat daerah');
    var muatKendaraan = KDO.isiSelect(document.getElementById('formKendaraan'), '/api/v1/kendaraan', function (r) {
        return r.kdo_nomor_polisi + ' — ' + r.kdo_merek + ' ' + r.kdo_tipe + (r.kdo_alasan_service.length ? ' (layak service)' : '');
    }, 'Pilih kendaraan');

    var crud = KDO.crud({
        url: '/api/v1/penjadwalan-service', tabel: '#tabelJadwal', filterBar: '#filterBar', modal: '#modalJadwal', form: '#formJadwal',
        judulTambah: 'Tambah Jadwal Service', judulEdit: 'Ubah Jadwal Service', order: [[3, 'desc']],
        namaBaris: function (r) { return 'jadwal ' + r.kendaraan.kdo_nomor_polisi + ' (' + KDO.tanggalKalimat(r.kdo_service_mulai) + ')'; },
        columns: [
            { data: 'kendaraan', orderable: false, render: function (v) { return '<span class="kdo-kode">' + KDO.esc(v.kdo_nomor_polisi) + '</span>' + KDO.chip(v.kdo_merek + ' ' + v.kdo_tipe); } },
            { data: 'kendaraan.pegawai', orderable: false, render: function (v) { return v && v.perangkat_daerah ? KDO.esc(v.perangkat_daerah.kdo_nama_perangkat) : KDO.kosong; } },
            { data: 'kendaraan.pegawai', orderable: false, render: function (v) { return v ? KDO.orang(v.kdo_nama_pegawai, 'NIP ' + v.kdo_nip) : KDO.kosong; } },
            { data: 'kdo_service_mulai', name: 'kdo_service_mulai', render: KDO.tanggal },
            { data: 'kdo_service_selesai', name: 'kdo_service_selesai', render: KDO.tanggal },
            { data: 'kdo_status_jadwal', orderable: false, render: function (v, t, r) { return KDO.chipKode(v, r.kdo_status_jadwal_label); } },
            { data: 'kdo_keterangan', orderable: false, render: function (v) {
                if (!v) { return KDO.kosong; }
                var pendek = v.length > 40 ? v.slice(0, 40) + '…' : v;
                return '<a class="kdo-note-link" data-aksi="keterangan" tabindex="0">' + KDO.esc(pendek) + '</a>';
            } },
            { data: null, orderable: false, className: 'text-right', render: function () { return KDO.tombolAksi(false); } }
        ]
    });

    KDO.rentangTanggal('#filterTanggal', function () { crud.tabel.ajax.reload(); });

    $('#tabelJadwal').on('click', '[data-aksi=keterangan]', function () {
        var r = crud.tabel.row($(this).closest('tr')).data();
        KDO.teksPanjang({
            judul: 'Keterangan Service · ' + r.kendaraan.kdo_nomor_polisi,
            isi: r.kdo_keterangan,
            meta: 'Jadwal ' + KDO.tanggalKalimat(r.kdo_service_mulai) + (r.kdo_service_selesai ? ' – ' + KDO.tanggalKalimat(r.kdo_service_selesai) : '') + ' · diperbarui ' + moment(r.kdo_updated_at).format('DD/MM/YYYY HH:mm')
        });
    });

    var kendaraanDipilih = new URLSearchParams(window.location.search).get('kendaraan');
    if (kendaraanDipilih) {
        muatKendaraan.done(function () {
            crud.bukaForm(null);
            $('#formKendaraan').val(kendaraanDipilih);
        });
    }
})();
</script>
@endpush
