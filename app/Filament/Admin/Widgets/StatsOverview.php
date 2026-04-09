<?php

namespace App\Filament\Admin\Widgets;

use App\Models\{User, BankSampah, TransaksiPenyetoran, PenarikanSaldo};
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    // Matikan auto-refresh untuk performa
    protected function getPollingInterval(): ?string
    {
        return null;
    }

    protected function getStats(): array
    {
        // Hitung total berat sampah dari transaksi yang sudah selesai
        $totalBerat = TransaksiPenyetoran::where('status', 'selesai')->sum('total_berat_kg');
        
        // Hitung jumlah penarikan yang menunggu persetujuan
        $pendingPenarikan = PenarikanSaldo::where('status', 'pending')->count();

        return [
            // Statistik Nasabah
            Stat::make('Total Nasabah', User::where('role', 'nasabah')->count())
                ->description('Pengguna terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), // Contoh chart mini

            // Statistik Bank Sampah
            Stat::make('Total Bank Sampah', BankSampah::where('status', 'aktif')->count())
                ->description('Unit bank aktif')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            // Statistik Transaksi Bulanan
            Stat::make('Transaksi Bulan Ini', 
                TransaksiPenyetoran::whereMonth('tgl_setor', now()->month)
                    ->whereYear('tgl_setor', now()->year)
                    ->where('status', 'selesai')
                    ->count()
            )
                ->description('Setoran berhasil')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary'),

            // Statistik Berat Sampah
            Stat::make('Total Sampah Terkumpul', number_format($totalBerat, 1, ',', '.') . ' kg')
                ->description('Akumulasi berat global')
                ->descriptionIcon('heroicon-m-scale')
                ->color('warning'),

            // Statistik Penarikan Saldo
            Stat::make('Penarikan Pending', $pendingPenarikan)
                ->description($pendingPenarikan > 0 ? 'Perlu tindakan segera' : 'Semua diproses')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($pendingPenarikan > 0 ? 'danger' : 'success')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    // Jika diklik akan langsung menuju halaman penarikan
                    'onclick' => "window.location.href='/admin/penarikan-saldo'",
                ]),

            // Statistik Petugas
            Stat::make('Total Petugas', User::where('role', 'petugas')->count())
                ->description('Petugas lapangan')
                ->descriptionIcon('heroicon-m-identification')
                ->color('gray'),
        ];
    }
}