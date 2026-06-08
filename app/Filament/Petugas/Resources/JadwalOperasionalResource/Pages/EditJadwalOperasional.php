<?php

namespace App\Filament\Petugas\Resources\JadwalOperasionalResource\Pages;

use App\Filament\Petugas\Resources\JadwalOperasionalResource;
use App\Models\AktivitasPetugas;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditJadwalOperasional extends EditRecord
{
    protected static string $resource = JadwalOperasionalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    $user = Auth::user();
                    if ($user?->petugas) {
                        AktivitasPetugas::log(
                            $user->petugas->id,
                            'jadwal_dihapus',
                            "Menghapus jadwal operasional hari {$this->record->hari}",
                            ['id_jadwal' => $this->record->id, 'hari' => $this->record->hari]
                        );
                    }
                }),
        ];
    }

    protected function afterSave(): void
    {
        $user = Auth::user();
        if ($user?->petugas) {
            AktivitasPetugas::log(
                $user->petugas->id,
                'jadwal_diubah',
                "Mengubah jadwal operasional hari {$this->record->hari} ({$this->record->jam_buka} - {$this->record->jam_tutup})",
                [
                    'id_jadwal' => $this->record->id,
                    'hari'      => $this->record->hari,
                    'jam_buka'  => $this->record->jam_buka,
                    'jam_tutup' => $this->record->jam_tutup,
                    'is_active' => $this->record->is_active,
                ]
            );
        }
    }
}
