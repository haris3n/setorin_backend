<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AktivitasAdminResource\Pages;
use App\Models\AktivitasAdmin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AktivitasAdminResource extends Resource
{
    protected static ?string $model = AktivitasAdmin::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Log Aktivitas';

    protected static ?string $pluralModelLabel = 'Log Aktivitas Admin';

    protected static ?string $modelLabel = 'Log Aktivitas';

    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('pengguna.nama')
                    ->label('Admin')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('jenis_aktivitas')
                    ->label('Aktivitas')
                    ->colors([
                        'success' => 'create',
                        'warning' => 'update',
                        'danger' => 'delete',
                        'info' => 'login',
                        'primary' => 'export',
                    ]),

                Tables\Columns\TextColumn::make('modul')
                    ->label('Modul')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ucfirst($state ?? '-')),

                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis_aktivitas')
                    ->label('Jenis Aktivitas')
                    ->options([
                        'create' => 'Create',
                        'update' => 'Update',
                        'delete' => 'Delete',
                        'login' => 'Login',
                        'export' => 'Export',
                    ]),

                Tables\Filters\SelectFilter::make('modul')
                    ->label('Modul')
                    ->options([
                        'user' => 'User',
                        'bank_sampah' => 'Bank Sampah',
                        'harga_sampah' => 'Harga Sampah',
                        'misi' => 'Misi',
                        'konten_edukasi' => 'Konten Edukasi',
                        'penarikan_saldo' => 'Penarikan Saldo',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAktivitasAdmins::route('/'),
        ];
    }
}