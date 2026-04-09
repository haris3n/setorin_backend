<?php
namespace App\Filament\Admin\Resources\HargaSampahResource\Pages;
use App\Filament\Admin\Resources\HargaSampahResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditHargaSampah extends EditRecord
{
    protected static string $resource = HargaSampahResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
