# Log Proses Pengembangan

Tanggal: 10 Nov 2026 · Waktu kerja: ±1 jam

| # | Tahap | Hasil |
| --- | --- | --- |
| 1 | Analisis kebutuhan & lingkungan | Laravel 13.40, PHP 8.3, MariaDB 10.6, Node 18; database `kdo-apps` sudah ada; tidak ada `pdo_sqlite` |
| 2 | Tooling pengembangan | Paket dev `laravel/boost` (pedoman konvensi & best practice Laravel) |
| 3 | API scaffolding | `php artisan install:api` (Sanctum 4.3), `statefulApi()`, `currentRequestHost()` |
| 4 | Konfigurasi | `.env`: APP_NAME, locale `id`, faker `id_ID`, timezone WIB, `KDO_*`; `config/kdo.php` |
| 5 | Enum domain | `app/Enums/*` + trait `MemilikiOpsi` |
| 6 | Migration | `kdo_user` (mengganti users), `kdo_perangkat_daerah`, `kdo_pegawai`, `kdo_kendaraan`, `kdo_penjadwalan_service`, `kdo_notifikasi_service` |
| 7 | Revisi user: konfigurasi email | Tabel `kdo_konfigurasi_email` + modelnya **dihapus**; diganti if/else berbasis config (`ModeEmail`) |
| 8 | Perbaikan saat migrasi | Nama index komposit > 64 karakter → diberi nama pendek; izin `laravel.log` diperbaiki |
| 9 | Model, factory, seeder | `migrate:fresh --seed` sukses: 24 kendaraan, 15 pegawai, 6 jadwal, 2 user |
| 10 | API | Resource, FormRequest, 8 controller, Mailable markdown, route v1, pesan error Indonesia, Gate admin, throttle login |
| 11 | Smoke test API (curl) | Login token, ringkasan, monitoring, kirim email (dev → flixads999), validasi 422, 404, 401 |
| 12 | Frontend | Layout sidebar, login, dashboard, 5 halaman master/penjadwalan, UI kit CSS/JS sesuai referensi |
| 13 | Verifikasi frontend | Alur session login → halaman → API stateful; role user 403 di Master User; 11 URL CDN 200; 24 blok JS inline lolos `node --check` |
| 14 | Test otomatis | 24 test / 66 assertion lulus (auth, CRUD, validasi, restrict/cascade, role, kriteria monitoring, email dev/prod, escaping email) |
| 15 | Pint | Kode dirapikan dengan `vendor/bin/pint` |
| 16 | Dokumentasi | Folder `docs/` |
| 17 | Revisi user: warna sidebar | Latar biru `#006cb8`, menu aktif oranye `#ff9e00` (diambil dari gambar referensi), teks aktif navy agar terbaca (lihat K-15) |
| 18 | Revisi user: logo sidebar | Favicon BPAD di `public/` jadi logo sidebar dengan susunan logo → KDO Apps → Kendaraan Dinas Operasional (lihat K-16) |
| 19 | Revisi user: relasi | `kdo_perangkat_daerah_id` dipindah dari kendaraan ke pegawai, lalu `migrate:fresh --seed` (K-17) |
| 20 | Revisi user: halaman login | Kartu dua kolom sesuai gambar contoh, tanpa Cloudflare, isian modern (K-18) |
| 21 | Revisi user: sidebar | Teks menu aktif putih + efek collapse rail ikon / drawer mobile (K-19) |
| 22 | Revisi user: font | Nunito dari Google Fonts (K-20) |
| 23 | Verifikasi revisi | 6 halaman 200, API relasi baru via pegawai, 33 blok JS lolos `node --check`, 29 test / 83 assertion lulus |
| 24 | Postman collection | 9 folder / 45 request; dijalankan dengan Newman: 126 assertion lulus, data uji terhapus otomatis (K-21) |
| 25 | Mailpit | Binary v1.33.1 di `~/.local/bin`, `.env` → SMTP 127.0.0.1:1025; email notifikasi mode dev tertangkap lengkap di Mailpit (K-22) |

## Batasan karena waktu (rencana lanjutan)
- Belum diuji di browser sungguhan (tidak ada Chrome/Playwright di mesin). Verifikasi baru sebatas HTTP + sintaks JS.
- Belum ada kirim email massal, ubah password mandiri, dan export laporan.
- Library frontend masih dari CDN; untuk intranet tanpa internet, salin ke `public/vendor`.
- Status kendaraan (`dalam_service`) belum otomatis mengikuti jadwal service; saat ini diubah manual.
- Efek collapse sidebar & halaman login belum dicek di browser sungguhan (hanya HTTP + sintaks JS).
- Mailpit belum dipasang sebagai service permanen; jalankan manual atau aktifkan systemd user service (lihat testing-api-dan-email.md).
