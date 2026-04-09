<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\NasabahResource\Pages;
use App\Models\Nasabah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class NasabahResource extends Resource
{
    protected static ?string $model = Nasabah::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Daftar Nasabah';

    protected static ?string $pluralModelLabel = 'Daftar Nasabah';

    protected static ?string $modelLabel = 'Nasabah';

    public static function form(Form $form): Form
    {
        // View-only resource - tidak ada form
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        $petugas = Auth::user()->petugas;

        return $table
            ->modifyQueryUsing(fn ($query) =>
                $query->where('id_bank_sampah', $petugas?->id_bank_sampah)
                    ->with(['pengguna', 'transaksi'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pengguna.nama')
                    ->label('Nama Nasabah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pengguna.no_telepon')
                    ->label('No. Telepon')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tgl_bergabung')
                    ->label('Tanggal Bergabung')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaksi_count')
                    ->label('Total Transaksi')
                    ->counts('transaksi')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('pengguna.status_akun')
                    ->label('Status')
                    ->colors([
                        'success' => 'aktif',
                        'warning' => 'pending',
                        'danger' => 'nonaktif',
                    ]),
            ])
            ->defaultSort('tgl_bergabung', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNasabahs::route('/'),
        ];
    }
}