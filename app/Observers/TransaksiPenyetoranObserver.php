<?php

namespace App\Observers;

use App\Models\AktivitasPetugas;
use App\Models\TransaksiPenyetoran;

class TransaksiPenyetoranObserver
{
    public function created(TransaksiPenyetoran $transaksi): void
    {
        if ($transaksi->id_petugas) {
            $nasabahNama = $transaksi->nasabah?->pengguna?->nama ?? 'Tidak diketahui';

            AktivitasPetugas::log(
                $transaksi->id_petugas,
                'transaksi_baru',
                "Menambahkan transaksi baru untuk nasabah {$nasabahNama} ({$transaksi->total_berat_kg} kg, {$transaksi->total_koin} koin)",
                [
                    'id_transaksi' => $transaksi->id,
                    'id_nasabah' => $transaksi->id_nasabah,
                    'total_berat_kg' => $transaksi->total_berat_kg,
                    'total_koin' => $transaksi->total_koin,
                    'status' => $transaksi->status,
                ]
            );
        }
    }
}