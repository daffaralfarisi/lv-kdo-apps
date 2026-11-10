<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\KendaraanController;
use App\Http\Controllers\Api\V1\MonitoringController;
use App\Http\Controllers\Api\V1\NotifikasiServiceController;
use App\Http\Controllers\Api\V1\PegawaiController;
use App\Http\Controllers\Api\V1\PenjadwalanServiceController;
use App\Http\Controllers\Api\V1\PerangkatDaerahController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('auth.login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
        Route::get('auth/me', [AuthController::class, 'me'])->name('auth.me');

        Route::apiResource('perangkat-daerah', PerangkatDaerahController::class)->parameters(['perangkat-daerah' => 'perangkatDaerah']);
        Route::apiResource('pegawai', PegawaiController::class);
        Route::apiResource('kendaraan', KendaraanController::class);
        Route::apiResource('penjadwalan-service', PenjadwalanServiceController::class)->parameters(['penjadwalan-service' => 'penjadwalanService']);

        Route::get('monitoring/ringkasan', [MonitoringController::class, 'ringkasan'])->name('monitoring.ringkasan');
        Route::get('monitoring/kendaraan', [MonitoringController::class, 'index'])->name('monitoring.kendaraan');
        Route::get('kendaraan/{kendaraan}/notifikasi-service', [NotifikasiServiceController::class, 'index'])->name('kendaraan.notifikasi-service.index');
        Route::post('kendaraan/{kendaraan}/notifikasi-service', [NotifikasiServiceController::class, 'store'])->name('kendaraan.notifikasi-service.store');

        Route::apiResource('users', UserController::class)->middleware('can:admin');
    });
});
