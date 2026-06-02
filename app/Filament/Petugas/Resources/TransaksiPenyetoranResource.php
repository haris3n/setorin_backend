<?php

namespace App\Filament\Petugas\Resources;

use App\Filament\Petugas\Resources\TransaksiPenyetoranResource\Pages;
use App\Helpers\NasabahQrCode;
use App\Models\{TransaksiPenyetoran, HargaSampah, Nasabah, User};
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
            Forms\Components\Section::make('Identifikasi & Data Nasabah')
                ->description('Wajib melakukan scan QR Code kartu nasabah terlebih dahulu.')
                ->schema([
                    // Scanner Widget custom
                    Forms\Components\View::make('filament.components.qr-scanner')
                        ->columnSpanFull(),

                    // Hidden scan_code to catch scanned text and trigger lookup
                    Forms\Components\Hidden::make('scan_code')
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (empty($state)) return;

                            // QR resmi: SETORIN:NASABAH:{id} — fallback: no telepon / id numerik
                            $user = null;
                            $qrUserId = NasabahQrCode::parseUserId($state);
                            if ($qrUserId) {
                                $user = User::where('role', 'nasabah')->where('id', $qrUserId)->first();
                            }
                            if (! $user) {
                                $user = User::where('role', 'nasabah')
                                    ->where(function ($query) use ($state) {
                                        $query->where('no_telepon', $state)
                                              ->orWhere('id', $state);
                                    })
                                    ->first();
                            }

                            if ($user && $user->nasabah) {
                                $set('id_nasabah', $user->nasabah->id);
                                $set('nasabah_nama', $user->nama);
                                $set('nasabah_no_telepon', $user->no_telepon);
                                
                                // Bersihkan input scan_code agar siap untuk scan berikutnya jika perlu
                                $set('scan_code', null);
                            }
                        }),

                    // Hidden id_nasabah to store selected customer
                    Forms\Components\Hidden::make('id_nasabah')
                        ->required(),

                    Forms\Components\TextInput::make('nasabah_nama')
                        ->label('Nama Lengkap Nasabah')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Akan terisi otomatis setelah di-scan')
                        ->afterStateHydrated(function ($state, callable $set, $record) {
                            if ($record && $record->nasabah && $record->nasabah->pengguna) {
                                $set('nasabah_nama', $record->nasabah->pengguna->nama);
                            }
                        }),

                    Forms\Components\TextInput::make('nasabah_no_telepon')
                        ->label('No. Handphone Nasabah')
                        ->disabled()
                        ->dehydrated(false)
                        ->placeholder('Akan terisi otomatis setelah di-scan')
                        ->afterStateHydrated(function ($state, callable $set, $record) {
                            if ($record && $record->nasabah && $record->nasabah->pengguna) {
                                $set('nasabah_no_telepon', $record->nasabah->pengguna->no_telepon);
                            }
                        }),

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

                Tables\Columns\TextColumn::make('nasabah.pengguna.no_telepon')
                    ->label('No. Handphone')
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
                        'pending'     => 'warning',
                        'diproses'    => 'info',
                        'selesai'     => 'success',
                        'dibatalkan'  => 'danger',
                        default       => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'diproses' => 'Menunggu Petugas',
                        'selesai'  => 'Selesai',
                        'pending'  => 'Menunggu Nasabah',
                        'dibatalkan' => 'Dibatalkan',
                        default => $state,
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
                        'pending' => 'Menunggu Nasabah',
                        'diproses' => 'Menunggu Petugas',
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
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status !== 'selesai' && $record->status !== 'dibatalkan')
                    ->tooltip('Hanya transaksi yang belum selesai yang dapat diedit'),
                Tables\Actions\Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'diproses')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Transaksi')
                    ->modalDescription('Apakah Anda yakin ingin mengonfirmasi transaksi ini? Koin akan ditambahkan ke akun nasabah.')
                    ->action(function ($record) {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($record) {
                            $record->update(['status' => 'selesai']);

                            $totalKoin = (int) $record->total_koin;
                            $totalBerat = (float) $record->total_berat_kg;

                            \App\Models\Koin::create([
                                'id_pengguna' => $record->nasabah->id_pengguna,
                                'jumlah_koin' => $totalKoin,
                                'sumber'      => 'transaksi',
                            ]);

                            \App\Models\Notifikasi::where('id_pengguna', $record->nasabah->id_pengguna)
                                ->where('id_transaksi', $record->id)
                                ->where('memerlukan_konfirmasi', true)
                                ->update([
                                    'memerlukan_konfirmasi' => false,
                                    'status_notifikasi'     => 'dibaca',
                                ]);

                            \App\Models\Notifikasi::create([
                                'id_pengguna'           => $record->nasabah->id_pengguna,
                                'id_transaksi'          => $record->id,
                                'judul'                 => 'Setoran Berhasil Dikonfirmasi (oleh Petugas)',
                                'pesan'                 => "Anda mendapatkan {$totalKoin} koin dari setoran seberat {$totalBerat} kg yang dikonfirmasi petugas.",
                                'tipe'                  => 'transaksi',
                                'status_notifikasi'     => 'belum_dibaca',
                                'memerlukan_konfirmasi' => false,
                            ]);
                        });
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Transaksi Selesai')
                            ->body('Koin telah berhasil ditambahkan ke akun nasabah.')
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTransaksiPenyetorans::route('/'),
            'create' => Pages\CreateTransaksiPenyetoran::route('/create'),
            'edit'   => Pages\EditTransaksiPenyetoran::route('/{record}/edit'),
        ];
    }
}