<?php

namespace App\Filament\Admin\Resources\PengumumanResource\Pages;

use App\Filament\Admin\Resources\PengumumanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengumuman extends EditRecord
{
    protected static string $resource = PengumumanResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
