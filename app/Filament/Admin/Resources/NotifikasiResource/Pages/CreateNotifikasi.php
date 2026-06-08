<?php
namespace App\Filament\Admin\Resources\NotifikasiResource\Pages;
use App\Filament\Admin\Resources\NotifikasiResource;
use App\Helpers\AdminActivityLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateNotifikasi extends CreateRecord
{
    protected static string $resource = NotifikasiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        AdminActivityLogger::create(
            'Notifikasi',
            $this->record->id,
            $this->record->judul
        );
    }
}
