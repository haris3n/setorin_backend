<?php

namespace App\Filament\Admin\Resources\BankSampahResource\Pages;

use App\Filament\Admin\Resources\BankSampahResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

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
}
