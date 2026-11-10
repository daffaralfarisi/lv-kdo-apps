# Dokumentasi KDO Apps

Aplikasi pengelolaan **Kendaraan Dinas Operasional (KDO)**: Laravel 13, frontend + backend dalam satu project, backend berupa REST API (`/api/v1`).

| Dokumen | Isi |
| --- | --- |
| [instalasi.md](instalasi.md) | Cara setup, menjalankan, akun bawaan, konfigurasi email |
| [keputusan-teknis.md](keputusan-teknis.md) | Catatan setiap keputusan teknis beserta alasannya |
| [skema-database.md](skema-database.md) | Tabel `kdo_*`, field, relasi, ERD |
| [api.md](api.md) | Referensi endpoint API v1 |
| [testing-api-dan-email.md](testing-api-dan-email.md) | Postman collection & testing email dengan Mailpit |
| [log-pengembangan.md](log-pengembangan.md) | Urutan proses development yang sudah dilakukan |

## Fitur

1. **Master Kendaraan Dinas**: CRUD, filter perangkat daerah/kondisi/status, detail + riwayat jadwal service. Perangkat daerah kendaraan mengikuti pegawai penanggung jawab.
2. **Master Pegawai** (PNS penanggung jawab): CRUD, perangkat daerah tempat bertugas, filter perangkat daerah & golongan, daftar kendaraan tanggungan.
3. **Master Perangkat Daerah**: CRUD (Dinas Pendidikan, Dinas Kesehatan, Dinas PUPR).
4. **Master User** (khusus admin): CRUD akun Admin & User.
5. **Penjadwalan Service**: CRUD jadwal, status otomatis (dijadwalkan/berlangsung/selesai), filter rentang tanggal.
6. **Dashboard Monitoring**: kendaraan layak service berdasarkan
   a. tahun produksi > 5 tahun, b. kondisi rusak ringan, c. kondisi rusak berat,
   lalu **kirim email** ke pegawai penanggung jawab (mode dev/prod).

## Hak akses

| Role | Akses |
| --- | --- |
| `admin` | Semua menu + Master User (tambah/ubah/hapus user) |
| `user` | Semua menu operasional (dashboard, master data, penjadwalan); tidak dapat mengelola user |
