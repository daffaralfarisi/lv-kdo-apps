# Referensi API v1

Base URL: `/api/v1`. Semua respons berformat JSON. Selalu kirim header `Accept: application/json`.

Postman collection siap pakai: [`postman/KDO-Apps-API.postman_collection.json`](postman/KDO-Apps-API.postman_collection.json) (panduan di [testing-api-dan-email.md](testing-api-dan-email.md)).

## Autentikasi (Sanctum)
- **Frontend (browser)**: login membuat session, dan request berikutnya memakai cookie + header `X-CSRF-TOKEN`.
- **Klien eksternal** (Postman/mobile): login mengembalikan token, lalu kirim `Authorization: Bearer <token>`.

| Method | Endpoint | Keterangan |
| --- | --- | --- |
| POST | `/auth/login` | Body `kdo_email`, `kdo_password`, `device_name?`. Throttle 5×/menit |
| POST | `/auth/logout` | Hapus token / akhiri session |
| GET | `/auth/me` | Profil user login |

## Resource CRUD
Semua resource berikut mendukung `GET` (index), `POST`, `GET /{id}`, `PUT /{id}`, dan `DELETE /{id}`.

| Endpoint | Filter index | Sort (`sort`, `direction=asc|desc`) |
| --- | --- | --- |
| `/perangkat-daerah` | `search`, `kdo_status_perangkat` | nama, status, `pegawai_count`, `kendaraan_count` |
| `/pegawai` | `search` (nama/NIP/jabatan), `kdo_perangkat_daerah_id`, `kdo_golongan` | nama, nip, jabatan, golongan, `kendaraan_count` |
| `/kendaraan` | `search`, `kdo_perangkat_daerah_id` (melalui pegawai), `kdo_pegawai_id`, `kdo_kondisi`, `kdo_status` | nopol, merek, tipe, tahun, warna, kondisi, status |
| `/penjadwalan-service` | `search` (nopol), `kdo_status_jadwal`, `kdo_perangkat_daerah_id`, `kdo_kendaraan_id`, `tanggal_dari`, `tanggal_sampai` (YYYY-MM-DD) | mulai, selesai |
| `/users` (admin) | `search`, `kdo_role` | nama, email, role |

Pagination: `page` dan `per_page` (1–500, default 10). Respons index: `{ data: [...], links, meta: { total, ... } }`.

Kendaraan tidak memiliki `kdo_perangkat_daerah_id`; perangkat daerahnya ada di `pegawai.perangkat_daerah`. Payload pegawai wajib menyertakan `kdo_perangkat_daerah_id`, dan payload kendaraan wajib `kdo_pegawai_id`.

Nama field request/response sama dengan kolom database (`kdo_*`). Respons juga memuat field turunan seperti `kdo_umur`, `kdo_kondisi_label`, `kdo_alasan_service`, dan `kdo_status_jadwal`.

## Monitoring & notifikasi
| Method | Endpoint | Keterangan |
| --- | --- | --- |
| GET | `/monitoring/ringkasan` | Total, layak service, per kriteria, belum dinotifikasi, mode email, rekap per perangkat |
| GET | `/monitoring/kendaraan` | Daftar layak service. Filter: `kriteria` (`umur`, `rusak_ringan`, `rusak_berat`), `kdo_perangkat_daerah_id`, `status_notifikasi` (`belum`/`sudah`), `search` |
| GET | `/kendaraan/{id}/notifikasi-service` | Riwayat email kendaraan |
| POST | `/kendaraan/{id}/notifikasi-service` | Kirim email layak service ke penanggung jawab (dev: dialihkan ke penerima dev) |

## Kode status
| Kode | Arti |
| --- | --- |
| 200 / 201 | Berhasil / data dibuat (disertai `message`) |
| 401 | Belum login / token tidak valid |
| 403 | Role tidak berhak (mis. user membuka `/users`) |
| 404 | Data tidak ditemukan |
| 409 | Tidak bisa dihapus karena masih dipakai (perangkat daerah masih punya pegawai, pegawai masih punya kendaraan, hapus akun sendiri) |
| 422 | Validasi gagal: `{ message, errors: { field: [pesan] } }` |
| 429 | Terlalu banyak percobaan login |
| 502 | Email gagal dikirim (cek konfigurasi `MAIL_*`) |

## Contoh
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login -H 'Accept: application/json' \
  -H 'Content-Type: application/json' -d '{"kdo_email":"admin@kdo-apps.test","kdo_password":"password"}'

curl http://127.0.0.1:8000/api/v1/monitoring/kendaraan?kriteria=umur \
  -H 'Accept: application/json' -H 'Authorization: Bearer <token>'
```
