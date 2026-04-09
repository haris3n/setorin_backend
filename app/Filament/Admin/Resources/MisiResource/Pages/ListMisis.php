<?php
namespace App\Filament\Admin\Resources\MisiResource\Pages;
use App\Filament\Admin\Resources\MisiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListMisis extends ListRecords
{
    protected static string $resource = MisiResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
