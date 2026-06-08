<?php
namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PenarikanSaldoResource\Pages;
use App\Helpers\AdminActivityLogger;
use App\Models\PenarikanSaldo;
use App\Models\Saldo;
use App\Models\Notifikasi;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class PenarikanSaldoResource extends Resource
{
    protected static ?string $model = PenarikanSaldo::class;
    protected static ?string $pluralModelLabel = 'Penarikan Saldo';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Penarikan Saldo';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }
    public static function getNavigationBadgeColor(): string|array|null { return 'warning'; }

    public static function form(Form $form): Form { return $form->schema([]); }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pengguna.nama')->label('Nasabah')->searchable(),
                Tables\Columns\TextColumn::make('pengguna.no_telepon')->label('No. HP'),
                Tables\Columns\TextColumn::make('jumlah_tarik')->money('IDR')->label('Jumlah')->sortable(),
                Tables\Columns\TextColumn::make('metode_bayar')->label('Metode'),
                Tables\Columns\TextColumn::make('no_rekening')->label('No. Rekening'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning', 'disetujui' => 'success', 'ditolak' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tgl_penarikan')->dateTime('d M Y H:i')->label('Tanggal'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak']),
            ])
            ->defaultSort('tgl_penarikan', 'desc')
            ->actions([
                Tables\Actions\Action::make('setujui')->label('Setujui')
                    ->color('success')->icon('heroicon-o-check-circle')->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        try {
                            DB::transaction(function () use ($record) {
                                $record->update(['status' => 'disetujui']);

                                // Kurangi saldo_tertahan (dana keluar permanen)
                                $saldo = Saldo::find($record->id_saldo);
                                if ($saldo) {
                                    $saldo->saldo_tertahan = (double) $saldo->saldo_tertahan - (double) $record->jumlah_tarik;
                                    $saldo->tgl_update = now();
                                    $saldo->save();
                                }

                                Notifikasi::create([
                                    'id_pengguna' => $record->id_pengguna,
                                    'judul'       => 'Penarikan Saldo Disetujui',
                                    'pesan'       => 'Penarikan Rp ' . number_format($record->jumlah_tarik, 0, ',', '.') . ' telah disetujui dan dana dikirim.',
                                    'tipe'        => 'saldo',
                                ]);
                            });

                            Notification::make()
                                ->title('Penarikan Disetujui')
                                ->body('Dana Rp ' . number_format($record->jumlah_tarik, 0, ',', '.') . ' berhasil diproses.')
                                ->success()
                                ->send();

                            AdminActivityLogger::log(
                                'update',
                                'Menyetujui penarikan saldo Rp ' . number_format($record->jumlah_tarik, 0, ',', '.') . ' atas nama ' . ($record->pengguna?->nama ?? '-'),
                                'Penarikan Saldo',
                                $record->id
                            );
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Menyetujui')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('tolak')->label('Tolak')
                    ->color('danger')->icon('heroicon-o-x-circle')->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        try {
                            DB::transaction(function () use ($record) {
                                $record->update(['status' => 'ditolak']);

                                // Refund: kembalikan saldo_tertahan ke saldo aktif
                                $saldo = Saldo::find($record->id_saldo);
                                if ($saldo) {
                                    $saldo->saldo_tertahan = (double) $saldo->saldo_tertahan - (double) $record->jumlah_tarik;
                                    $saldo->jumlah_saldo = (double) $saldo->jumlah_saldo + (double) $record->jumlah_tarik;
                                    $saldo->tgl_update = now();
                                    $saldo->save();
                                }

                                Notifikasi::create([
                                    'id_pengguna' => $record->id_pengguna,
                                    'judul'       => 'Penarikan Saldo Ditolak',
                                    'pesan'       => 'Penarikan Rp ' . number_format($record->jumlah_tarik, 0, ',', '.') . ' ditolak. Dana dikembalikan ke saldo Anda.',
                                    'tipe'        => 'saldo',
                                ]);
                            });

                            Notification::make()
                                ->title('Penarikan Ditolak')
                                ->body('Saldo Rp ' . number_format($record->jumlah_tarik, 0, ',', '.') . ' dikembalikan ke nasabah.')
                                ->warning()
                                ->send();

                            AdminActivityLogger::log(
                                'update',
                                'Menolak penarikan saldo Rp ' . number_format($record->jumlah_tarik, 0, ',', '.') . ' atas nama ' . ($record->pengguna?->nama ?? '-'),
                                'Penarikan Saldo',
                                $record->id
                            );
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Menolak')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return ['index' => Pages\ListPenarikanSaldos::route('/')];
    }
}
