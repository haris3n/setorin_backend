<?php
namespace App\Filament\Admin\Resources\NotifikasiResource\Pages;

use App\Filament\Admin\Resources\NotifikasiResource;
use App\Helpers\AdminActivityLogger;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Log;

class EditNotifikasi extends EditRecord
{
    protected static string $resource = NotifikasiResource::class;

    protected function getHeaderActions(): array 
    { 
        return [
            Actions\DeleteAction::make()
                ->after(function () {
                    Log::info('EditNotifikasi: DeleteAction triggered', [
                        'record_id' => $this->record->id,
                        'record_judul' => $this->record->judul,
                    ]);

                    try {
                        AdminActivityLogger::delete(
                            'Notifikasi',
                            $this->record->id,
                            $this->record->judul
                        );
                        Log::info('EditNotifikasi: Delete logged successfully');
                    } catch (\Exception $e) {
                        Log::error('EditNotifikasi: Failed to log delete', [
                            'error' => $e->getMessage(),
                        ]);
                    }
                })
        ]; 
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        Log::info('EditNotifikasi: afterSave() triggered', [
            'record_id' => $this->record->id,
            'record_judul' => $this->record->judul,
        ]);

        try {
            AdminActivityLogger::update(
                'Notifikasi',
                $this->record->id,
                $this->record->judul
            );
            Log::info('EditNotifikasi: Update logged successfully');
        } catch (\Exception $e) {
            Log::error('EditNotifikasi: Failed to log update', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
