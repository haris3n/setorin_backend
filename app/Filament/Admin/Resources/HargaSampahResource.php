<?php
namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HargaSampahResource\Pages;
use App\Models\HargaSampah;
use App\Models\BankSampah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HargaSampahResource extends Resource
{
    protected static ?string $model = HargaSampah::class;
    protected static ?string $pluralModelLabel = 'Harga Sampah';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Harga Sampah';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Harga Sampah')->schema([
                Forms\Components\Select::make('id_bank_sampah')
                    ->label('Bank Sampah')
                    ->options(BankSampah::where('status', 'aktif')->pluck('nama_bank', 'id'))
                    ->required()->searchable()->native(false),
                Forms\Components\TextInput::make('jenis_sampah')
                    ->required()->label('Jenis Sampah')->maxLength(255),
                Forms\Components\TextInput::make('harga_per_kg')
                    ->numeric()->prefix('Rp')->required()->label('Harga per Kg'),
                Forms\Components\Select::make('status')
                    ->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'])
                    ->default('aktif')->required()->native(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bankSampah.nama_bank')->label('Bank Sampah')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('jenis_sampah')->searchable()->label('Jenis Sampah'),
                Tables\Columns\TextColumn::make('harga_per_kg')->money('IDR')->label('Harga/Kg')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success', 'nonaktif' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('d M Y')->label('Diperbarui')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_bank_sampah')
                    ->label('Bank Sampah')
                    ->options(BankSampah::pluck('nama_bank', 'id'))->searchable(),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif']),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHargaSampahs::route('/'),
            'create' => Pages\CreateHargaSampah::route('/create'),
            'edit'   => Pages\EditHargaSampah::route('/{record}/edit'),
        ];
    }
}
