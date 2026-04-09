<?php

namespace App\Filament\Admin\Widgets;

use App\Models\TransaksiPenyetoran;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransaksiChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Transaksi (7 Hari Terakhir)';

    protected static ?int $sort = 2;

    // Menentukan tinggi chart agar proporsional
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Optimasi: Ambil data sekaligus dalam satu query untuk 7 hari terakhir
        $transaksiPerHari = TransaksiPenyetoran::where('status', 'selesai')
            ->where('tgl_setor', '>=', now()->subDays(6)->startOfDay())
            ->select(
                DB::raw('DATE(tgl_setor) as date'),
                DB::raw('count(*) as aggregate')
            )
            ->groupBy('date')
            ->pluck('aggregate', 'date');

        // Mapping data ke label 7 hari terakhir
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->translatedFormat('d M');
            
            // Jika hari tersebut tidak ada transaksi, beri nilai 0
            $data[] = $transaksiPerHari->get($date, 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Transaksi Selesai',
                    'data' => $data,
                    'fill' => 'start',
                    'tension' => 0.4, // Membuat garis sedikit melengkung (smooth line)
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'borderColor' => '#22c55e',
                    'borderWidth' => 3,
                    'pointBackgroundColor' => '#22c55e',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    // Matikan auto-refresh untuk performa
    protected function getPollingInterval(): ?string
    {
        return null;
    }
}