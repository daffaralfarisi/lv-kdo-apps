# Testing API (Postman) & Email (Mailpit)

## 1. Postman collection

File: [`docs/postman/KDO-Apps-API.postman_collection.json`](postman/KDO-Apps-API.postman_collection.json) — format Postman v2.1, **9 folder, 45 request, 126 assertion**.

### Import & persiapan
1. Postman → **Import** → pilih file di atas.
2. Buka collection **KDO Apps API v1** → tab **Variables** → ubah `base_url` sesuai server, mis. `http://127.0.0.1:8000` (`php artisan serve`) atau `http://kdo-apps.test`.
3. Jalankan **1. Autentikasi → Login Admin (token)**. Token tersimpan otomatis ke variabel `token` dan dipakai semua request sebagai Bearer.

### Isi folder
| Folder | Isi |
| --- | --- |
| 1. Autentikasi | Login admin & user (token disimpan otomatis), profil |
| 2. Perangkat Daerah | Daftar (filter/sort), tambah, detail, ubah |
| 3. Pegawai | Daftar, tambah (NIP & email acak), detail, ubah |
| 4. Kendaraan | Daftar, tambah (normalisasi nopol), detail, ubah, filter per perangkat daerah |
| 5. Penjadwalan Service | Daftar, tambah, detail, ubah |
| 6. Monitoring & Email Notifikasi | Ringkasan, monitoring per kriteria, **kirim email**, riwayat email |
| 7. Master User (Admin) | Daftar, tambah, detail, ubah |
| 8. Contoh Respons Error | 422 validasi, 401 tanpa token, 404, 403 role user, 409 hapus data yang masih dipakai |
| 9. Hapus Data Uji & Logout | Menghapus semua data buatan collection lalu logout |

Setiap request memiliki test script. ID data uji (`perangkat_daerah_id`, `pegawai_id`, `kendaraan_id`, `jadwal_id`, `user_id`) disimpan otomatis ke variabel collection. Request di folder yang lebih bawah memakai ID tersebut, jadi jalankan folder berurutan.

### Menjalankan semuanya sekaligus
- **Collection Runner**: klik **Run collection**, biarkan urutan default, lalu **Run**. Folder 9 membersihkan data uji.
- **CLI (Newman)**:
  ```bash
  npx newman run docs/postman/KDO-Apps-API.postman_collection.json --env-var base_url=http://127.0.0.1:8000
  ```
  Hasil verifikasi: 45 request, 126 assertion, 0 gagal (±3 detik).

### Catatan
- Setiap request sudah mengirim header `Accept: application/json`. Tanpa header ini, error validasi tidak dikembalikan sebagai JSON.
- Login dibatasi 5 percobaan/menit per email + IP (respons 429).
- Postman tidak mengirim header `Origin`/`Referer`, sehingga login berjalan dalam **mode token**. Browser yang membuka aplikasi memakai mode session.
- Nama perangkat, NIP, nomor polisi, dan email dibuat acak di pre-request script, sehingga collection bisa dijalankan berulang.
- Jika run terhenti di tengah, data uji bisa tertinggal. Jalankan folder 9, atau hapus manual data berawalan "Dinas Uji Postman" / nopol berakhiran "PST".

## 2. Testing email dengan Mailpit

Mailpit adalah server SMTP lokal yang **menangkap** semua email dari aplikasi dan menampilkannya di web UI. Email tidak benar-benar dikirim ke penerima.

### Konfigurasi
Mailpit sendiri tidak perlu konfigurasi tambahan (tanpa login, tanpa TLS). Cukup arahkan Laravel ke Mailpit di `.env` (sudah diatur):
```dotenv
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
```
Setelah mengubah `.env`, jalankan `php artisan config:clear`.

### Pemasangan di mesin ini
| Item | Nilai |
| --- | --- |
| Binary | `~/.local/bin/mailpit` (v1.33.1, SHA-256 cocok dengan digest rilis resmi GitHub) |
| Penyimpanan email | `~/.local/share/mailpit/mailpit.db` (tetap ada setelah restart) |
| SMTP | `127.0.0.1:1025` |
| Web UI | http://localhost:8025 |

### Menjalankan Mailpit
**Manual** (di terminal tersendiri):
```bash
~/.local/bin/mailpit --database ~/.local/share/mailpit/mailpit.db --smtp 127.0.0.1:1025 --listen 127.0.0.1:8025
```

**Otomatis setiap WSL menyala** (systemd user service; jalankan sekali):
```bash
mkdir -p ~/.config/systemd/user
cat > ~/.config/systemd/user/mailpit.service <<'UNIT'
[Unit]
Description=Mailpit - penangkap email lokal (KDO Apps)
After=network.target

[Service]
ExecStart=%h/.local/bin/mailpit --database %h/.local/share/mailpit/mailpit.db --smtp 127.0.0.1:1025 --listen 127.0.0.1:8025
Restart=on-failure

[Install]
WantedBy=default.target
UNIT
systemctl --user daemon-reload
systemctl --user enable --now mailpit
```
Untuk mematikan dan menghapusnya: `systemctl --user disable --now mailpit && rm ~/.config/systemd/user/mailpit.service`. Hentikan dulu Mailpit yang dijalankan manual agar port tidak bentrok.

### Uji cepat
```bash
php artisan tinker --execute 'Illuminate\Support\Facades\Mail::raw("Tes Mailpit", fn ($m) => $m->to("tes@kdo-apps.test")->subject("Tes Mailpit"));'
```
Lalu buka http://localhost:8025.

### Menguji email notifikasi KDO
- Kirim dari **Dashboard Monitoring → Kirim Email**, atau dari Postman **6. → Kirim Email Notifikasi**.
- **Mode dev** (`KDO_EMAIL_MODE=dev`): di Mailpit email ditujukan ke `flixads999@gmail.com`, subjek berawalan `[DEV]`, dan isinya memuat panel "MODE DEVELOPMENT" dengan alamat pegawai sebenarnya.
- **Mode prod** aman diuji dengan Mailpit: set `KDO_EMAIL_MODE=prod`, jalankan `php artisan config:clear`, lalu kirim. Email di Mailpit akan ditujukan ke alamat pegawai, tetapi tidak benar-benar terkirim.

### Masalah umum
| Gejala | Penyebab / solusi |
| --- | --- |
| Respons 502 "Email gagal dikirim. Periksa konfigurasi MAIL_*" | Mailpit belum berjalan. Jalankan Mailpit, cek dengan `ss -ltn \| grep 1025` |
| http://localhost:8025 tidak terbuka | Pastikan Mailpit berjalan. WSL2 meneruskan `localhost` ke Windows secara otomatis |
| Ingin benar-benar mengirim ke Gmail | Ganti `MAIL_*` ke SMTP asli (lihat [instalasi.md](instalasi.md)) |
