@extends('layouts.app')
@section('judul', 'Pegawai')
@section('subjudul', 'Master data pegawai negeri sipil penanggung jawab kendaraan')

@section('konten')
<div class="kdo-card kdo-filter-card" id="filterBar">
    <div class="kdo-filter-bar">
        <label class="kdo-field mb-0"><i class="fas fa-search kdo-field-icon"></i><input type="search" data-param="search" placeholder="Cari nama / NIP / jabatan"></label>
        <div class="kdo-sep"></div>
        <div class="kdo-field"><i class="fas fa-building kdo-field-icon"></i>
            <select data-param="kdo_perangkat_daerah_id" id="filterPerangkat"><option value="">Semua perangkat daerah</option></select>
        </div>
        <div class="kdo-field"><i class="fas fa-layer-group kdo-field-icon"></i>
            <select data-param="kdo_golongan"><option value="">Semua golongan</option>
                @foreach (config('kdo.golongan') as $golongan)<option value="{{ $golongan }}">{{ $golongan }}</option>@endforeach
            </select>
        </div>
        <button type="button" class="kdo-btn kdo-btn-reset" data-aksi="reset"><i class="fas fa-undo"></i>Reset</button>
        <button type="button" class="kdo-btn kdo-btn-primary" data-aksi="tambah"><i class="fas fa-plus"></i>Tambah Pegawai</button>
    </div>
</div>
<div class="kdo-card kdo-table-card">
    <table class="table kdo-table" id="tabelPegawai">
        <thead><tr><th>Pegawai</th><th>Perangkat Daerah</th><th>Jabatan</th><th>Golongan</th><th>Email</th><th>Kendaraan</th><th class="text-right">Aksi</th></tr></thead>
    </table>
</div>
@endsection

@push('modal')
<div class="modal fade" id="modalPegawai" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><form class="modal-content" id="formPegawai" novalidate>
        <div class="modal-header"><h5 class="modal-title">Pegawai</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button></div>
        <div class="modal-body">
            <div class="form-group"><label>Perangkat Daerah</label><select class="form-control" name="kdo_perangkat_daerah_id" id="formPerangkat"></select></div>
            <div class="form-group"><label>Nama Pegawai</label><input class="form-control" name="kdo_nama_pegawai"></div>
            <div class="form-group"><label>NIP (18 digit)</label><input class="form-control" name="kdo_nip" inputmode="numeric" maxlength="18"></div>
            <div class="form-group"><label>Jabatan</label><input class="form-control" name="kdo_jabatan"></div>
            <div class="form-row">
                <div class="form-group col-md-4"><label>Golongan</label><select class="form-control" name="kdo_golongan">
                    @foreach (config('kdo.golongan') as $golongan)<option value="{{ $golongan }}">{{ $golongan }}</option>@endforeach
                </select></div>
                <div class="form-group col-md-8"><label>Email <small class="text-muted">(tujuan notifikasi)</small></label><input type="email" class="form-control" name="kdo_email"></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="kdo-btn" data-dismiss="modal">Batal</button><button type="submit" class="kdo-btn kdo-btn-primary"><i class="fas fa-save"></i>Simpan</button></div>
    </form></div>
</div>
@endpush

@push('scripts')
<script>
(function () {
var labelPerangkat = function (r) { return r.kdo_nama_perangkat; };
KDO.isiSelect(document.getElementById('filterPerangkat'), '/api/v1/perangkat-daerah', labelPerangkat, 'Semua perangkat daerah');
KDO.isiSelect(document.getElementById('formPerangkat'), '/api/v1/perangkat-daerah', labelPerangkat, 'Pilih perangkat daerah');
KDO.crud({
    url: '/api/v1/pegawai', tabel: '#tabelPegawai', filterBar: '#filterBar', modal: '#modalPegawai', form: '#formPegawai',
    judulTambah: 'Tambah Pegawai', judulEdit: 'Ubah Pegawai', order: [[0, 'asc']],
    namaBaris: function (r) { return r.kdo_nama_pegawai; },
    columns: [
        { data: 'kdo_nama_pegawai', name: 'kdo_nama_pegawai', render: function (v, t, r) { return KDO.orang(v, 'NIP ' + r.kdo_nip); } },
        { data: 'perangkat_daerah', orderable: false, render: function (v) { return v ? KDO.esc(v.kdo_nama_perangkat) : KDO.kosong; } },
        { data: 'kdo_jabatan', name: 'kdo_jabatan', render: KDO.atauKosong },
        { data: 'kdo_golongan', name: 'kdo_golongan', render: function (v) { return KDO.chip(v, 'ungu'); } },
        { data: 'kdo_email', orderable: false, render: KDO.atauKosong },
        { data: 'kendaraan_count', name: 'kendaraan_count', render: function (v) { return '<span class="kdo-pill">' + KDO.angka(v) + ' unit</span>'; } },
        { data: null, orderable: false, className: 'text-right', render: function () { return KDO.tombolAksi(true); } }
    ],
    lihat: function (r) {
        $.getJSON('/api/v1/pegawai/' + r.id).done(function (res) {
            var p = res.data;
            KDO.popup({
                judul: p.kdo_nama_pegawai,
                subjudul: 'NIP ' + p.kdo_nip + ' · ' + (p.perangkat_daerah ? p.perangkat_daerah.kdo_nama_perangkat : '') + ' · ' + p.kendaraan_count + ' kendaraan',
                ikon: 'fa-id-badge',
                kolom: [
                    { label: 'Nomor Polisi', html: function (b) { return '<span class="kdo-kode">' + KDO.esc(b.kdo_nomor_polisi) + '</span>'; } },
                    { label: 'Merek / Tipe', html: function (b) { return KDO.esc(b.kdo_merek + ' ' + b.kdo_tipe); } },
                    { label: 'Tahun', key: 'kdo_tahun_produksi' },
                    { label: 'Kondisi', html: function (b) { return KDO.chipKode(b.kdo_kondisi, b.kdo_kondisi_label); } },
                    { label: 'Status', html: function (b) { return KDO.chipKode(b.kdo_status, b.kdo_status_label); } }
                ],
                baris: p.kendaraan,
                kaki: 'Kendaraan yang menjadi tanggung jawab pegawai.'
            });
        }).fail(KDO.gagal);
    }
});
})();
</script>
@endpush
