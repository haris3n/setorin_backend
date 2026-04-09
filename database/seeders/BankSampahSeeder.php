<?php

namespace Database\Seeders;

use App\Models\BankSampah;
use App\Models\JadwalOperasional;
use Illuminate\Database\Seeder;

class BankSampahSeeder extends Seeder
{
    /**
     * Jalankan seed database untuk Bank Sampah dan Jadwal Operasional.
     */
    public function run(): void
    {
        // 1. Buat atau perbarui data Bank Sampah utama
        $bank = BankSampah::updateOrCreate(
            ['nama_bank' => 'Bank Sampah Indramayu Bersih'], // Kunci unik
            [
                'alamat'     => 'Jl. Lohbener Lama No. 8, Indramayu',
                'no_telepon' => '0234567890',
                'status'     => 'aktif',
            ]
        );

        // 2. Definisi Jadwal Operasional
        $jadwal = [
            ['hari' => 'Senin', 'buka' => '08:00', 'tutup' => '16:00'],
            ['hari' => 'Selasa', 'buka' => '08:00', 'tutup' => '16:00'],
            ['hari' => 'Rabu', 'buka' => '08:00', 'tutup' => '16:00'],
            ['hari' => 'Kamis', 'buka' => '08:00', 'tutup' => '16:00'],
            ['hari' => 'Jumat', 'buka' => '08:00', 'tutup' => '15:00'],
            ['hari' => 'Sabtu', 'buka' => '08:00', 'tutup' => '12:00'],
        ];

        // 3. Masukkan jadwal ke database
        foreach ($jadwal as $j) {
            JadwalOperasional::updateOrCreate(
                [
                    'id_bank_sampah' => $bank->id,
                    'hari'           => $j['hari'],
                ],
                [
                    'jam_buka'  => $j['buka'],
                    'jam_tutup' => $j['tutup'],
                ]
            );
        }
    }
}