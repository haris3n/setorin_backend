<?php
namespace App\Filament\Admin\Resources\NotifikasiResource\Pages;
use App\Filament\Admin\Resources\NotifikasiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNotifikasi extends CreateRecord
{
    protected static string $resource = NotifikasiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
