<?php

use Illuminate\Support\Facades\Route;

/*
| Halaman web hanya merender kerangka Blade; seluruh data dimuat dari API /api/v1.
*/

Route::view('/login', 'auth.login')->middleware('guest')->name('login');

Route::middleware('auth')->group(function (): void {
    Route::redirect('/', '/dashboard');
    Route::view('/dashboard', 'dashboard.index')->name('dashboard');
    Route::view('/kendaraan', 'kendaraan.index')->name('kendaraan.index');
    Route::view('/pegawai', 'pegawai.index')->name('pegawai.index');
    Route::view('/perangkat-daerah', 'perangkat-daerah.index')->name('perangkat-daerah.index');
    Route::view('/penjadwalan-service', 'penjadwalan-service.index')->name('penjadwalan-service.index');
    Route::view('/users', 'users.index')->middleware('can:admin')->name('users.index');
});
