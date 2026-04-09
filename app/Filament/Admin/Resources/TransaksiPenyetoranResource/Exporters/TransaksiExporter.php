<?php

namespace App\Filament\Admin\Resources\TransaksiPenyetoranResource\Exporters;

use App\Models\TransaksiPenyetoran;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransaksiExporter extends Exporter
{
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('nasabah.pengguna.nama')->label('Nasabah'),
            ExportColumn::make('bankSampah.nama_bank')->label('Bank Sampah'),
            ExportColumn::make('petugas.pengguna.nama')->label('Petugas'),
            ExportColumn::make('total_berat_kg')->label('Berat (kg)'),
            ExportColumn::make('total_koin')->label('Koin'),
            ExportColumn::make('status')->label('Status'),
            ExportColumn::make('tgl_setor')->label('Tanggal'),
            ExportColumn::make('catatan')->label('Catatan'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export transaksi selesai. ';

        if ($export->total_rows > 0) {
            $body .= number_format($export->total_rows) . ' transaksi diexport.';
        }

        return $body;
    }

    public function mutateData($data): array
    {
        return $data;
    }
}