<?php

namespace App\Helpers;

/**
 * Format QR kartu nasabah: SETORIN:NASABAH:{id_pengguna}
 * Tetap mendukung scan legacy (no telepon / id numerik) di form petugas.
 */
class NasabahQrCode
{
    public const PREFIX = 'SETORIN:NASABAH:';

    public static function encode(int $userId): string
    {
        return self::PREFIX . $userId;
    }

    /**
     * Ambil id pengguna dari payload QR resmi, atau null jika bukan format tersebut.
     */
    public static function parseUserId(?string $payload): ?int
    {
        if ($payload === null || $payload === '') {
            return null;
        }

        $payload = trim($payload);

        if (! str_starts_with($payload, self::PREFIX)) {
            return null;
        }

        $id = (int) substr($payload, strlen(self::PREFIX));

        return $id > 0 ? $id : null;
    }
}
