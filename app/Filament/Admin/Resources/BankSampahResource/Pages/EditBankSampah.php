<?php

namespace App\Filament\Admin\Resources\BankSampahResource\Pages;

use App\Filament\Admin\Resources\BankSampahResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBankSampah extends EditRecord
{
    protected static string $resource = BankSampahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
