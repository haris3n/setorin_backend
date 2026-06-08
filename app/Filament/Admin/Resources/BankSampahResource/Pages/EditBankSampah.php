<?php

namespace App\Filament\Admin\Resources\BankSampahResource\Pages;

use App\Filament\Admin\Resources\BankSampahResource;
use App\Helpers\AdminActivityLogger;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBankSampah extends EditRecord
{
    protected static string $resource = BankSampahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    AdminActivityLogger::delete(
                        'Bank Sampah',
                        $this->record->id,
                        $this->record->nama_bank
                    );
                }),
        ];
    }

    // Redirect ke list setelah update
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Notifikasi sukses
    protected function getSavedNotificationTitle(): ?string
    {
        return 'Bank Sampah berhasil diupdate!';
    }

    protected function afterSave(): void
    {
        AdminActivityLogger::update(
            'Bank Sampah',
            $this->record->id,
            $this->record->nama_bank
        );
    }
}
