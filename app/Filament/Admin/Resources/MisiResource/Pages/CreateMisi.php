<?php
namespace App\Filament\Admin\Resources\MisiResource\Pages;

use App\Filament\Admin\Resources\MisiResource;
use App\Helpers\AdminActivityLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateMisi extends CreateRecord
{
    protected static string $resource = MisiResource::class;

    protected function afterCreate(): void
    {
        AdminActivityLogger::create(
            'Misi',
            $this->record->id,
            $this->record->nama_misi
        );
    }
}
