# KDO Apps — Monitoring Kendaraan Dinas Operasional

> Aplikasi dibangun menggunakan Laravel dan menggunakan AI Claude dengan Model Opus.

KDO Apps adalah aplikasi untuk mengelola **Kendaraan Dinas Operasional (KDO)** pemerintah daerah. Aplikasi ini mencatat data kendaraan, pegawai penanggung jawab, dan perangkat daerah. Aplikasi juga mengelola penjadwalan service, memantau kendaraan yang sudah layak service, dan mengirim email notifikasi ke pegawai penanggung jawab.

Untuk Screenshot aplikasi dan Koleksi API dan Dump DB ada dilink berikut
https://drive.google.com/drive/folders/1XUfvcTa2GikipOBEsrOvkDfojuVi6bI-

| Komponen | Teknologi |
| --- | --- |
| Backend | Laravel 13 (PHP 8.3), REST API `/api/v1`, Laravel Sanctum |
| Database | MariaDB / MySQL |
| Frontend | Blade, Bootstrap 4, jQuery DataTables, SweetAlert2 (via CDN), font Nunito |
| Email | SMTP; Mailpit untuk testing lokal |

**Fitur utama**
- Master data: kendaraan dinas, pegawai (PNS penanggung jawab), perangkat daerah, user (Admin & User)
- Penjadwalan service kendaraan
- Dashboard monitoring kendaraan layak service, dengan kriteria umur > 5 tahun, kondisi rusak ringan, atau rusak berat
- Kirim email notifikasi ke pegawai penanggung jawab, dengan mode dev/prod

---

