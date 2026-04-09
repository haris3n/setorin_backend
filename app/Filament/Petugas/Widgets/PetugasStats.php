<?php

namespace App\Filament\Petugas\Widgets;

use App\Models\TransaksiPenyetoran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class PetugasStats extends BaseWidget
{
    // Matikan auto-refresh untuk performa
    protected function getPollingInterval(): ?string
    {
        return null;
    }

    protected function getStats(): array
    {
        $petugas = Auth::user()->petugas;
        $bankId = $petugas?->id_bank_sampah;

        // ========== HARI INI ==========
        $transaksiHariIni = TransaksiPenyetoran::query()
            ->where('id_bank_sampah', $bankId)
            ->whereDate('tgl_setor', today())
            ->where('status', 'selesai')
            ->count();

        $beratHariIni = TransaksiPenyetoran::query()
            ->where('id_bank_sampah', $bankId)
            ->whereDate('tgl_setor', today())
            ->where('status', 'selesai')
            ->sum('total_berat_kg');

        $koinHariIni = TransaksiPenyetoran::query()
            ->where('id_bank_sampah', $bankId)
            ->whereDate('tgl_setor', today())
            ->where('status', 'selesai')
            ->sum('total_koin');

        // ========== BULAN INI ==========
        $transaksiBulanIni = TransaksiPenyetoran::query()
            ->where('id_bank_sampah', $bankId)
            ->whereMonth('tgl_setor', now()->month)
            ->whereYear('tgl_setor', now()->year)
            ->where('status', 'selesai')
            ->count();

        $beratBulanIni = TransaksiPenyetoran::query()
            ->where('id_bank_sampah', $bankId)
            ->whereMonth('tgl_setor', now()->month)
            ->whereYear('tgl_setor', now()->year)
            ->where('status', 'selesai')
            ->sum('total_berat_kg');

        $koinBulanIni = TransaksiPenyetoran::query()
            ->where('id_bank_sampah', $bankId)
            ->whereMonth('tgl_setor', now()->month)
            ->whereYear('tgl_setor', now()->year)
            ->where('status', 'selesai')
            ->sum('total_koin');

        // ========== TOTAL ==========
        $totalTransaksiSaya = TransaksiPenyetoran::query()
            ->where('id_petugas', $petugas?->id)
            ->where('status', 'selesai')
            ->count();

        return [
            // ========== STATS HARI INI ==========
            Stat::make('Transaksi Hari Ini', $transaksiHariIni)
                ->description('Total setoran hari ini')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('success'),

            Stat::make('Berat Hari Ini', number_format($beratHariIni, 1, ',', '.') . ' kg')
                ->description('Total timbangan hari ini')
                ->descriptionIcon('heroicon-m-scale')
                ->color('info'),

            Stat::make('Koin Hari Ini', number_format($koinHariIni))
                ->description('Koin diperoleh hari ini')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),

            // ========== STATS BULAN INI ==========
            Stat::make('Transaksi Bulan Ini', $transaksiBulanIni)
                ->description('Total setoran bulan ini')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('Berat Bulan Ini', number_format($beratBulanIni, 1, ',', '.') . ' kg')
                ->description('Total timbangan bulan ini')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),

            Stat::make('Koin Bulan Ini', number_format($koinBulanIni))
                ->description('Koin diperoleh bulan ini')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('warning'),

            // ========== TOTAL ==========
            Stat::make('Total Transaksi Saya', $totalTransaksiSaya)
                ->description('Kontribusi semua waktu')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('primary'),
        ];
    }
}