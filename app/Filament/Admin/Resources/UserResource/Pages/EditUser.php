<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Helpers\AdminActivityLogger;
use App\Models\Nasabah;
use App\Models\Petugas;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    Log::info('EditUser: DeleteAction triggered', [
                        'record_id' => $this->record->id,
                        'record_nama' => $this->record->nama,
                    ]);

                    try {
                        AdminActivityLogger::delete(
                            'User',
                            $this->record->id,
                            $this->record->nama . ' (' . ucfirst($this->record->role) . ')'
                        );
                        Log::info('EditUser: Delete logged successfully');
                    } catch (\Exception $e) {
                        Log::error('EditUser: Failed to log delete', [
                            'error' => $e->getMessage(),
                        ]);
                    }
                }),
        ];
    }

    /**
     * Hydrate id_bank_sampah dari tabel petugas/nasabah ke form
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record;

        if ($record->role === 'petugas' && $record->petugas) {
            $data['id_bank_sampah'] = $record->petugas->id_bank_sampah;
        } elseif ($record->role === 'nasabah' && $record->nasabah) {
            $data['id_bank_sampah'] = $record->nasabah->id_bank_sampah;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;
        $bankSampahId = $this->data['id_bank_sampah'] ?? null;

        if ($record->role === 'petugas' && $bankSampahId) {
            Petugas::updateOrCreate(
                ['id_pengguna' => $record->id],
                ['id_bank_sampah' => $bankSampahId]
            );
        }

        if ($record->role === 'nasabah' && $bankSampahId) {
            Nasabah::updateOrCreate(
                ['id_pengguna' => $record->id],
                ['id_bank_sampah' => $bankSampahId]
            );
        }

        // Log aktivitas
        Log::info('EditUser: afterSave() triggered', [
            'record_id' => $record->id,
            'record_nama' => $record->nama,
        ]);

        try {
            AdminActivityLogger::update(
                'User',
                $record->id,
                $record->nama . ' (' . ucfirst($record->role) . ')'
            );
            Log::info('EditUser: Update logged successfully');
        } catch (\Exception $e) {
            Log::error('EditUser: Failed to log update', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
