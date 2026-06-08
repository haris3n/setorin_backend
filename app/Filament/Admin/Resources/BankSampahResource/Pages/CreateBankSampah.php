<?php

namespace App\Filament\Admin\Resources\BankSampahResource\Pages;

use App\Filament\Admin\Resources\BankSampahResource;
use App\Helpers\AdminActivityLogger;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateBankSampah extends CreateRecord
{
    protected static string $resource = BankSampahResource::class;

    // Redirect ke list setelah create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // Notifikasi sukses
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Bank Sampah berhasil dibuat!';
    }

    protected function afterCreate(): void
    {
        Log::info('CreateBankSampah: afterCreate() triggered', [
            'record_id' => $this->record->id,
            'record_nama' => $this->record->nama_bank,
        ]);

        try {
            AdminActivityLogger::create(
                'Bank Sampah',
                $this->record->id,
                $this->record->nama_bank
            );
            Log::info('CreateBankSampah: AdminActivityLogger called successfully');
        } catch (\Exception $e) {
            Log::error('CreateBankSampah: Failed to log activity', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
