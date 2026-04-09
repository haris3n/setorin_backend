<?php
namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MisiResource\Pages;
use App\Models\Misi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MisiResource extends Resource
{
    protected static ?string $model = Misi::class;
    protected static ?string $pluralModelLabel = 'Misi & Reward';
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Misi & Reward';
    protected static ?string $navigationGroup = 'Konten';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Misi')->schema([
                Forms\Components\TextInput::make('nama_misi')->required()->label('Nama Misi')->maxLength(255),
                Forms\Components\TextInput::make('reward_koin')->numeric()->required()->suffix('koin')->label('Reward Koin'),
                Forms\Components\Textarea::make('deskripsi')->label('Deskripsi')->columnSpanFull(),
                Forms\Components\DatePicker::make('tgl_mulai')->required()->label('Tanggal Mulai'),
                Forms\Components\DatePicker::make('tgl_selesai')->required()->label('Tanggal Selesai')->after('tgl_mulai'),
                Forms\Components\Select::make('status_misi')
                    ->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'])
                    ->default('aktif')->required()->label('Status')->native(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_misi')->searchable()->label('Nama Misi'),
                Tables\Columns\TextColumn::make('reward_koin')->suffix(' koin')->sortable()->label('Reward'),
                Tables\Columns\TextColumn::make('tgl_mulai')->date('d M Y')->label('Mulai'),
                Tables\Columns\TextColumn::make('tgl_selesai')->date('d M Y')->label('Selesai'),
                Tables\Columns\TextColumn::make('klaim_count')->counts('klaim')->label('Total Klaim'),
                Tables\Columns\TextColumn::make('status_misi')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success', 'nonaktif' => 'danger', default => 'gray',
                    })->label('Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle')
                    ->label(fn ($record) => $record->status_misi === 'aktif' ? 'Nonaktifkan' : 'Aktifkan')
                    ->color(fn ($record) => $record->status_misi === 'aktif' ? 'danger' : 'success')
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn ($record) => $record->update([
                        'status_misi' => $record->status_misi === 'aktif' ? 'nonaktif' : 'aktif'
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMisis::route('/'),
            'create' => Pages\CreateMisi::route('/create'),
            'edit'   => Pages\EditMisi::route('/{record}/edit'),
        ];
    }
}