## Daftar Isi
1. [Prasyarat](#1-prasyarat)
2. [Instalasi](#2-instalasi)
3. [Menjalankan Aplikasi](#3-menjalankan-aplikasi)
4. [Akun Login](#4-akun-login)
5. [Setup Email Testing (Mailpit)](#5-setup-email-testing-mailpit)
6. [Mode Email Notifikasi (dev/prod)](#6-mode-email-notifikasi-devprod)
7. [Testing (PHPUnit & Postman)](#7-testing-phpunit--postman)
8. [Perintah yang Sering Dipakai](#8-perintah-yang-sering-dipakai)
9. [Troubleshooting](#9-troubleshooting)
10. [Dokumentasi Lanjutan](#10-dokumentasi-lanjutan)

---

## 1. Prasyarat

| Kebutuhan | Versi | Cek |
| --- | --- | --- |
| PHP | 8.3+ dengan ekstensi `pdo_mysql`, `mbstring`, `xml`, `curl`, `intl`, `zip` | `php -v` dan `php -m` |
| Composer | 2.x | `composer -V` |
| MariaDB / MySQL | MariaDB 10.6+ atau MySQL 8+ | `mysql --version` |
| Git | terbaru | `git --version` |
| Mailpit | opsional, untuk testing email | `mailpit version` |

- **Node.js tidak diperlukan.** Library frontend dimuat dari CDN, jadi tidak ada langkah `npm install` / `npm run build`.
- Browser perlu akses internet untuk memuat CDN (jsDelivr) dan Google Fonts.

Contoh pemasangan di Ubuntu / WSL (PHP 8.3 dari PPA `ondrej/php`):
```bash
sudo apt install php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl php8.3-intl php8.3-zip mariadb-server
```

---

## 2. Instalasi

### 2.1 Clone repository
```bash
git clone https://github.com/daffaralfarisi/lv-kdo-apps.git
cd lv-kdo-apps
```

### 2.2 Install dependensi PHP
```bash
composer install
```

### 2.3 Siapkan file environment
```bash
cp .env.example .env
php artisan key:generate
```

Buka `.env`, lalu sesuaikan bagian berikut:
```dotenv
APP_URL=http://127.0.0.1:8000     # alamat yang dipakai untuk membuka aplikasi

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kdo-apps
DB_USERNAME=root
DB_PASSWORD=isi_password_database
```

### 2.4 Buat database
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS \`kdo-apps\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 2.5 Migrasi tabel & data dummy
```bash
php artisan migrate --seed
```
Perintah ini membuat semua tabel `kdo_*` beserta data contoh:
- 2 akun (admin & user)
- 3 perangkat daerah
- 15 pegawai
- 24 kendaraan
- 6 jadwal service

Untuk mengulang database dari awal (**semua data terhapus**):
```bash
php artisan migrate:fresh --seed
```

### 2.6 Izin folder (Linux)
```bash
chmod -R ug+rwX storage bootstrap/cache
```
Jika aplikasi juga dijalankan web server (Nginx/Apache) dengan user `www-data`, pastikan user web server **dan** user terminal sama-sama bisa menulis ke `storage/`, misalnya lewat grup yang sama.

---

## 3. Menjalankan Aplikasi

### Opsi A — server bawaan PHP (paling cepat)
```bash
php artisan serve
```
Buka **http://127.0.0.1:8000**. Anda otomatis diarahkan ke halaman login.

### Opsi B — virtual host (Nginx)
Arahkan document root ke folder `public/`. Contoh konfigurasi:
```nginx
server {
    listen 80;
    server_name kdo-apps.test;
    root /path/ke/lv-kdo-apps/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```
Tambahkan `127.0.0.1 kdo-apps.test` ke file hosts, set `APP_URL=http://kdo-apps.test` di `.env`, lalu buka http://kdo-apps.test.

> Perintah bawaan `composer run dev` / `php artisan dev` menjalankan Vite dan **tidak diperlukan** untuk proyek ini.

---

## 4. Akun Login

| Role | Email | Password | Akses |
| --- | --- | --- | --- |
| Admin | `admin@kdo-apps.test` | `password` | Semua menu + Master User |
| User | `user@kdo-apps.test` | `password` | Semua menu kecuali Master User |

> Ganti password akun bawaan lewat menu **Master User** sebelum aplikasi dipakai di server produksi.

---

## 5. Setup Email Testing (Mailpit)

Mailpit adalah server SMTP lokal. Semua email dari aplikasi **ditangkap** dan ditampilkan di web UI, tanpa ada yang benar-benar terkirim. Mailpit tidak perlu konfigurasi tambahan.

### 5.1 Install & jalankan Mailpit (pilih salah satu)

**Linux / WSL (tanpa sudo)**
```bash
mkdir -p ~/.local/bin ~/.local/share/mailpit
curl -sL https://github.com/axllent/mailpit/releases/latest/download/mailpit-linux-amd64.tar.gz | tar -xz -C ~/.local/bin mailpit
~/.local/bin/mailpit --database ~/.local/share/mailpit/mailpit.db --smtp 127.0.0.1:1025 --listen 127.0.0.1:8025
```
Biarkan terminal tersebut tetap terbuka. Agar Mailpit berjalan otomatis lewat systemd, lihat [docs/testing-api-dan-email.md](docs/testing-api-dan-email.md#menjalankan-mailpit).

**Docker**
```bash
docker run -d --name mailpit --restart unless-stopped -p 1025:1025 -p 8025:8025 axllent/mailpit
```

**macOS (Homebrew)**
```bash
brew install mailpit
brew services start mailpit
```

### 5.2 Arahkan Laravel ke Mailpit
Pengaturan ini sudah menjadi bawaan `.env.example`:
```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@kdo-apps.test"
```
Setiap kali `.env` diubah, jalankan:
```bash
php artisan config:clear
```

### 5.3 Cek koneksi
```bash
php artisan tinker --execute 'Illuminate\Support\Facades\Mail::raw("Tes Mailpit", fn ($m) => $m->to("tes@kdo-apps.test")->subject("Tes Mailpit"));'
```
Buka **http://localhost:8025**. Email "Tes Mailpit" harus muncul.

Untuk menguji email notifikasi aplikasi, buka **Dashboard Monitoring**, klik tombol **Kirim Email** pada salah satu kendaraan, lalu cek Mailpit.

---

## 6. Mode Email Notifikasi (dev/prod)

Mode diatur di `.env`, tanpa tabel database:
```dotenv
KDO_EMAIL_MODE=dev                            # dev | prod
KDO_EMAIL_DEV_PENERIMA=flixads999@gmail.com   # tujuan semua email saat mode dev
KDO_BATAS_UMUR_SERVICE=5                      # umur kendaraan (tahun) yang dilampaui agar layak service
```

| Mode | Tujuan email | Subjek |
| --- | --- | --- |
| `dev` | Semua dialihkan ke `KDO_EMAIL_DEV_PENERIMA` | Diawali `[DEV]` |
| `prod` | Email pegawai penanggung jawab | Normal |

Jalankan `php artisan config:clear` setelah mengubah mode. Mode yang aktif ditampilkan di Dashboard Monitoring.

Untuk **mengirim email sungguhan** (bukan ke Mailpit), ganti `MAIL_*` ke SMTP asli. Contoh Gmail dengan App Password:
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=akun@gmail.com
MAIL_PASSWORD=app-password-gmail
MAIL_FROM_ADDRESS="akun@gmail.com"
```

---

## 7. Testing (PHPUnit & Postman)

### 7.1 Feature test (PHPUnit)
Test memakai database terpisah `kdo-apps_testing` (lihat `phpunit.xml`), dengan user dan password database dari `.env`:
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS \`kdo-apps_testing\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
php artisan test
```
Hasil yang diharapkan: **29 test lulus**. Test tidak membutuhkan Mailpit, karena pengiriman email dipalsukan (fake).

### 7.2 Postman
1. Import [`docs/postman/KDO-Apps-API.postman_collection.json`](docs/postman/KDO-Apps-API.postman_collection.json) ke Postman.
2. Ubah variabel collection `base_url` (bawaan `http://127.0.0.1:8000`).
3. Jalankan **1. Autentikasi → Login Admin (token)**. Token tersimpan otomatis untuk request lain.
4. Atau jalankan semuanya lewat **Collection Runner**. Ada 45 request, dan data uji dihapus otomatis di folder terakhir.

Lewat command line (opsional, butuh Node.js):
```bash
npx newman run docs/postman/KDO-Apps-API.postman_collection.json --env-var base_url=http://127.0.0.1:8000
```

---

## 8. Perintah yang Sering Dipakai

| Perintah | Fungsi |
| --- | --- |
| `php artisan serve` | Menjalankan aplikasi di http://127.0.0.1:8000 |
| `php artisan migrate --seed` | Membuat tabel + data dummy |
| `php artisan migrate:fresh --seed` | Reset database (semua data terhapus) |
| `php artisan config:clear` | Memuat ulang `.env` setelah diubah |
| `php artisan route:list --path=api` | Melihat daftar endpoint API |
| `php artisan test` | Menjalankan feature test |
| `vendor/bin/pint` | Merapikan format kode PHP |

---

## 9. Troubleshooting

| Gejala | Solusi |
| --- | --- |
| `could not find driver` saat migrasi | Pasang ekstensi `pdo_mysql` (mis. `php8.3-mysql`) |
| `SQLSTATE[HY000] [1045] Access denied` | Periksa `DB_USERNAME` / `DB_PASSWORD` di `.env` |
| `Unknown database 'kdo-apps'` | Buat database dulu (langkah 2.4) |
| `laravel.log ... Permission denied` | File log dibuat user lain (mis. `www-data`). Samakan pemilik/grup folder `storage/` atau beri izin tulis |
| Kirim email gagal: "Email gagal dikirim. Periksa konfigurasi MAIL_*" | Mailpit belum berjalan atau `MAIL_*` salah. Jalankan Mailpit, lalu `php artisan config:clear` |
| Halaman tampil tanpa styling | Browser tidak bisa mengakses CDN atau Google Fonts. Periksa koneksi internet |
| Setelah login kembali ke halaman login / error 419 | Hapus cookie situs, buka aplikasi lewat satu alamat yang konsisten (sesuai `APP_URL`), lalu `php artisan config:clear` |
| Error 429 saat login | Terlalu banyak percobaan (maks. 5 per menit). Tunggu 1 menit |
| Perubahan `.env` tidak berefek | Jalankan `php artisan config:clear` |

---

## 10. Dokumentasi Lanjutan

| Dokumen | Isi |
| --- | --- |
| [docs/README.md](docs/README.md) | Ringkasan fitur & hak akses |
| [docs/skema-database.md](docs/skema-database.md) | Tabel `kdo_*`, relasi, ERD |
| [docs/api.md](docs/api.md) | Referensi endpoint API v1 |
| [docs/testing-api-dan-email.md](docs/testing-api-dan-email.md) | Panduan Postman & Mailpit |
| [docs/keputusan-teknis.md](docs/keputusan-teknis.md) | Catatan keputusan teknis |
| [docs/log-pengembangan.md](docs/log-pengembangan.md) | Log proses pengembangan |
