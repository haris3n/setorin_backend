<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BankSampahResource\Pages;
use App\Filament\Admin\Resources\BankSampahResource\RelationManagers;
use App\Models\BankSampah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BankSampahResource extends Resource
{
    protected static ?string $model = BankSampah::class;
    protected static ?string $pluralModelLabel = 'Bank Sampah';

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Bank Sampah';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Bank Sampah')
                    ->description('Data utama bank sampah')
                    ->schema([
                        Forms\Components\TextInput::make('nama_bank')
                            ->label('Nama Bank Sampah')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Bank Sampah Hijau Lestari')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->rows(3)
                            ->placeholder('Jl. Contoh No. 123, Kelurahan, Kecamatan, Kota')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('no_telepon')
                            ->label('No. Telepon')
                            ->tel()
                            ->placeholder('08123456789 atau +6281234567890')
                            ->maxLength(20)
                            ->rule('regex:/^(\+62|62|0)[0-9]{9,13}$/')
                            ->helperText('Format: 08xxx atau +62xxx'),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->placeholder('banksampah@example.com')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Logo & Lokasi')
                    ->description('Upload logo dan pilih lokasi di peta')
                    ->schema([
                        Forms\Components\FileUpload::make('logo')
                            ->label('Logo Bank Sampah')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '16:9',
                            ])
                            ->maxSize(2048) // 2MB
                            ->directory('bank-sampah-logos')
                            ->visibility('public')
                            ->helperText('Format: JPG, PNG. Maksimal 2MB')
                            ->columnSpanFull(),

                        \Dotswan\MapPicker\Fields\Map::make('location')
                            ->label('Lokasi Bank Sampah')
                            ->columnSpanFull()
                            ->defaultLocation(latitude: -6.200000, longitude: 106.816666) // Default Jakarta
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $set('latitude', $state['lat']);
                                $set('longitude', $state['lng']);
                            })
                            ->afterStateHydrated(function ($state, $record, callable $set) {
                                if ($record) {
                                    $set('location', [
                                        'lat' => $record->latitude ?? -6.200000,
                                        'lng' => $record->longitude ?? 106.816666,
                                    ]);
                                }
                            })
                            ->extraStyles([
                                'min-height: 400px',
                                'border-radius: 12px',
                            ])
                            ->liveLocation(true, true, 5000) // Update setiap 5 detik jika user gerak
                            ->showMarker()
                            ->markerColor('#16A34A')
                            ->showFullscreenControl()
                            ->showZoomControl()
                            ->draggable()
                            ->tilesUrl('https://tile.openstreetmap.de/{z}/{x}/{y}.png')
                            ->zoom(15)
                            ->detectRetina()
                            ->showMyLocationButton()
                            ->extraTileControl([])
                            ->extraControl([
                                'zoomDelta'           => 1,
                                'zoomSnap'            => 0.5,
                                'wheelPxPerZoomLevel' => 60
                            ])
                            ->helperText('🗺️ Klik di peta untuk menentukan lokasi. Marker bisa di-drag untuk adjust posisi. Klik tombol "My Location" untuk gunakan lokasi Anda saat ini.'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Terisi otomatis dari peta'),

                                Forms\Components\TextInput::make('longitude')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Terisi otomatis dari peta'),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('status')
                            ->label('Status Aktif')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true)
                            ->inline(false)
                            ->helperText('Aktifkan untuk membuat bank sampah dapat beroperasi')
                            ->formatStateUsing(fn ($state) => $state === 'aktif')
                            ->dehydrateStateUsing(fn ($state) => $state ? 'aktif' : 'nonaktif'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-bank-sampah.png'))
                    ->size(50),

                Tables\Columns\TextColumn::make('nama_bank')
                    ->label('Nama Bank Sampah')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (BankSampah $record): string => $record->alamat)
                    ->wrap(),

                Tables\Columns\TextColumn::make('no_telepon')
                    ->label('Telepon')
                    ->icon('heroicon-m-phone')
                    ->copyable()
                    ->copyMessage('Nomor telepon disalin!')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->copyMessage('Email disalin!')
                    ->searchable(),

                Tables\Columns\IconColumn::make('status')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn (BankSampah $record): bool => $record->status === 'aktif'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada Bank Sampah')
            ->emptyStateDescription('Klik tombol "New" untuk menambahkan bank sampah pertama.')
            ->emptyStateIcon('heroicon-o-building-office-2');
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
            'index' => Pages\ListBankSampahs::route('/'),
            'create' => Pages\CreateBankSampah::route('/create'),
            'edit' => Pages\EditBankSampah::route('/{record}/edit'),
        ];
    }
}
