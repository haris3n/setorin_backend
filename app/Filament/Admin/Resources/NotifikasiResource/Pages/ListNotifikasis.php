<?php
namespace App\Filament\Admin\Resources\NotifikasiResource\Pages;
use App\Filament\Admin\Resources\NotifikasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotifikasis extends ListRecords
{
    protected static string $resource = NotifikasiResource::class;
    
    protected function getHeaderActions(): array 
    { 
        return [Actions\CreateAction::make()]; 
    }
}
