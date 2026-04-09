<?php

namespace App\Filament\Petugas\Resources\TransaksiPenyetoranResource\Pages;

use App\Filament\Petugas\Resources\TransaksiPenyetoranResource;
use App\Models\{DetailTransaksiSampah, HargaSampah, Koin, Nasabah, Notifikasi, TransaksiPenyetoran};
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
            // 1. Buat Header Transaksi (Awal)
            $transaksi = TransaksiPenyetoran::create([
                'id_nasabah'     => $data['id_nasabah'],
                'id_bank_sampah' => $data['id_bank_sampah'],
                'id_petugas'     => $data['id_petugas'],
                'catatan'        => $data['catatan'] ?? null,
                'status'         => 'diproses',
                'tgl_setor'      => now(),
            ]);

            // 2. Iterasi Detail Sampah
            foreach ($detail as $item) {
                $harga = HargaSampah::findOrFail($item['id_harga_sampah']);
                
                // Hitung Nilai Rupiah & Konversi ke Koin
                $subtotalRupiah = $harga->harga_per_kg * $item['berat_kg'];
                $perolehanKoin = (int)($subtotalRupiah / 100);

                DetailTransaksiSampah::create([
                    'id_transaksi'    => $transaksi->id,
                    'id_harga_sampah' => $item['id_harga_sampah'],
                    'berat_kg'        => $item['berat_kg'],
                    'subtotal'        => $subtotalRupiah,
                ]);

                $totalBerat += $item['berat_kg'];
                $totalKoin += $perolehanKoin;
            }

            // 3. Update Final Header Transaksi
            $transaksi->update([
                'total_berat_kg' => $totalBerat,
                'total_koin'     => $totalKoin,
                'status'         => 'selesai',
            ]);

            // 4. Update Saldo Koin Nasabah & Catat Riwayat Koin
            $nasabah = Nasabah::findOrFail($data['id_nasabah']);
            
            Koin::create([
                'id_pengguna'  => $nasabah->id_pengguna,
                'jumlah_koin'  => $totalKoin,
                'sumber'       => 'transaksi',
                'id_referensi' => $transaksi->id,
            ]);

            // 5. Buat Notifikasi Database untuk Aplikasi Mobile Nasabah
            Notifikasi::create([
                'id_pengguna' => $nasabah->id_pengguna,
                'judul'       => 'Penyetoran Berhasil!',
                'pesan'       => "Setoran seberat {$totalBerat}kg telah selesai diproses. Anda mendapatkan {$totalKoin} koin.",
                'tipe'        => 'transaksi',
            ]);

            return $transaksi;
        });
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Transaksi Berhasil Disimpan')
            ->body('Data setoran telah tercatat dan koin nasabah telah diperbarui.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}