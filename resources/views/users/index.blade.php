@extends('layouts.app')
@section('judul', 'Master User')
@section('subjudul', 'Kelola akun pengguna aplikasi (khusus admin)')

@section('konten')
<div class="kdo-card kdo-filter-card" id="filterBar">
    <div class="kdo-filter-bar">
        <label class="kdo-field mb-0"><i class="fas fa-search kdo-field-icon"></i><input type="search" data-param="search" placeholder="Cari nama / email"></label>
        <div class="kdo-sep"></div>
        <div class="kdo-field"><i class="fas fa-user-tag kdo-field-icon"></i>
            <select data-param="kdo_role"><option value="">Semua role</option>
                @foreach (\App\Enums\RoleUser::opsi() as $nilai => $label)<option value="{{ $nilai }}">{{ $label }}</option>@endforeach
            </select>
        </div>
        <button type="button" class="kdo-btn kdo-btn-reset" data-aksi="reset"><i class="fas fa-undo"></i>Reset</button>
        <button type="button" class="kdo-btn kdo-btn-primary" data-aksi="tambah"><i class="fas fa-user-plus"></i>Tambah User</button>
    </div>
</div>
<div class="kdo-card kdo-table-card">
    <table class="table kdo-table" id="tabelUser">
        <thead><tr><th>User</th><th>Role</th><th>Dibuat</th><th class="text-right">Aksi</th></tr></thead>
    </table>
</div>
@endsection

@push('modal')
<div class="modal fade" id="modalUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog"><form class="modal-content" id="formUser" novalidate>
        <div class="modal-header"><h5 class="modal-title">User</h5><button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span>&times;</span></button></div>
        <div class="modal-body">
            <div class="form-group"><label>Nama</label><input class="form-control" name="kdo_nama_user"></div>
            <div class="form-group"><label>Email</label><input type="email" class="form-control" name="kdo_email" autocomplete="off"></div>
            <div class="form-group"><label>Role</label><select class="form-control" name="kdo_role">
                @foreach (\App\Enums\RoleUser::opsi() as $nilai => $label)<option value="{{ $nilai }}">{{ $label }}</option>@endforeach
            </select></div>
            <div class="form-row">
                <div class="form-group col-md-6 mb-0"><label>Password <small class="text-muted" id="petunjukPassword"></small></label><input type="password" class="form-control" name="kdo_password" autocomplete="new-password"></div>
                <div class="form-group col-md-6 mb-0"><label>Konfirmasi Password</label><input type="password" class="form-control" name="kdo_password_confirmation" autocomplete="new-password"></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="kdo-btn" data-dismiss="modal">Batal</button><button type="submit" class="kdo-btn kdo-btn-primary"><i class="fas fa-save"></i>Simpan</button></div>
    </form></div>
</div>
@endpush

@push('scripts')
<script>
KDO.crud({
    url: '/api/v1/users', tabel: '#tabelUser', filterBar: '#filterBar', modal: '#modalUser', form: '#formUser',
    judulTambah: 'Tambah User', judulEdit: 'Ubah User', order: [[0, 'asc']],
    namaBaris: function (r) { return r.kdo_nama_user; },
    isiForm: function (baris) { $('#petunjukPassword').text(baris ? '(kosongkan jika tidak diubah)' : '(min. 8 karakter)'); },
    columns: [
        { data: 'kdo_nama_user', name: 'kdo_nama_user', render: function (v, t, r) { return KDO.orang(v, r.kdo_email) + (r.id === window.KDO_USER_ID ? ' <span class="kdo-pill">Anda</span>' : ''); } },
        { data: 'kdo_role', name: 'kdo_role', render: function (v, t, r) { return KDO.chipKode(v, r.kdo_role_label); } },
        { data: 'kdo_created_at', name: 'kdo_created_at', render: KDO.tanggalJam },
        { data: null, orderable: false, className: 'text-right', render: function (v, t, r) {
            var html = KDO.tombolAksi(false);
            return r.id === window.KDO_USER_ID ? html.replace(/<button[^>]*data-aksi="hapus"[\s\S]*?<\/button>/, '') : html;
        } }
    ]
});
</script>
@endpush
