# Catatan Keputusan Teknis

Format setiap keputusan: **Keputusan**, lalu *alasan* dan *konsekuensi*.

### K-01 Satu project, backend REST API (`/api/v1`) + frontend Blade
Halaman Blade hanya merender kerangka (filter, tabel, modal). Semua data dibaca dan ditulis lewat AJAX ke API.
*Alasan*: memenuhi permintaan "frontend dan backend 1 project, backend via API". API yang sama bisa dipakai aplikasi lain (mobile) tanpa perubahan.

### K-02 Autentikasi Laravel Sanctum mode ganda
Request dari frontend bersifat *stateful* (session + CSRF). Klien eksternal mendapat token Bearer dari endpoint login yang sama (`$request->hasSession()`). `Sanctum::currentRequestHost()` diaktifkan agar host apa pun yang menyajikan aplikasi dianggap stateful.
*Alasan*: token tidak perlu disimpan di localStorage (rawan XSS), dan halaman web tetap dilindungi middleware `auth`.

### K-03 Prefix `kdo_` untuk semua tabel & field aplikasi, termasuk user
Tabel `users` bawaan diganti `kdo_user`, dengan `$authPasswordName = 'kdo_password'` dan `$rememberTokenName = 'kdo_remember_token'`. Timestamp memakai konstanta `CREATED_AT = 'kdo_created_at'`.
*Konsekuensi*: tabel internal framework/Sanctum tetap memakai nama bawaan karena namanya di-hardcode di Laravel. Migration users bawaan diedit (bukan migration baru) karena project masih skeleton dan tabelnya kosong.

### K-04 Penambahan field di luar mapping
- `kdo_pegawai.kdo_email`: **wajib ada** untuk fitur kirim email ke pegawai. Nullable; pegawai tanpa email ditandai di dashboard dan pengirimannya ditolak (422).
- `kdo_penjadwalan_service.kdo_keterangan`: catatan pekerjaan service (opsional).
- Timestamp `kdo_created_at`/`kdo_updated_at` di setiap tabel untuk audit.
- Tabel `kdo_notifikasi_service`: riwayat email (siapa, kapan, ke mana, mode). Dipakai untuk kolom "Email Terakhir" dan filter "belum dinotifikasi".

### K-05 Nilai domain sebagai PHP Enum, disimpan sebagai string
`KondisiKendaraan` (baik/rusak_ringan/rusak_berat), `StatusKendaraan`, `StatusPerangkat`, `RoleUser`, `ModeEmail`, `StatusJadwal`, dan `KriteriaService`. Golongan PNS (I/a–IV/e) disimpan di `config/kdo.php`.
*Alasan*: satu sumber kebenaran untuk validasi, label UI, dan filter. Kolom string lebih mudah dikembangkan daripada tipe ENUM MySQL.

### K-06 Kriteria layak service
Sebuah kendaraan layak service jika memenuhi salah satu kriteria: `tahun_produksi <= tahun_ini - 5 - 1` (umur **> 5** tahun, mis. produksi 2020 atau sebelumnya pada 2026), kondisi `rusak_ringan`, atau kondisi `rusak_berat`. Batas umur diatur di `KDO_BATAS_UMUR_SERVICE`. Logika ini dipusatkan di scope `Kendaraan::layakService()` dan `alasanService()`.

### K-07 Integritas relasi
- Perangkat daerah yang masih punya pegawai, dan pegawai yang masih punya kendaraan, **tidak bisa dihapus** (FK restrict + respons 409 dengan pesan).
- Menghapus kendaraan ikut menghapus jadwal & riwayat notifikasinya (cascade).
- Riwayat notifikasi tetap tersimpan meski pegawai/user pengirim dihapus (null on delete).

### K-08 Konfigurasi email dev/prod lewat file config, tanpa tabel
Sesuai arahan revisi: mode dibaca dari `config('kdo.email_notifikasi.mode')` (env `KDO_EMAIL_MODE`), lalu if/else di `ModeEmail::alamatTujuan()`. Di mode **dev**, semua email dialihkan ke `flixads999@gmail.com`. Nilai mode yang tidak dikenal dianggap dev agar aman.
Status "sudah/belum dinotifikasi" dihitung **per mode aktif**, sehingga email uji coba di mode dev tidak membuat kendaraan tercatat sudah dinotifikasi saat pindah ke prod. Menu UI untuk mengubah mode (versi awal dengan tabel) dibatalkan; dashboard hanya menampilkan mode aktif.

