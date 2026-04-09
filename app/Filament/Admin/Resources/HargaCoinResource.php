<?php
namespace App\Filament\Admin\Resources;
use App\Filament\Admin\Resources\HargaCoinResource\Pages;
use App\Models\HargaCoin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
class HargaCoinResource extends Resource
{
    protected static ?string $model = HargaCoin::class;
    protected static ?string $pluralModelLabel = 'Harga Coin';
    protected static ?string $navigationIcon = 'heroicon-o-wallet';
    protected static ?string $navigationLabel = 'Harga Coin';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Pengaturan Harga Coin')->schema([
                Forms\Components\TextInput::make('harga_per_coin')
                    ->numeric()->prefix('Rp')->required()->label('Harga per Coin'),
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
                Tables\Columns\TextColumn::make('harga_per_coin')->money('IDR')->label('Harga per Coin')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success', 'nonaktif' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('updated_at')->dateTime('d M Y')->label('Diperbarui')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHargaCoins::route('/'),
            'edit'   => Pages\EditHargaCoin::route('/{record}/edit'),
        ];
    }
}
