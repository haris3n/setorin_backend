<?php

namespace App\Filament\Petugas\Resources\NasabahResource\Pages;

use App\Filament\Petugas\Resources\NasabahResource;
use Filament\Resources\Pages\ListRecords;

class ListNasabahs extends ListRecords
{
    protected static string $resource = NasabahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // View-only - tidak ada tombol tambah
        ];
    }
}