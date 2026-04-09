<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\AktivitasPetugasResource\Pages;
use App\Models\AktivitasPetugas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AktivitasPetugasResource extends Resource
{
    protected static ?string $model = AktivitasPetugas::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Riwayat Aktivitas';

    protected static ?string $pluralModelLabel = 'Riwayat Aktivitas';

    protected static ?string $modelLabel = 'Aktivitas';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        $petugas = Auth::user()->petugas;

        return $table
            ->modifyQueryUsing(fn ($query) =>
                $query->where('id_petugas', $petugas?->id)
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('jenis_aktivitas')
                    ->label('Jenis Aktivitas')
                    ->colors([
                        'success' => 'transaksi_baru',
                        'info' => 'jadwal_diubah',
                        'warning' => 'profil_diubah',
                        'primary' => 'login',
                    ]),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50),
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
            'index' => Pages\ListAktivitasPetugas::route('/'),
        ];
    }
}