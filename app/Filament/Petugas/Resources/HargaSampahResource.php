<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\HargaSampahResource\Pages;
use App\Models\HargaSampah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class HargaSampahResource extends Resource
{
    protected static ?string $model = HargaSampah::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Daftar Harga Sampah';

    protected static ?string $pluralModelLabel = 'Daftar Harga Sampah';

    protected static ?string $modelLabel = 'Harga Sampah';

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
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis_sampah')
                    ->label('Jenis Sampah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('harga_per_kg')
                    ->label('Harga per Kg')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'aktif',
                        'danger' => 'nonaktif',
                    ]),
            ])
            ->defaultSort('jenis_sampah', 'asc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHargaSampahs::route('/'),
        ];
    }
}