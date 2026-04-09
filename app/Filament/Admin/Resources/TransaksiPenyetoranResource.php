<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TransaksiPenyetoranResource\Exporters\TransaksiExporter;
use App\Filament\Admin\Resources\TransaksiPenyetoranResource\Pages;
use App\Models\TransaksiPenyetoran;
use Filament\Actions\Exports\ExportAction;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                        'pending' => 'warning', 'diproses' => 'info',
                        'selesai' => 'success', 'dibatalkan' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tgl_setor')->dateTime('d M Y H:i')->label('Tanggal'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan']),
                Tables\Filters\SelectFilter::make('id_bank_sampah')
                    ->label('Bank Sampah')->relationship('bankSampah', 'nama_bank'),
            ])
            ->defaultSort('tgl_setor', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->exporter(TransaksiExporter::class)
                    ->label('Export Excel'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return ['index' => Pages\ListTransaksiPenyetorans::route('/')];
    }
}
