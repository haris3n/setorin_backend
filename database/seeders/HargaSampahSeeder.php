<?php

namespace Database\Seeders;

use App\Models\BankSampah;
use App\Models\HargaSampah;
use Illuminate\Database\Seeder;

class HargaSampahSeeder extends Seeder
{
    /**
     * Jalankan seed database untuk master harga sampah.
     */
    public function run(): void
    {
        // Ambil bank sampah pertama sebagai referensi
        $bank = BankSampah::first();

        // Pastikan ada bank sampah sebelum menjalankan seeder
        if (!$bank) {
            $this->command->warn('Data Bank Sampah tidak ditemukan. Jalankan BankSampahSeeder terlebih dahulu.');
            return;
        }

        $jenisSampah = [
            ['jenis' => 'Plastik PET',  'harga' => 2000],
            ['jenis' => 'Plastik HDPE', 'harga' => 1800],
            ['jenis' => 'Kertas Koran', 'harga' => 1500],
            ['jenis' => 'Kardus',       'harga' => 1200],
            ['jenis' => 'Logam/Besi',   'harga' => 5000],
            ['jenis' => 'Aluminium',    'harga' => 8000],
            ['jenis' => 'Botol Kaca',   'harga' => 1000],
            ['jenis' => 'Elektronik',   'harga' => 3000],
        ];

        foreach ($jenisSampah as $item) {
            HargaSampah::updateOrCreate(
                [
                    'id_bank_sampah' => $bank->id,
                    'jenis_sampah'   => $item['jenis'],
                ],
                [
                    'harga_per_kg' => $item['harga'],
                    'status'       => 'aktif',
                ]
            );
        }

        $this->command->info('Master harga sampah berhasil disemai.');
    }
}