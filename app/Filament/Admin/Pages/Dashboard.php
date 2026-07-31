<?php
namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard //test lagi
{
    protected static ?string $navigationIcon  = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title           = 'Dashboard Setor.in';

    public function getWidgets(): array
    {
        return [
            // Baris 1 — Statistik Utama
            \App\Filament\Admin\Widgets\StatsOverview::class,

            // Baris 2 — Grafik Tren
            \App\Filament\Admin\Widgets\TransaksiChart::class,
            \App\Filament\Admin\Widgets\BeratSampahChart::class,

            // Baris 3 — Ringkasan Misi
            \App\Filament\Admin\Widgets\MisiStatsWidget::class,

            // Baris 4 — Tabel Performa Bank Sampah
            \App\Filament\Admin\Widgets\BankSampahStatsWidget::class,

            // Baris 5 — Top Nasabah
            \App\Filament\Admin\Widgets\TopNasabahWidget::class,
        ];
    }
}