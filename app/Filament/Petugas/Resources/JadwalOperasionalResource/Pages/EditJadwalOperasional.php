<?php

namespace App\Filament\Petugas\Resources\JadwalOperasionalResource\Pages;

use App\Filament\Petugas\Resources\JadwalOperasionalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJadwalOperasional extends EditRecord
{
    protected static string $resource = JadwalOperasionalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
