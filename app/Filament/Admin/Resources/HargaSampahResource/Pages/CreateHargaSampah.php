<?php
namespace App\Filament\Admin\Resources\HargaSampahResource\Pages;

use App\Filament\Admin\Resources\HargaSampahResource;
use App\Helpers\AdminActivityLogger;
use Filament\Resources\Pages\CreateRecord;

class CreateHargaSampah extends CreateRecord
{
    protected static string $resource = HargaSampahResource::class;

    protected function afterCreate(): void
    {
        AdminActivityLogger::create(
            'Harga Sampah',
            $this->record->id,
            $this->record->jenis_sampah
        );
    }
}
