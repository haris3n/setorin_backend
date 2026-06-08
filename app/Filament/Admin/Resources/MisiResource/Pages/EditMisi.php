<?php
namespace App\Filament\Admin\Resources\MisiResource\Pages;

use App\Filament\Admin\Resources\MisiResource;
use App\Helpers\AdminActivityLogger;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMisi extends EditRecord
{
    protected static string $resource = MisiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    AdminActivityLogger::delete(
                        'Misi',
                        $this->record->id,
                        $this->record->nama_misi
                    );
                }),
        ];
    }

    protected function afterSave(): void
    {
        AdminActivityLogger::update(
            'Misi',
            $this->record->id,
            $this->record->nama_misi
        );
    }
}
