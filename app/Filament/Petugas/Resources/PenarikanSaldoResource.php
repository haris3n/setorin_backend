<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\PenarikanSaldoResource\Pages;
use App\Models\PenarikanSaldo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PenarikanSaldoResource extends Resource
{
    protected static ?string $model = PenarikanSaldo::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Penarikan Saldo';

    protected static ?string $pluralModelLabel = 'Penarikan Saldo';

    protected static ?string $modelLabel = 'Penarikan Saldo';

    public static function form(Form $form): Form
    {
        // View only - petugas tidak bisa edit penarikan
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        $petugas = Auth::user()->petugas;
        $bankId = $petugas?->id_bank_sampah;

        return $table
            ->modifyQueryUsing(fn ($query) =>
                $query->whereHas('pengguna.nasabah', function ($q) use ($bankId) {
                    $q->where('id_bank_sampah', $bankId);
                })
                ->with(['pengguna', 'saldo'])
                ->orderBy('created_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pengguna.nama')
                    ->label('Nasabah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_tarik')
                    ->label('Jumlah Tarik')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('metode_bayar')
                    ->label('Metode')
                    ->badge(),

                Tables\Columns\TextColumn::make('no_rekening')
                    ->label('No. Rekening')
                    ->copyable(),

                Tables\Columns\TextColumn::make('tgl_penarikan')
                    ->label('Tanggal')
                    ->date('d M Y'),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'disetujui',
                        'danger' => 'ditolak',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenarikanSaldos::route('/'),
        ];
    }
}