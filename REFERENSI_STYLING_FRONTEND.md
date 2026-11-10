Terapkan gaya frontend berikut di project ini (Bootstrap 4, Font Awesome 5, jQuery + DataTables, SweetAlert2 v11, moment + daterangepicker). Pakai nilai persis seperti di bawah, jangan diganti warna/ukuran lain. Tulis CSS sebagai partial yang di-include sekali per halaman.

**Palet**
- Navy `#082556` (judul, kode, teks penting), primary `#287bb5` (hover `#20699b`).
- Teks isi `#3d4657`, teks redup `#9aa3b2` / `#a8b0bd`, header tabel `#94a3b8`.
- Garis: field `#e3e8ee`, tabel `#eef1f5`, antar-baris `#f2f5f8`.
- Peringatan: bg `#fffaeb`, border `#fedf89`, teks `#7a2e0e`, ikon `#dc6803`.
- Aman: bg `#f6fef9`, border `#abefc6`, teks `#067647`, ikon `#079455`.
- Bahaya `#b42318` / `#d92d20`.
- Focus ring: border `#7ea9d8` + `box-shadow: 0 0 0 3px rgba(40,123,181,.13)`.
- Radius: field/tombol 10px, chip 7px, pill 999px, kartu 14px, popup 16–18px.

**1. Filter bar**
- Bungkus filter dengan kartu putih tanpa border (radius 14px, `shadow-sm`). Di dalamnya satu baris flex-wrap, jarak antar-elemen 9px.
- Setiap filter adalah "field":
  - Tinggi 36px, padding 0 11px, border 1px `#e3e8ee`, radius 10px.
  - Ikon FA 11.5px warna `#9aa3b2` di kiri; select/input tanpa border, 12px, tebal 600, warna `#3d4657`.
  - Saat hover border jadi `#ccd7e3`; saat `:focus-within` pakai focus ring.
- Select dan input tanggal diberi ikon chevron kecil (8px, `#b0b8c4`) di kanan.
- Setiap `<select>` di dalam field otomatis diganti dropdown kustom dengan JS murni. `<select>` asli disembunyikan tapi tetap jadi sumber nilai.
  - Panel muncul 10px di bawah field: min-width 180px, radius 10px, padding 5px, `box-shadow: 0 12px 30px rgba(15,23,42,.15)`.
  - Opsi: padding 7px 10px, radius 7px. Hover bg `#e6f1f9` teks navy; opsi aktif bg `#287bb5` teks putih dengan ikon centang di kanan.
  - Klik di luar atau Esc menutup panel. Saat memilih, picu event `change` native di select asli.
- Filter tanggal pakai satu daterangepicker (bukan dua input date):
  - Placeholder "Semua tanggal", input readonly, `autoUpdateInput: false`, format `DD/MM/YYYY`.
  - Pilihan cepat: Hari ini, Kemarin, 7 hari terakhir, 30 hari terakhir, Bulan ini, Bulan lalu, Tahun ini.
  - Label tombol "Terapkan" / "Hapus"; "Hapus" mengosongkan rentang.
  - Kirim ke server sebagai `YYYY-MM-DD`.
  - Tema: radius 12px tanpa border, range/tanggal aktif `#287bb5`, in-range bg `#e6f1f9`.
- Input pencarian di-debounce 400ms.
- Ada pemisah vertikal 1px × 24px `#e8ecf1` antar-kelompok.
- Tombol Reset di ujung kanan (`margin-left:auto`): putih, border `#e3e8ee`, 11.5px tebal 700 warna `#6b7688`. Saat hover bg `#fdecea`, border `#f5c2c7`, teks `#b42318`.
- Varian tombol utama: bg `#287bb5`, teks putih.
- Reset mengosongkan semua filter. Untuk select, set nilainya lalu `dispatchEvent(new Event('change'))` native, karena `$.trigger('change')` tidak mengupdate label dropdown kustom.

**2. Tabel striped (DataTables server-side)**
- Font 12.5px.
- Header: 10.5px, tebal 700, uppercase, letter-spacing .4px, warna `#94a3b8`, garis bawah 1px `#eef1f5`, tanpa garis atas.
- Sel: padding .6rem, `vertical-align: middle`, garis atas 1px `#f2f5f8`, warna `#3d4657`.
- Baris ganjil bg `#f6f8fb`, hover `#eef4fb`. Tulis aturan striped SEBELUM `:hover`.
- Pola sel:
  - Kode: tebal 700 navy, dengan chip modul di bawahnya.
  - Orang: nama di atas, badge/NIK di bawahnya (10.5px `#9aa3b2`).
  - Nominal: rata kanan `Rp 1.234.567` (`id-ID`).
  - Nilai kosong: `—` warna `#a8b0bd`.
- Bahasa DataTables Indonesia: "Memuat...", "Menampilkan _START_–_END_ dari _TOTAL_ baris", "Tidak ada data", Awal / Akhir / Berikutnya / Sebelumnya.

