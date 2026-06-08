<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TransaksiPenyetoranResource\Pages;
use App\Helpers\AdminActivityLogger;
use App\Models\TransaksiPenyetoran;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TransaksiPenyetoranResource extends Resource
{
    protected static ?string $model = TransaksiPenyetoran::class;
    protected static ?string $pluralModelLabel = 'Transaksi Penyetoran';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form { return $form->schema([]); }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('nasabah.pengguna.nama')->label('Nasabah')->searchable(),
                Tables\Columns\TextColumn::make('bankSampah.nama_bank')->label('Bank Sampah'),
                Tables\Columns\TextColumn::make('petugas.pengguna.nama')->label('Petugas'),
                Tables\Columns\TextColumn::make('total_berat_kg')->suffix(' kg')->label('Berat'),
                Tables\Columns\TextColumn::make('total_koin')->suffix(' koin')->label('Koin'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'    => 'warning',
                        'diproses'   => 'info',
                        'selesai'    => 'success',
                        'dibatalkan' => 'danger',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tgl_setor')->dateTime('d M Y H:i')->label('Tanggal'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'    => 'Pending',
                        'diproses'   => 'Diproses',
                        'selesai'    => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
                Tables\Filters\SelectFilter::make('id_bank_sampah')
                    ->label('Bank Sampah')
                    ->relationship('bankSampah', 'nama_bank'),
            ])
            ->defaultSort('tgl_setor', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_csv')
                    ->label('Export Excel (CSV)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $transaksi = TransaksiPenyetoran::with([
                            'nasabah.pengguna',
                            'bankSampah',
                            'petugas.pengguna',
                        ])
                        ->orderBy('tgl_setor', 'desc')
                        ->get();

                        $filename = 'transaksi_penyetoran_' . now()->format('Ymd_His') . '.csv';

                        // Catat log ekspor ke aktivitas admin
                        $user = Auth::user();
                        if ($user) {
                            AdminActivityLogger::log(
                                'export',
                                'Mengekspor data transaksi penyetoran (' . $transaksi->count() . ' baris)',
                                'Transaksi',
                                null
                            );
                        }

                        return response()->streamDownload(function () use ($transaksi) {
                            $out = fopen('php://output', 'w');

                            // UTF-8 BOM agar Excel membaca karakter Indonesia dengan benar
                            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

                            fputcsv($out, [
                                'ID', 'Tanggal', 'Nasabah', 'Bank Sampah',
                                'Petugas', 'Berat (kg)', 'Koin', 'Status', 'Catatan',
                            ]);

                            foreach ($transaksi as $t) {
                                fputcsv($out, [
                                    $t->id,
                                    $t->tgl_setor?->format('Y-m-d H:i:s') ?? '-',
                                    $t->nasabah?->pengguna?->nama ?? '-',
                                    $t->bankSampah?->nama_bank ?? '-',
                                    $t->petugas?->pengguna?->nama ?? '-',
                                    number_format((float) $t->total_berat_kg, 2, '.', ''),
                                    $t->total_koin,
                                    $t->status,
                                    $t->catatan ?? '-',
                                ]);
                            }

                            fclose($out);
                        }, $filename, [
                            'Content-Type'        => 'text/csv; charset=UTF-8',
                            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                        ]);
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return ['index' => Pages\ListTransaksiPenyetorans::route('/')];
    }
}
