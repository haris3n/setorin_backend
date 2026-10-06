<?php

namespace App\Filament\Admin\Resources\PengumumanResource\Pages;

use App\Filament\Admin\Resources\PengumumanResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePengumuman extends CreateRecord
{
    protected static string $resource = PengumumanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id_pengguna'] = Auth::id();

        return $data;
    }
}