**3. Badge dan indikator**
- Chip: inline-block, padding 2px 9px, radius 7px, 10px tebal 700, letter-spacing .3px. Varian:
  - default biru: bg `#eef4fb`, teks `#1f5f99`;
  - ungu: `#f1ecfb` / `#5b3ea6`;
  - amber: `#fff6e0` / `#9a6700`;
  - hijau: `#ecfdf3` / `#067647`;
  - merah: `#fef3f2` / `#b42318`.
- Pill sekunder: 10px tebal 700, `#667085` di atas `#f2f4f7`, radius 999px, padding 0 7px.
- Baris status: 11px `#667085`, bagian dipisah " · ", bagian kritis (mis. "lewat tenggat") merah `#b42318` tebal.
- Catatan indikator (kotak satu kalimat):
  - flex, gap 8px, padding 9px 12px, radius 10px, 12.5px line-height 1.5.
  - Ikon `fa-info-circle` warna peringatan; varian `.aman` pakai warna aman dan `fa-check-circle`.
  - Angka penting ditulis tebal.
  - Tautan "Lihat Selengkapnya →" tebal navy (hover `#287bb5` + underline) membuka popup rincian.
  - Teks tambahan kecil 11px `#98a2b3` di baris bawah.
- Tombol aksi tabel "Lihat":
  - Tinggi 30px, padding 0 12px, radius 8px, border `#e3e8ee`, putih, 11px tebal 700 navy, ikon `fa-file-alt`.
  - Saat hover: bg navy, teks putih.

**4. Popup**
- **Preset SweetAlert2** lewat `customClass` (helper `swalModern(gaya)`), supaya dialog lain tidak ikut berubah:
  - Kotak: radius 18px, tanpa border, `box-shadow: 0 24px 48px rgba(15,23,42,.18)`, padding 26px 26px 20px.
  - Judul 17px tebal 800 `#0f172a`; teks 13px `#475467` line-height 1.6.
  - Ikon diperkecil ke 54px dengan border 3px warna lembut: info `#cfe4fb`/`#175cd3`, warning `#fedf89`/`#b54708`, error `#fecdca`/`#d92d20`, success `#c6f0d8`/`#067647`.
  - Tombol: radius 10px, padding 11px 20px, 13.5px tebal 700, tanpa shadow. Konfirmasi `#287bb5`; varian `tolak` `#d92d20`; batal bg `#f2f4f7` teks `#475467`.
  - Focus ring hanya pada `:focus-visible`.
  - Input/textarea Swal: border `#d0d5dd`, radius 10px, focus ring standar.
- **Popup tabel rincian** (JS murni, tanpa library, bisa dibuka dari dalam modal):
  - Overlay `position:fixed; inset:0`, `z-index: 20000` (di atas modal Bootstrap 1050 dan Swal 1060), bg `rgba(15,23,42,.45)`, ditempel ke `<body>`.
  - Kotak: max-width 760px, max-height 84vh, radius 16px, `box-shadow: 0 24px 48px rgba(15,23,42,.28)`, kolom flex dengan isi yang bisa di-scroll.
  - Kepala: bg `#f3f7fd`, garis bawah `#e4ecf8`.
    - Ikon kotak 32px radius 9px dengan gradien `135deg #082556 → #287bb5`.
    - Judul 13.5px tebal navy, sub-judul 11.5px `#7089ab` (nama · jumlah · total).
    - Tombol × di kanan.
  - Tabel: header sticky bg `#f8fafc` (10.5px uppercase `#667085`), sel padding 8px 14px dengan garis `#eef1f5`, kolom nominal rata kanan, `tfoot` total tebal 800.
  - Kaki 11px `#98a2b3` untuk keterangan seperti "Menampilkan 100 dari 130".
  - Tutup lewat ×, klik overlay, atau Esc. Esc ditangkap di fase capture + `stopPropagation`, agar modal di bawahnya tidak ikut tertutup. Fokus kembali ke tautan pemicu.
  - Di layar <576px: padding overlay 10px, padding sel 7px 9px.
- **Swal untuk teks panjang:**
  - Lebar 760, `showCloseButton`.
  - Isi dalam kotak `white-space: pre-wrap`, 12.5px, bg `#f8fafc`, border `#eef1f5`, radius 10px, max-height 55vh, scroll.
  - Di bawahnya metadata 11px `#6b7688` (versi, waktu, hash) dengan `word-break: break-all`.

**Aturan umum**
- Semua data dari server di-escape sebelum masuk HTML.
- Format rupiah `Rp 1.234.567`. Tanggal tabel `DD/MM/YYYY HH:mm`, tanggal dalam kalimat `3 Nov 2026` (bulan Jan…Des, Agustus = Agu).
- Partial yang di-include sebelum jQuery dimuat harus memakai JS murni.
- Beri guard agar CSS/JS partial tidak terpasang dua kali.
- Seluruh label UI dalam Bahasa Indonesia.
