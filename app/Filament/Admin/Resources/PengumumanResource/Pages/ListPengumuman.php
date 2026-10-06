<?php

namespace App\Filament\Admin\Resources\PengumumanResource\Pages;

use App\Filament\Admin\Resources\PengumumanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengumuman extends ListRecords
{
    protected static string $resource = PengumumanResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Tambah Pengumuman')];
    }
}
