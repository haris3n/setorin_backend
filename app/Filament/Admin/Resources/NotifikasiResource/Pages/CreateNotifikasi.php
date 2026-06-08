<?php
namespace App\Filament\Admin\Resources\NotifikasiResource\Pages;

use App\Filament\Admin\Resources\NotifikasiResource;
use App\Helpers\AdminActivityLogger;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;

class CreateNotifikasi extends CreateRecord
{
    protected static string $resource = NotifikasiResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        Log::info('CreateNotifikasi: afterCreate() triggered', [
            'record_id' => $this->record->id,
            'record_judul' => $this->record->judul,
        ]);

        try {
            AdminActivityLogger::create(
                'Notifikasi',
                $this->record->id,
                $this->record->judul
            );
            
            Log::info('CreateNotifikasi: AdminActivityLogger called successfully');
        } catch (\Exception $e) {
            Log::error('CreateNotifikasi: Failed to log activity', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
