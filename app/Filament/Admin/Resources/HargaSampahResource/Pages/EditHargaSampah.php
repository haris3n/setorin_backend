<?php
namespace App\Filament\Admin\Resources\HargaSampahResource\Pages;

use App\Filament\Admin\Resources\HargaSampahResource;
use App\Helpers\AdminActivityLogger;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHargaSampah extends EditRecord
{
    protected static string $resource = HargaSampahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    AdminActivityLogger::delete(
                        'Harga Sampah',
                        $this->record->id,
                        $this->record->jenis_sampah
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        AdminActivityLogger::update(
            'Harga Sampah',
            $this->record->id,
            $this->record->jenis_sampah
        );
    }
}
