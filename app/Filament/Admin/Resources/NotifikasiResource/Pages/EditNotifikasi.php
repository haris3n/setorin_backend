<?php
namespace App\Filament\Admin\Resources\NotifikasiResource\Pages;
use App\Filament\Admin\Resources\NotifikasiResource;
use App\Helpers\AdminActivityLogger;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNotifikasi extends EditRecord
{
    protected static string $resource = NotifikasiResource::class;

    protected function getHeaderActions(): array 
    { 
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    AdminActivityLogger::delete(
                        'Notifikasi',
                        $this->record->id,
                        $this->record->judul
                    );
                })
        ]; 
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        AdminActivityLogger::update(
            'Notifikasi',
            $this->record->id,
            $this->record->judul
        );
    }
}
