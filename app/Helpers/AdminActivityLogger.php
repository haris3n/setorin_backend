<?php

namespace App\Helpers;

use App\Models\AktivitasAdmin;
use Illuminate\Support\Facades\Auth;

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
        $user = Auth::user();

        if (!$user) {
            return;
        }

        AktivitasAdmin::log(
            $user->id,
            $jenis,
            $deskripsi,
            $modul,
            $dataId,
            $dataLama,
            $dataBaru
        );
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