<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Batas Umur Kendaraan Layak Service
    |--------------------------------------------------------------------------
    |
    | Kendaraan dengan umur (tahun berjalan - tahun produksi) LEBIH DARI nilai
    | ini masuk kriteria monitoring "tahun produksi > 5 tahun".
    |
    */

    'batas_umur_service' => (int) env('KDO_BATAS_UMUR_SERVICE', 5),

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Email Notifikasi
    |--------------------------------------------------------------------------
    |
    | mode = "dev"  : semua email notifikasi dialihkan ke penerima_dev.
    | mode = "prod" : email dikirim ke alamat email pegawai penanggung jawab.
    | Ubah lewat .env (KDO_EMAIL_MODE), lalu jalankan php artisan config:clear.
    |
    */

    'email_notifikasi' => [
        'mode' => env('KDO_EMAIL_MODE', 'dev'),
        'penerima_dev' => env('KDO_EMAIL_DEV_PENERIMA', 'flixads999@gmail.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Golongan Pegawai Negeri Sipil
    |--------------------------------------------------------------------------
    */

    'golongan' => [
        'I/a', 'I/b', 'I/c', 'I/d',
        'II/a', 'II/b', 'II/c', 'II/d',
        'III/a', 'III/b', 'III/c', 'III/d',
        'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e',
    ],

];
