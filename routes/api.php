<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Nasabah\{ProfilController, TransaksiController, SaldoController, MisiController};
use App\Http\Controllers\Api\Petugas\{TransaksiPetugasController, JadwalController};
use App\Http\Controllers\Api\Admin\{PenggunaController, BankSampahController, HargaSampahController, MisiAdminController, PenarikanController, LaporanController};
use App\Http\Controllers\Api\Admin\KontenEdukasiController;

/*
|--------------------------------------------------------------------------
| API Routes - Setor.in
|--------------------------------------------------------------------------
*/

// ============================================================
// PUBLIC — Tidak Butuh Login (dengan rate limiting)
// ============================================================
Route::middleware('throttle:register')->post('/register',   [AuthController::class, 'register']);
Route::middleware('throttle:otp')->post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::middleware('throttle:login')->post('/login',      [AuthController::class, 'login']);

// ============================================================
// PROTECTED — Wajib Login (Sanctum) dengan rate limiting
// ============================================================
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // ── NASABAH ──────────────────────────────────────────────
    Route::middleware('role:nasabah')->prefix('nasabah')->group(function () {
        Route::get('/profil',               [ProfilController::class, 'show']);
        Route::put('/profil',               [ProfilController::class, 'update']);
        Route::get('/notifikasi',           [ProfilController::class, 'notifikasi']);
        Route::get('/edukasi',              [ProfilController::class, 'edukasi']);

        Route::get('/bank-sampah',          [TransaksiController::class, 'listBankSampah']);
        Route::post('/bank-sampah/pilih',   [TransaksiController::class, 'pilihBankSampah']);
        Route::get('/transaksi',            [TransaksiController::class, 'riwayat']);
        Route::post('/laporan-sampah',      [TransaksiController::class, 'laporkanSampah']);

        Route::get('/saldo',                [SaldoController::class, 'show']);
        Route::post('/saldo/tukar-koin',    [SaldoController::class, 'tukarKoin']);
        Route::post('/saldo/tarik',         [SaldoController::class, 'ajukanPenarikan']);

        Route::get('/misi',                 [MisiController::class, 'index']);
        Route::post('/misi/{id}/klaim',     [MisiController::class, 'klaim']);
    });

    // ── PETUGAS ──────────────────────────────────────────────
    Route::middleware('role:petugas')->prefix('petugas')->group(function () {
        Route::get('/transaksi',              [TransaksiPetugasController::class, 'index']);
        Route::post('/transaksi',             [TransaksiPetugasController::class, 'store']);
        Route::put('/transaksi/{id}/konfirmasi', [TransaksiPetugasController::class, 'konfirmasi']);

        Route::get('/jadwal',                 [JadwalController::class, 'index']);
        Route::put('/jadwal/{id}',            [JadwalController::class, 'update']);
        Route::get('/laporan',                [JadwalController::class, 'laporan']);
    });

    // ── ADMIN ─────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::apiResource('/pengguna',      PenggunaController::class);
        Route::apiResource('/bank-sampah',   BankSampahController::class);
        Route::apiResource('/harga-sampah',  HargaSampahController::class);
        Route::apiResource('/misi',          MisiAdminController::class);

        Route::get('/penarikan',             [PenarikanController::class, 'index']);
        Route::put('/penarikan/{id}/setujui', [PenarikanController::class, 'setujui']);
        Route::put('/penarikan/{id}/tolak',   [PenarikanController::class, 'tolak']);

        Route::get('/laporan',               [LaporanController::class, 'index']);
        Route::get('/laporan/export',         [LaporanController::class, 'export']);
        Route::get('/laporan/export-detail',  [LaporanController::class, 'exportDetail']);
    });
});

// di dalam Route::middleware('role:admin')->prefix('admin')
Route::apiResource('/konten-edukasi', KontenEdukasiController::class);
Route::put('/konten-edukasi/{id}/publish', [KontenEdukasiController::class, 'publish']);
Route::put('/konten-edukasi/{id}/archive', [KontenEdukasiController::class, 'archive']);