### K-09 Email dikirim sinkron (tanpa queue)
*Alasan*: admin langsung tahu hasilnya dan tidak perlu menjalankan queue worker. Kegagalan transport ditangkap dan dikembalikan sebagai 502. Jika nanti ada kirim massal, sebaiknya pindah ke `ShouldQueue`.

### K-10 Hak akses: hanya Master User yang khusus admin
Interpretasi kebutuhan: admin dapat mengakses semua menu dan menambah user, sedangkan user mengakses semua menu operasional. Implementasi memakai Gate `admin` dengan middleware `can:admin` di route web `/users` dan API `/users`; menu disembunyikan untuk role user. Admin tidak bisa menghapus atau menurunkan role akunnya sendiri, sehingga selalu ada minimal satu admin.

### K-11 Frontend: Bootstrap 4 + jQuery + DataTables + SweetAlert2 + moment/daterangepicker via CDN
Mengikuti `REFERENSI_STYLING_FRONTEND.md`. Vite/Tailwind bawaan **tidak dipakai** karena styling referensi berbasis Bootstrap 4 dan Node 18 di mesin ini tidak kompatibel dengan Vite 8. Versi library dipin di `partials/vendor-*.blade.php`. Untuk lingkungan tanpa internet, file tersebut cukup diganti ke salinan lokal di `public/vendor`.

### K-12 UI kit sebagai partial Blade
`partials/kdo-ui-css.blade.php` dan `partials/kdo-ui-js.blade.php` dipasang sekali per halaman dengan guard `@once` dan `if (window.KDO) return`. Isinya:
- dropdown kustom dan popup rincian (JS murni);
- `swalModern()`, Swal teks panjang, dan filter bar dengan debounce 400 ms;
- reset filter memakai `dispatchEvent(new Event('change'))` native;
- daterangepicker dengan nilai `YYYY-MM-DD` ke server;
- wrapper DataTables server-side yang memetakan `start/length/order` ke `page/per_page/sort/direction` API;
- helper CRUD generik (modal form, error 422 per field, konfirmasi hapus).

Semua data server di-escape (`KDO.esc`) sebelum masuk HTML. Format tanggal mengikuti referensi: tabel `DD/MM/YYYY HH:mm`, kalimat `3 Nov 2026` (Agustus = Agu).

### K-13 Bahasa Indonesia untuk pesan API
`APP_LOCALE=id`, `lang/id/validation.php` (nama atribut ramah, mis. `kdo_nip` menjadi "NIP"), dan `lang/id/auth.php`. Exception 401/403/404/429 di `/api/*` diberi pesan Indonesia di `bootstrap/app.php`.

### K-14 Test memakai MariaDB `kdo-apps_testing`
Ekstensi `pdo_sqlite` tidak tersedia, jadi `phpunit.xml` diarahkan ke database MariaDB terpisah dengan `LazilyRefreshDatabase`. Keuntungannya, test berjalan di engine yang sama dengan produksi.

### K-15 Palet sidebar mengikuti warna brand
Atas permintaan user, warna diambil dari pixel gambar referensi: latar sidebar **biru `#006cb8`**, menu aktif **oranye `#ff9e00`**. Warna konten lain tetap mengikuti `REFERENSI_STYLING_FRONTEND.md`.
*Konsekuensi*: awalnya teks menu aktif memakai navy `#082556` (kontras ±7:1) karena teks putih di atas oranye hanya ±2:1; atas permintaan user kemudian diganti putih (lihat K-19). Teks menu biasa `#eaf3fb` di atas biru (±4,9:1). Nilai warna disimpan sebagai variabel CSS `--kdo-sb-biru` dan `--kdo-sb-oren` di `.kdo-sidebar`.

### K-16 Logo sidebar memakai favicon BPAD
Brand sidebar disusun bertumpuk dan rata tengah: logo (`public/favicon.ico`), lalu "KDO Apps", lalu "Kendaraan Dinas Operasional".
*Konsekuensi*: file favicon berisi PNG 1000×1000 berlatar transparan, dan warna birunya sama dengan latar sidebar (`#006cb8`). Karena itu logo diberi alas kotak putih (radius 14px) agar daun biru dan tulisan BPAD tetap terlihat. Semua halaman juga menautkan favicon lewat `<link rel="icon">`.

