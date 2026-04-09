<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\TransaksiPenyetoranResource\Pages;
use App\Models\{TransaksiPenyetoran, HargaSampah, Nasabah};
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TransaksiPenyetoranResource extends Resource
{
    protected static ?string $model = TransaksiPenyetoran::class;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';

    // Label di Sidebar
    protected static ?string $navigationLabel = 'Transaksi';

    // Label Judul Halaman (Menghilangkan akhiran 's')
    protected static ?string $pluralModelLabel = 'Transaksi Penyetoran';

    // Label untuk tombol Tambah/Edit
    protected static ?string $modelLabel = 'Transaksi Penyetoran';

    protected static ?string $slug = 'input-transaksi';

    public static function form(Form $form): Form
    {
        // Ambil data petugas yang sedang login
        $petugas = Auth::user()->petugas;
        $idBankSampah = $petugas?->id_bank_sampah;

        return $form->schema([
            Forms\Components\Section::make('Data Nasabah')
                ->description('Pilih nasabah yang terdaftar di bank sampah Anda.')
                ->schema([
                    Forms\Components\Select::make('id_nasabah')
                        ->label('Nasabah')
                        ->options(
                            Nasabah::where('id_bank_sampah', $idBankSampah)
                                ->with('pengguna')
                                ->get()
                                ->pluck('pengguna.nama', 'id')
                        )
                        ->required()
                        ->searchable()
                        ->preload(),

                    // Hidden fields untuk otomatisasi data
                    Forms\Components\Hidden::make('id_bank_sampah')
                        ->default($idBankSampah),
                    
                    Forms\Components\Hidden::make('id_petugas')
                        ->default($petugas?->id),

                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan Tambahan')
                        ->placeholder('Contoh: Sampah plastik sudah dibersihkan')
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Detail Sampah')
                ->description('Masukkan jenis sampah dan beratnya.')
                ->schema([
                    Forms\Components\Repeater::make('detail')
                        ->label('Rincian Sampah')
                        ->schema([
                            Forms\Components\Select::make('id_harga_sampah')
                                ->label('Jenis Sampah')
                                ->options(
                                    HargaSampah::where('id_bank_sampah', $idBankSampah)
                                        ->where('status', 'aktif')
                                        ->get()
                                        ->pluck('jenis_sampah', 'id')
                                )
                                ->required()
                                ->reactive()
                                ->distinct() // Mencegah jenis sampah yang sama diinput dua kali
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems(),

                            Forms\Components\TextInput::make('berat_kg')
                                ->label('Berat')
                                ->numeric()
                                ->required()
                                ->suffix('kg')
                                ->minValue(0.1),
                        ])
                        ->columns(2)
                        ->minItems(1)
                        ->addActionLabel('Tambah Jenis Sampah')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $petugas = Auth::user()->petugas;

        return $table
            // Filter agar petugas melihat SEMUA transaksi di bank sampah mereka (bukan hanya yang mereka buat)
            ->modifyQueryUsing(fn ($query) =>
                $query->where('id_bank_sampah', $petugas?->id_bank_sampah)
                      ->with(['nasabah.pengguna', 'detail.hargaSampah', 'petugas.pengguna'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nasabah.pengguna.nama')
                    ->label('Nasabah')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_berat_kg')
                    ->label('Total Berat')
                    ->suffix(' kg')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_koin')
                    ->label('Koin Didapat')
                    ->suffix(' koin')
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'    => 'warning',
                        'selesai'    => 'success',
                        'dibatalkan' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('tgl_setor')
                    ->label('Waktu Transaksi')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('petugas.pengguna.nama')
                    ->label('Petugas')
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),

                Tables\Filters\Filter::make('tgl_setor')
                    ->label('Tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')->label('Dari'),
                        Forms\Components\DatePicker::make('sampai_tanggal')->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari_tanggal'], fn ($q) => $q->whereDate('tgl_setor', '>=', $data['dari_tanggal']))
                            ->when($data['sampai_tanggal'], fn ($q) => $q->whereDate('tgl_setor', '<=', $data['sampai_tanggal']));
                    }),
            ])
            ->defaultSort('tgl_setor', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksiPenyetorans::route('/'),
            'create' => Pages\CreateTransaksiPenyetoran::route('/create'),
        ];
    }
}