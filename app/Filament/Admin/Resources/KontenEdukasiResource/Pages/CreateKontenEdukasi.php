<?php
namespace App\Filament\Admin\Resources\KontenEdukasiResource\Pages;

use App\Filament\Admin\Resources\KontenEdukasiResource;
use App\Helpers\AdminActivityLogger;
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

    protected function afterCreate(): void
    {
        AdminActivityLogger::create(
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
