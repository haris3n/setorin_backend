<?php
namespace App\Filament\Admin\Resources\KontenEdukasiResource\Pages;
use App\Filament\Admin\Resources\KontenEdukasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
class ListKontenEdukasis extends ListRecords
{
    protected static string $resource = KontenEdukasiResource::class;
    protected function getHeaderActions(): array { return [Actions\CreateAction::make()]; }
}
