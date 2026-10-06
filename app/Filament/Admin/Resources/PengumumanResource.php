<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PengumumanResource\Pages;
use App\Models\Pengumuman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PengumumanResource extends Resource
{
    protected static ?string $model = Pengumuman::class;

    protected static ?string $slug = 'pengumuman';

    protected static ?string $modelLabel = 'Pengumuman';

    protected static ?string $pluralModelLabel = 'Pengumuman';

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Pengumuman')->schema([
                Forms\Components\TextInput::make('judul')
                    ->label('Judul')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\Textarea::make('isi')
                    ->label('Isi Pengumuman')->required()->rows(6)->columnSpanFull(),
                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')->required()->default(now()),
                Forms\Components\DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai')->afterOrEqual('tanggal_mulai')
                    ->helperText('Kosongkan jika tidak ada batas akhir. Tanggal akhir termasuk periode tayang.'),
                Forms\Components\Select::make('status')
                    ->options(Pengumuman::STATUS)->default('draft')->required()
                    ->helperText('Pengumuman dipublikasikan hanya tampil di API pengguna selama periode tayang.'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('judul')->searchable()->limit(60),
            Tables\Columns\TextColumn::make('pengguna.nama')->label('Dibuat oleh')->placeholder('-'),
            Tables\Columns\TextColumn::make('status')->badge()
                ->formatStateUsing(fn (string $state) => Pengumuman::STATUS[$state] ?? $state)
                ->color(fn (string $state) => match ($state) {
                    'published' => 'success',
                    'archived' => 'warning',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('tanggal_mulai')->date('d M Y')->sortable(),
            Tables\Columns\TextColumn::make('tanggal_selesai')->date('d M Y')->placeholder('Tanpa batas')->sortable(),
        ])->defaultSort('id', 'desc')
            ->filters([Tables\Filters\SelectFilter::make('status')->options(Pengumuman::STATUS)])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengumuman::route('/'),
            'create' => Pages\CreatePengumuman::route('/create'),
            'edit' => Pages\EditPengumuman::route('/{record}/edit'),
        ];
    }
}
