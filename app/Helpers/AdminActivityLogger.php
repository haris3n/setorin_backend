<?php

namespace App\Helpers;

use App\Models\AktivitasAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Filament\Facades\Filament;

class AdminActivityLogger
{
    /**
     * Catat aktivitas admin
     */
    public static function log(
        string $jenis,
        string $deskripsi,
        ?string $modul = null,
        ?int $dataId = null,
        ?array $dataLama = null,
        ?array $dataBaru = null
    ): void {
        try {
            Log::info('AdminActivityLogger: Starting log creation', [
                'jenis' => $jenis,
                'modul' => $modul,
                'data_id' => $dataId,
            ]);

            // Try Filament auth first, fallback to regular Auth
            $user = Filament::auth()->user() ?? Auth::user();

            if (!$user) {
                Log::warning('AdminActivityLogger: No authenticated user found');
                return;
            }

            Log::info('AdminActivityLogger: User authenticated', [
                'user_id' => $user->id,
                'user_name' => $user->nama ?? 'N/A',
            ]);

            $logData = [
                'id_pengguna' => $user->id,
                'jenis_aktivitas' => $jenis,
                'modul' => $modul,
                'data_id' => $dataId,
                'deskripsi' => $deskripsi,
                'data_lama' => $dataLama,
                'data_baru' => $dataBaru,
                'created_at' => now(),
            ];

            Log::info('AdminActivityLogger: Attempting to create log', $logData);

            $aktivitas = AktivitasAdmin::create($logData);
            
            Log::info('AdminActivityLogger: Log created successfully', [
                'aktivitas_id' => $aktivitas->id,
            ]);
            
        } catch (\Exception $e) {
            Log::error('AdminActivityLogger Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Catat aktivitas create
     */
    public static function create(string $modul, int $dataId, string $namaData): void
    {
        self::log('create', "Menambahkan data {$modul}: {$namaData}", $modul, $dataId);
    }

    /**
     * Catat aktivitas update
     */
    public static function update(string $modul, int $dataId, string $namaData): void
    {
        self::log('update', "Mengubah data {$modul}: {$namaData}", $modul, $dataId);
    }

    /**
     * Catat aktivitas delete
     */
    public static function delete(string $modul, int $dataId, string $namaData): void
    {
        self::log('delete', "Menghapus data {$modul}: {$namaData}", $modul, $dataId);
    }
}