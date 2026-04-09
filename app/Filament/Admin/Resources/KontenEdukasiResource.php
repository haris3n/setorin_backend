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
                Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255)
                    ->label('Judul Artikel')
                    ->columnSpanFull(),
                
                Forms\Components\Select::make('kategori')
                    ->options([
                        'sampah_organik'   => 'Sampah Organik',
                        'sampah_anorganik' => 'Sampah Anorganik',
                        'daur_ulang'       => 'Daur Ulang',
                        'lingkungan'       => 'Lingkungan Hidup',
                        'tips_praktis'     => 'Tips Praktis',
                    ])
                    ->label('Kategori')
                    ->searchable()
                    ->native(false),

                Forms\Components\DatePicker::make('tgl_publikasi')
                    ->label('Tanggal Publikasi')
                    ->default(now()),

                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft', 
                        'published' => 'Published', 
                        'archived' => 'Archived'
                    ])
                    ->default('draft')
                    ->required()
                    ->label('Status')
                    ->native(false),

                Forms\Components\RichEditor::make('isi')
                    ->required()
                    ->label('Isi Artikel')
                    ->columnSpanFull(),
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

                // Pastikan relasi di model bernama 'pengguna' atau 'user'
                Tables\Columns\TextColumn::make('pengguna.nama')
                    ->label('Dibuat oleh'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray', 
                        'published' => 'success', 
                        'archived' => 'warning', 
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('tgl_publikasi')
                    ->date('d M Y')
                    ->sortable()
                    ->label('Tgl Publikasi'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft', 
                        'published' => 'Published', 
                        'archived' => 'Archived'
                    ]),
                Tables\Filters\SelectFilter::make('kategori')
                    ->options([
                        'sampah_organik' => 'Sampah Organik', 
                        'sampah_anorganik' => 'Sampah Anorganik',
                        'daur_ulang' => 'Daur Ulang', 
                        'lingkungan' => 'Lingkungan Hidup', 
                        'tips_praktis' => 'Tips Praktis',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
                // Action untuk mempublikasikan artikel dengan cepat
                Tables\Actions\Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-globe-alt')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status !== 'published')
                    ->action(fn ($record) => $record->update([
                        'status' => 'published', 
                        'tgl_publikasi' => now()
                    ])),

                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            // Perhatikan nama class List di sini (harus sesuai file)
            'index'  => Pages\ListKontenEdukasis::route('/'), 
            'create' => Pages\CreateKontenEdukasi::route('/create'),
            'edit'   => Pages\EditKontenEdukasi::route('/{record}/edit'),
        ];
    }
}