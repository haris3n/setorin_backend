<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Helpers\AdminActivityLogger;
use App\Models\Nasabah;
use App\Models\Petugas;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;
        $bankSampahId = $this->data['id_bank_sampah'] ?? null;

        if ($record->role === 'petugas' && $bankSampahId) {
            Petugas::firstOrCreate(
                ['id_pengguna' => $record->id],
                ['id_bank_sampah' => $bankSampahId]
            );
        }

        if ($record->role === 'nasabah' && $bankSampahId) {
            Nasabah::firstOrCreate(
                ['id_pengguna' => $record->id],
                [
                    'id_bank_sampah' => $bankSampahId,
                    'tgl_bergabung' => now()->toDateString(),
                ]
            );
        }

        // Log aktivitas
        Log::info('CreateUser: afterCreate() triggered', [
            'record_id' => $record->id,
            'record_nama' => $record->nama,
            'role' => $record->role,
        ]);

        try {
            AdminActivityLogger::create(
                'User',
                $record->id,
                $record->nama . ' (' . ucfirst($record->role) . ')'
            );
            Log::info('CreateUser: AdminActivityLogger called successfully');
        } catch (\Exception $e) {
            Log::error('CreateUser: Failed to log activity', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
