<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\JadwalOperasionalResource\Pages;
use App\Models\JadwalOperasional;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class JadwalOperasionalResource extends Resource
{
    protected static ?string $model = JadwalOperasional::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Jadwal Operasional';

    protected static ?string $pluralModelLabel = 'Jadwal Operasional';
    
    protected static ?string $modelLabel = 'Jadwal Operasional';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pengaturan Jadwal')
                    ->description('Tentukan hari dan jam operasional bank sampah.')
                    ->schema([
                        Forms\Components\Select::make('hari')
                            ->options([
                                'Senin' => 'Senin',
                                'Selasa' => 'Selasa',
                                'Rabu' => 'Rabu',
                                'Kamis' => 'Kamis',
                                'Jumat' => 'Jumat',
                                'Sabtu' => 'Sabtu',
                                'Minggu' => 'Minggu',
                            ])->required(),
                        
                        Forms\Components\TimePicker::make('jam_buka')
                            ->label('Jam Buka')
                            ->required(),

                        Forms\Components\TimePicker::make('jam_tutup')
                            ->label('Jam Tutup')
                            ->required(),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                        
                        // Otomatis set ID Bank Sampah berdasarkan petugas yang login
                        Forms\Components\Hidden::make('id_bank_sampah')
                            ->default(fn () => Auth::user()->petugas?->id_bank_sampah),

                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Filter agar petugas hanya melihat jadwal bank sampah mereka sendiri
            ->modifyQueryUsing(fn ($query) => 
                $query->where('id_bank_sampah', Auth::user()->petugas?->id_bank_sampah)
            )
            ->columns([
                Tables\Columns\TextColumn::make('hari')
                    ->label('Hari')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jam_buka')
                    ->label('Buka')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('jam_tutup')
                    ->label('Tutup')
                    ->time('H:i'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwalOperasionals::route('/'),
            'create' => Pages\CreateJadwalOperasional::route('/create'),
            'edit' => Pages\EditJadwalOperasional::route('/{record}/edit'),
        ];
    }
}