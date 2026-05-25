<?php

namespace App\Filament\Petugas\Resources\TransaksiPenyetoranResource\Pages;

use App\Filament\Petugas\Resources\TransaksiPenyetoranResource;
use App\Models\{DetailTransaksiSampah, HargaSampah, Nasabah, Notifikasi, TransaksiPenyetoran};
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateTransaksiPenyetoran extends CreateRecord
{
    protected static string $resource = TransaksiPenyetoranResource::class;

    protected function handleRecordCreation(array $data): TransaksiPenyetoran
    {
        $detail = $data['detail'] ?? [];
        $totalBerat = 0;
        $totalKoin = 0;

        return DB::transaction(function () use ($data, $detail, &$totalBerat, &$totalKoin) {
            $transaksi = TransaksiPenyetoran::create([
                'id_nasabah'     => $data['id_nasabah'],
                'id_bank_sampah' => $data['id_bank_sampah'],
                'id_petugas'     => $data['id_petugas'],
                'catatan'        => $data['catatan'] ?? null,
                'status'         => 'diproses',
                'tgl_setor'      => now(),
            ]);

            foreach ($detail as $item) {
                $harga = HargaSampah::findOrFail($item['id_harga_sampah']);

                $subtotalRupiah = $harga->harga_per_kg * $item['berat_kg'];
                $perolehanKoin = (int) ($subtotalRupiah / 100);

                DetailTransaksiSampah::create([
                    'id_transaksi'    => $transaksi->id,
                    'id_harga_sampah' => $item['id_harga_sampah'],
                    'berat_kg'        => $item['berat_kg'],
                    'subtotal'        => $subtotalRupiah,
                ]);

                $totalBerat += $item['berat_kg'];
                $totalKoin += $perolehanKoin;
            }

            $transaksi->update([
                'total_berat_kg' => $totalBerat,
                'total_koin'     => $totalKoin,
                'status'         => 'diproses',
            ]);

            $nasabah = Nasabah::findOrFail($data['id_nasabah']);

            Notifikasi::create([
                'id_pengguna'           => $nasabah->id_pengguna,
                'id_transaksi'          => $transaksi->id,
                'judul'                 => 'Konfirmasi Setoran Sampah',
                'pesan'                 => "Petugas mencatat setoran {$totalBerat} kg (estimasi {$totalKoin} koin). Buka aplikasi dan tekan Konfirmasi jika data sudah sesuai.",
                'tipe'                  => 'transaksi',
                'status_notifikasi'     => 'belum_dibaca',
                'memerlukan_konfirmasi' => true,
            ]);

            return $transaksi;
        });
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Transaksi Tercatat')
            ->body('Menunggu konfirmasi nasabah di aplikasi mobile. Koin akan masuk setelah nasabah mengonfirmasi.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
