<?php
namespace App\Filament\Admin\Resources\KontenEdukasiResource\Pages;
use App\Filament\Admin\Resources\KontenEdukasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
class EditKontenEdukasi extends EditRecord
{
    protected static string $resource = KontenEdukasiResource::class;
    protected function getHeaderActions(): array { return [Actions\DeleteAction::make()]; }
}
