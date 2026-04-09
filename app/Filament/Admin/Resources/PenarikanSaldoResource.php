<?php
namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PenarikanSaldoResource\Pages;
use App\Models\PenarikanSaldo;
use App\Models\Saldo;
use App\Models\Notifikasi;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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
                        DB::transaction(function () use ($record) {
                            $record->update(['status' => 'disetujui']);
                            Saldo::find($record->id_saldo)->decrement('jumlah_saldo', $record->jumlah_tarik);
                            Notifikasi::create([
                                'id_pengguna' => $record->id_pengguna,
                                'judul'       => 'Penarikan Saldo Disetujui',
                                'pesan'       => 'Penarikan Rp ' . number_format($record->jumlah_tarik) . ' telah disetujui.',
                                'tipe'        => 'saldo',
                            ]);
                        });
                    }),
                Tables\Actions\Action::make('tolak')->label('Tolak')
                    ->color('danger')->icon('heroicon-o-x-circle')->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update(['status' => 'ditolak']);
                        Notifikasi::create([
                            'id_pengguna' => $record->id_pengguna,
                            'judul'       => 'Penarikan Saldo Ditolak',
                            'pesan'       => 'Penarikan Rp ' . number_format($record->jumlah_tarik) . ' ditolak.',
                            'tipe'        => 'saldo',
                        ]);
                    }),
            ]);
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return ['index' => Pages\ListPenarikanSaldos::route('/')];
    }
}
