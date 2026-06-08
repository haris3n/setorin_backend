<?php

namespace App\Filament\Petugas\Resources\JadwalOperasionalResource\Pages;

use App\Filament\Petugas\Resources\JadwalOperasionalResource;
use App\Models\AktivitasPetugas;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateJadwalOperasional extends CreateRecord
{
    protected static string $resource = JadwalOperasionalResource::class;

    protected function afterCreate(): void
    {
        $user = Auth::user();
        if ($user?->petugas) {
            AktivitasPetugas::log(
                $user->petugas->id,
                'jadwal_dibuat',
                "Menambahkan jadwal operasional hari {$this->record->hari} ({$this->record->jam_buka} - {$this->record->jam_tutup})",
                [
                    'id_jadwal'   => $this->record->id,
                    'hari'        => $this->record->hari,
                    'jam_buka'    => $this->record->jam_buka,
                    'jam_tutup'   => $this->record->jam_tutup,
                    'is_active'   => $this->record->is_active,
                ]
            );
        }
    }
}