### K-17 Perangkat daerah dipindah dari kendaraan ke pegawai
Revisi user: kolom `kdo_perangkat_daerah_id` dipindah dari `kdo_kendaraan` ke `kdo_pegawai`. Rantai relasinya menjadi kendaraan → pegawai (penanggung jawab) → perangkat daerah. `PerangkatDaerah::kendaraan()` memakai `HasManyThrough` melalui pegawai.
*Konsekuensi*:
- `kdo_kendaraan.kdo_pegawai_id` dan `kdo_pegawai.kdo_perangkat_daerah_id` dibuat **wajib**, agar setiap kendaraan selalu punya instansi.
- Memindahkan pegawai ke perangkat daerah lain otomatis ikut memindahkan kendaraan tanggungannya.
- Filter `kdo_perangkat_daerah_id` pada kendaraan, monitoring, dan penjadwalan bekerja melalui pegawai.
- Migration create-table diedit (belum pernah di-commit/deploy), lalu dijalankan `migrate:fresh --seed`. Database saat itu hanya berisi data dummy.

### K-18 Halaman login model kartu dua kolom
Mengikuti gambar contoh user:
- logo BPAD di panel kiri (`#f3f4f6`), form di panel kanan;
- latar gradasi lembut dengan lingkaran pastel;
- ikon bintang di atas judul "Selamat datang di aplikasi Monitoring KDO" dan subjudul "Kendaraan Dinas Operasional";
- tanpa widget Cloudflare.

Isian dibuat lebih modern: tinggi 48px, ikon di dalam isian, focus ring sesuai referensi, tombol mata untuk password, pesan error di bawah tiap isian, dan tombol berstatus "Memproses...". Label memakai "Email" (bukan "Username") karena login memakai `kdo_email`. Di layar < 768px panel logo disembunyikan dan logo kecil tampil di atas form.

### K-19 Sidebar: teks menu aktif putih & efek collapse
- Teks menu aktif `#fff` di atas oranye (permintaan user, menggantikan navy pada K-15).
- Sidebar bisa diciutkan menjadi rail ikon 78px lewat tombol bulat di tepi sidebar (chevron berputar).
- Animasi: transisi lebar 0,3 detik `cubic-bezier(.4,0,.2,1)`; teks menu memudar, label grup berubah menjadi garis pendek, logo mengecil, dan tooltip muncul di kanan ikon saat sidebar ciut.
- Status ciut disimpan di `localStorage` (`kdo.sidebar.ciut`) dan diterapkan di `<head>` sebelum render agar tidak berkedip. Kolom DataTables disesuaikan ulang setelah animasi.
- Di layar < 992px sidebar menjadi drawer dengan backdrop gelap (tutup lewat backdrop atau Esc).
- Animasi dimatikan untuk pengguna yang mengaktifkan `prefers-reduced-motion`.

### K-20 Font Nunito
Font aplikasi diganti Nunito memakai link Google Fonts dari user (bobot 200–900), dengan fallback system font. Ditambah `preconnect` ke fonts.googleapis.com dan fonts.gstatic.com.

### K-21 Postman collection sebagai pengujian API end-to-end
File `docs/postman/KDO-Apps-API.postman_collection.json` (format v2.1, 9 folder, 45 request) dibuat dengan script generator agar JSON-nya selalu valid.
- Setiap request punya test script.
- Token dan ID data uji disimpan ke variabel collection.
- Data unik dibuat acak di pre-request, sehingga collection bisa dijalankan berulang.
- Folder terakhir menghapus data uji.

Diverifikasi dengan Newman: 126 assertion lulus, dan database kembali ke kondisi semula.

### K-22 Mailpit untuk testing email
Docker tidak tersedia, jadi Mailpit dipasang sebagai binary tingkat user: `~/.local/bin/mailpit` v1.33.1, dengan SHA-256 yang dicocokkan ke digest rilis GitHub.
- SMTP `127.0.0.1:1025`, UI `127.0.0.1:8025` (tidak terekspos ke jaringan).
- Email disimpan di `~/.local/share/mailpit/mailpit.db`.
- `.env` dan `.env.example` memakai SMTP Mailpit.

*Konsekuensi*:
- Mode prod bisa diuji dengan aman karena tidak ada email yang benar-benar terkirim.
- Mailpit belum dipasang sebagai service permanen; unit file systemd disediakan di `testing-api-dan-email.md` untuk diaktifkan sendiri.
- Tanpa Mailpit berjalan, kirim email menghasilkan 502 dengan pesan yang jelas.
