<?php

namespace App\Filament\Petugas\Resources\TransaksiPenyetoranResource\Pages;

use App\Filament\Petugas\Resources\TransaksiPenyetoranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransaksiPenyetorans extends ListRecords
{
    protected static string $resource = TransaksiPenyetoranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
