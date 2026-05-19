<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Models\Nasabah;
use App\Models\Petugas;
use Filament\Resources\Pages\CreateRecord;

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
    }
}
