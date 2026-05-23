<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NotifikasiResource\Pages;
use App\Models\Notifikasi;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotifikasiResource extends Resource
{
    protected static ?string $model = Notifikasi::class;
    protected static ?string $pluralModelLabel = 'Notifikasi';
    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    protected static ?string $navigationLabel = 'Notifikasi';
    protected static ?string $navigationGroup = 'Konten';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Kirim Notifikasi')
                ->description('Buat notifikasi baru untuk dikirim ke nasabah')
                ->schema([
                    Forms\Components\Select::make('id_pengguna')
                        ->label('Nasabah Tujuan')
                        ->options(
                            User::where('role', 'nasabah')
                                ->get()
                                ->pluck('nama', 'id')
                        )
                        ->required()
                        ->searchable()
                        ->native(false)
                        ->helperText('Pilih nasabah yang akan menerima notifikasi ini'),

                    Forms\Components\TextInput::make('judul')
                        ->required()
                        ->maxLength(255)
                        ->label('Judul Notifikasi')
                        ->placeholder('Contoh: Setor sampah berhasil!')
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('pesan')
                        ->required()
                        ->label('Isi Pesan')
                        ->rows(4)
                        ->placeholder('Tulis pesan notifikasi untuk nasabah...')
                        ->columnSpanFull(),

                    Forms\Components\Select::make('tipe')
                        ->options([
                            'transaksi' => '💰 Transaksi',
                            'misi'      => '🎯 Misi',
                            'saldo'     => '💳 Saldo',
                            'sistem'    => '⚙️ Sistem',
                        ])
                        ->default('sistem')
                        ->required()
                        ->label('Tipe Notifikasi')
                        ->native(false),

                    Forms\Components\Select::make('status_notifikasi')
                        ->options([
                            'belum_dibaca' => 'Belum Dibaca',
                            'dibaca'       => 'Sudah Dibaca',
                        ])
                        ->default('belum_dibaca')
                        ->required()
                        ->label('Status')
                        ->native(false),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pengguna.nama')
                    ->label('Nasabah')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'transaksi' => 'success',
                        'misi'      => 'warning',
                        'saldo'     => 'info',
                        'sistem'    => 'gray',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status_notifikasi')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'belum_dibaca' => 'danger',
                        'dibaca'       => 'success',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'belum_dibaca' => 'Belum Dibaca',
                        'dibaca'       => 'Sudah Dibaca',
                        default        => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('tipe')
                    ->options([
                        'transaksi' => 'Transaksi',
                        'misi'      => 'Misi',
                        'saldo'     => 'Saldo',
                        'sistem'    => 'Sistem',
                    ])
                    ->label('Tipe'),

                Tables\Filters\SelectFilter::make('status_notifikasi')
                    ->options([
                        'belum_dibaca' => 'Belum Dibaca',
                        'dibaca'       => 'Sudah Dibaca',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada notifikasi')
            ->emptyStateDescription('Klik tombol "New" untuk mengirim notifikasi ke nasabah.')
            ->emptyStateIcon('heroicon-o-bell-alert');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListNotifikasis::route('/'),
            'create' => Pages\CreateNotifikasi::route('/create'),
            'edit'   => Pages\EditNotifikasi::route('/{record}/edit'),
        ];
    }
}
