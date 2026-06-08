<?php
namespace App\Filament\Admin\Resources\KontenEdukasiResource\Pages;

use App\Filament\Admin\Resources\KontenEdukasiResource;
use App\Helpers\AdminActivityLogger;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKontenEdukasi extends EditRecord
{
    protected static string $resource = KontenEdukasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    AdminActivityLogger::delete(
                        'Konten Edukasi',
                        $this->record->id,
                        $this->record->judul
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        AdminActivityLogger::update(
            'Konten Edukasi',
            $this->record->id,
            $this->record->judul
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
