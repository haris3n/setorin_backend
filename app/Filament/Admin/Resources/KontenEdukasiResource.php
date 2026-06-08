<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\KontenEdukasiResource\Pages;
use App\Models\KontenEdukasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class KontenEdukasiResource extends Resource
{
    protected static ?string $model = KontenEdukasi::class;
    protected static ?string $pluralModelLabel = 'Konten Edukasi';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Konten Edukasi';
    protected static ?string $navigationGroup = 'Konten';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Konten')->schema([
                Forms\Components\Hidden::make('id_pengguna')
                    ->default(fn () => Auth::id())
                    ->dehydrated(true),

                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255)
                    ->label('Judul Artikel')
                    ->columnSpanFull(),

                Forms\Components\Select::make('kategori')
                    ->options([
                        'Kertas'     => 'Kertas',
                        'Plastik'    => 'Plastik',
                        'Kaca'       => 'Kaca',
                        'Organik'    => 'Organik',
                        'Logam'      => 'Logam',
                        'Elektronik' => 'Elektronik',
                    ])
                    ->required()
                    ->label('Kategori Sampah')
                    ->searchable()
                    ->native(false)
                    ->helperText('Pilih kategori yang sesuai dengan filter di aplikasi mobile'),

                Forms\Components\DatePicker::make('tgl_publikasi')
                    ->label('Tanggal Publikasi')
                    ->default(now()),

                Forms\Components\Select::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'archived'  => 'Archived',
                    ])
                    ->default('draft')
                    ->required()
                    ->label('Status')
                    ->native(false),

                // Textarea (bukan RichEditor) agar teks tampil bersih di aplikasi Flutter
                Forms\Components\Textarea::make('isi')
                    ->required()
                    ->label('Isi Artikel')
                    ->rows(15)
                    ->columnSpanFull()
                    ->helperText('Tulis teks biasa. Jangan gunakan format HTML karena akan ditampilkan apa adanya di aplikasi mobile.'),

            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->limit(50)
                    ->label('Judul'),

                Tables\Columns\TextColumn::make('kategori')
                    ->badge()
                    ->label('Kategori'),

                Tables\Columns\TextColumn::make('pengguna.nama')
                    ->label('Dibuat oleh'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'published' => 'success',
                        'archived'  => 'warning',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('tgl_publikasi')
                    ->date('d M Y')
                    ->sortable()
                    ->label('Tgl Publikasi'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'archived'  => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('kategori')
                    ->options([
                        'Kertas'     => 'Kertas',
                        'Plastik'    => 'Plastik',
                        'Kaca'       => 'Kaca',
                        'Organik'    => 'Organik',
                        'Logam'      => 'Logam',
                        'Elektronik' => 'Elektronik',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Quick publish action
                Tables\Actions\Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-globe-alt')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== 'published')
                    ->action(function ($record) {
                        $record->update([
                            'status'        => 'published',
                            'tgl_publikasi' => now(),
                        ]);
                    }),

                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKontenEdukasis::route('/'),
            'create' => Pages\CreateKontenEdukasi::route('/create'),
            'edit'   => Pages\EditKontenEdukasi::route('/{record}/edit'),
        ];
    }
}