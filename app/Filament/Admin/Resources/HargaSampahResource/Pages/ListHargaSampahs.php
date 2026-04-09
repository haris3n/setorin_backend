<?php
namespace App\Filament\Admin\Resources\HargaSampahResource\Pages;
use App\Filament\Admin\Resources\HargaSampahResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListHargaSampahs extends ListRecords
{
    protected static string $resource = HargaSampahResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
