@extends('layouts.app')
@section('judul', 'Perangkat Daerah')
@section('subjudul', 'Master data instansi pemilik/pengguna kendaraan dinas')

@section('konten')
<div class="kdo-card kdo-filter-card" id="filterBar">
    <div class="kdo-filter-bar">
        <label class="kdo-field mb-0"><i class="fas fa-search kdo-field-icon"></i><input type="search" data-param="search" placeholder="Cari perangkat daerah"></label>
        <div class="kdo-sep"></div>
        <div class="kdo-field"><i class="fas fa-toggle-on kdo-field-icon"></i>
            <select data-param="kdo_status_perangkat"><option value="">Semua status</option>
                @foreach (\App\Enums\StatusPerangkat::opsi() as $nilai => $label)<option value="{{ $nilai }}">{{ $label }}</option>@endforeach
            </select>
        </div>
        <button type="button" class="kdo-btn kdo-btn-reset" data-aksi="reset"><i class="fas fa-undo"></i>Reset</button>
        <button type="button" class="kdo-btn kdo-btn-primary" data-aksi="tambah"><i class="fas fa-plus"></i>Tambah Perangkat</button>
    </div>
</div>
<div class="kdo-card kdo-table-card">
    <table class="table kdo-table" id="tabelPerangkat">
        <thead><tr><th>Nama Perangkat Daerah</th><th>Status</th><th>Pegawai</th><th>Kendaraan</th><th class="text-right">Aksi</th></tr></thead>
    </table>
</div>
@endsection

@push('modal')
<div class="modal fade" id="modalPerangkat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><form class="modal-content" id="formPerangkat" novalidate>
        <div class="modal-header"><h5 class="modal-title">Perangkat Daerah</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button></div>
        <div class="modal-body">
            <div class="form-group"><label>Nama Perangkat Daerah</label><input class="form-control" name="kdo_nama_perangkat" placeholder="Dinas Pendidikan"></div>
            <div class="form-group mb-0"><label>Status</label><select class="form-control" name="kdo_status_perangkat">
                @foreach (\App\Enums\StatusPerangkat::opsi() as $nilai => $label)<option value="{{ $nilai }}">{{ $label }}</option>@endforeach
            </select></div>
        </div>
        <div class="modal-footer"><button type="button" class="kdo-btn" data-dismiss="modal">Batal</button><button type="submit" class="kdo-btn kdo-btn-primary"><i class="fas fa-save"></i>Simpan</button></div>
    </form></div>
</div>
@endpush

@push('scripts')
<script>
KDO.crud({
    url: '/api/v1/perangkat-daerah', tabel: '#tabelPerangkat', filterBar: '#filterBar', modal: '#modalPerangkat', form: '#formPerangkat',
    judulTambah: 'Tambah Perangkat Daerah', judulEdit: 'Ubah Perangkat Daerah', order: [[0, 'asc']],
    namaBaris: function (r) { return r.kdo_nama_perangkat; },
    columns: [
        { data: 'kdo_nama_perangkat', name: 'kdo_nama_perangkat', render: function (v) { return '<span class="kdo-kode">' + KDO.esc(v) + '</span>'; } },
        { data: 'kdo_status_perangkat', name: 'kdo_status_perangkat', render: function (v, t, r) { return KDO.chipKode(v, r.kdo_status_perangkat_label); } },
        { data: 'pegawai_count', name: 'pegawai_count', render: function (v) { return '<span class="kdo-pill">' + KDO.angka(v) + ' orang</span>'; } },
        { data: 'kendaraan_count', name: 'kendaraan_count', render: function (v) { return '<span class="kdo-pill">' + KDO.angka(v) + ' unit</span>'; } },
        { data: null, orderable: false, className: 'text-right', render: function () { return KDO.tombolAksi(true); } }
    ],
    lihat: function (r) {
        $.getJSON('/api/v1/perangkat-daerah/' + r.id).done(function (res) {
            var p = res.data;
            KDO.popup({
                judul: p.kdo_nama_perangkat,
                subjudul: p.kdo_status_perangkat_label + ' · ' + p.pegawai_count + ' pegawai · ' + p.kendaraan_count + ' kendaraan',
                ikon: 'fa-building',
                kolom: [
                    { label: 'Nomor Polisi', html: function (b) { return '<span class="kdo-kode">' + KDO.esc(b.kdo_nomor_polisi) + '</span>'; } },
                    { label: 'Merek / Tipe', html: function (b) { return KDO.esc(b.kdo_merek + ' ' + b.kdo_tipe); } },
                    { label: 'Tahun', key: 'kdo_tahun_produksi' },
                    { label: 'Kondisi', html: function (b) { return KDO.chipKode(b.kdo_kondisi, b.kdo_kondisi_label); } },
                    { label: 'Penanggung Jawab', html: function (b) { return b.pegawai ? KDO.esc(b.pegawai.kdo_nama_pegawai) : KDO.kosong; } }
                ],
                baris: p.kendaraan,
                kaki: 'Kendaraan milik perangkat daerah melalui pegawai penanggung jawabnya.'
            });
        }).fail(KDO.gagal);
    }
});
</script>
@endpush
