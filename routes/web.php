<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Models\{Nasabah, BankSampah, TransaksiPenyetoran, DetailTransaksiSampah, HargaSampah, KontenEdukasi};

Route::get('/', function () {
    $stats = [
        'total_nasabah' => 1480,
        'total_bank_sampah' => 45,
        'total_transaksi' => 3890,
        'total_sampah_kg' => 18450,
        'total_saldo_dicairkan' => '54.200.000',
    ];

    $hargaSampah = [];
    $edukasiList = [];

    try {
        if (Schema::hasTable('nasabah')) {
            $countNasabah = Nasabah::count();
            if ($countNasabah > 0) $stats['total_nasabah'] = $countNasabah;
        }
        if (Schema::hasTable('bank_sampah')) {
            $countBank = BankSampah::count();
            if ($countBank > 0) $stats['total_bank_sampah'] = $countBank;
        }
        if (Schema::hasTable('transaksi_penyetoran')) {
            $countTx = TransaksiPenyetoran::count();
            if ($countTx > 0) $stats['total_transaksi'] = $countTx;
        }
        if (Schema::hasTable('detail_transaksi_sampah')) {
            $sumKg = DetailTransaksiSampah::sum('berat_kg');
            if ($sumKg > 0) $stats['total_sampah_kg'] = round($sumKg);
        }
        if (Schema::hasTable('harga_sampah')) {
            $hargaSampah = HargaSampah::where('status', 'aktif')->take(6)->get();
        }
        if (Schema::hasTable('konten_edukasi')) {
            $edukasiList = KontenEdukasi::where('status', 'published')->latest()->take(3)->get();
        }
    } catch (\Throwable $e) {
        // Fallback gracefully if database or tables are not initialized yet
    }

    return view('welcome', compact('stats', 'hargaSampah', 'edukasiList'));
});

