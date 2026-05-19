<?php
namespace App\Filament\Admin\Resources\KontenEdukasiResource\Pages;
use App\Filament\Admin\Resources\KontenEdukasiResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateKontenEdukasi extends CreateRecord
{
    protected static string $resource = KontenEdukasiResource::class;

    /**
     * Auto-fill id_pengguna dengan user yang sedang login
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['id_pengguna'] = Auth::id();
        
        return $data;
    }

    /**
     * Redirect ke list page setelah create
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

