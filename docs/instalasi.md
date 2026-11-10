# Instalasi & Menjalankan

## Prasyarat
- PHP 8.3+ (ekstensi `pdo_mysql`, `mbstring`, `intl`), Composer 2
- MariaDB/MySQL dengan database `kdo-apps`
- Akses internet di browser (library frontend dimuat dari CDN jsDelivr)
- Node.js **tidak diperlukan** (tidak ada build step frontend)

## Langkah
```bash
composer install
cp .env.example .env          # lalu isi DB_PASSWORD
php artisan key:generate
php artisan migrate:fresh --seed   # buat tabel kdo_* + data dummy
php artisan serve                   # http://127.0.0.1:8000
```
Jika memakai virtual host (`APP_URL=http://kdo-apps.test`), cukup arahkan document root ke `public/`.

## Akun bawaan (seeder)
| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@kdo-apps.test` | `password` |
| User | `user@kdo-apps.test` | `password` |

> Ganti password setelah deploy ke server produksi.

## Konfigurasi email notifikasi (dev / prod)
Diatur lewat file config (`config/kdo.php`) yang membaca `.env`, **tanpa tabel database**:

```dotenv
KDO_EMAIL_MODE=dev                          # dev | prod
KDO_EMAIL_DEV_PENERIMA=flixads999@gmail.com
```

Logika pemilihan tujuan (if/else) ada di `App\Enums\ModeEmail::alamatTujuan()`:
- `dev`: semua email dialihkan ke `KDO_EMAIL_DEV_PENERIMA`, subjek diberi awalan `[DEV]`.
- `prod`: email dikirim ke `kdo_email` pegawai penanggung jawab.

Setelah mengubah `.env`, jalankan `php artisan config:clear`. Mode aktif ditampilkan di Dashboard.

**Testing email**: `.env` bawaan memakai **Mailpit** (`MAIL_MAILER=smtp`, `127.0.0.1:1025`). Semua email ditangkap dan bisa dilihat di http://localhost:8025, tanpa ada yang benar-benar terkirim. Panduan lengkap ada di [testing-api-dan-email.md](testing-api-dan-email.md).

**Pengiriman sungguhan** (termasuk ke flixads999@gmail.com): ganti ke SMTP asli, misalnya Gmail:
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=akun@gmail.com
MAIL_PASSWORD=app-password-gmail
MAIL_SCHEME=null
MAIL_FROM_ADDRESS="akun@gmail.com"
```

## Konfigurasi lain
| Variabel | Default | Fungsi |
| --- | --- | --- |
| `KDO_BATAS_UMUR_SERVICE` | `5` | Umur kendaraan (tahun) yang dilampaui agar dianggap layak service |
| `APP_TIMEZONE` | `Asia/Jakarta` | Dasar perhitungan "hari ini" untuk status jadwal & umur |
| `APP_LOCALE` | `id` | Pesan validasi Bahasa Indonesia (`lang/id`) |

## Menjalankan test
```bash
mysql -uroot -p -e 'CREATE DATABASE IF NOT EXISTS `kdo-apps_testing`'
php artisan test --compact
```
Test memakai database MariaDB terpisah `kdo-apps_testing` (lihat `phpunit.xml`) karena ekstensi `pdo_sqlite` tidak terpasang.

## Catatan izin file log
`storage/logs/laravel.log` sempat dimiliki `www-data` (mode 644) sehingga artisan/CLI gagal menulis log. File lama disimpan sebagai `storage/logs/laravel-backup-www-data.log` dan file baru dibuat dengan mode 666 (khusus lingkungan dev). Di server, samakan user PHP-FPM dan CLI atau gunakan grup bersama.
