<?php

namespace App\Filament\Admin\Resources\BankSampahResource\Pages;

use App\Filament\Admin\Resources\BankSampahResource;
use App\Helpers\AdminActivityLogger;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditBankSampah extends EditRecord
{
    protected static string $resource = BankSampahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    Log::info('EditBankSampah: DeleteAction triggered', [
                        'record_id' => $this->record->id,
                        'record_nama' => $this->record->nama_bank,
                    ]);

                    try {
                        AdminActivityLogger::delete(
                            'Bank Sampah',
                            $this->record->id,
                            $this->record->nama_bank
                        );
                        Log::info('EditBankSampah: Delete logged successfully');
                    } catch (\Exception $e) {
                        Log::error('EditBankSampah: Failed to log delete', [
                            'error' => $e->getMessage(),
                        ]);
                    }
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
        Log::info('EditBankSampah: afterSave() triggered', [
            'record_id' => $this->record->id,
            'record_nama' => $this->record->nama_bank,
        ]);

        try {
            AdminActivityLogger::update(
                'Bank Sampah',
                $this->record->id,
                $this->record->nama_bank
            );
            Log::info('EditBankSampah: Update logged successfully');
        } catch (\Exception $e) {
            Log::error('EditBankSampah: Failed to log update', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
