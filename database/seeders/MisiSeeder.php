<?php

namespace Database\Seeders;

use App\Models\Misi;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MisiSeeder extends Seeder
{
    /**
     * Jalankan seed database untuk daftar misi.
     */
    public function run(): void
    {
        $misi = [
            [
                'nama' => 'Setor Perdana',
                'desc' => 'Lakukan penyetoran sampah pertamamu',
                'koin' => 50
            ],
            [
                'nama' => 'Setoran 1 Kg',
                'desc' => 'Setor minimal 1 kg sampah hari ini',
                'koin' => 30
            ],
            [
                'nama' => 'Setoran 5 Kg',
                'desc' => 'Setor minimal 5 kg sampah hari ini',
                'koin' => 100
            ],
            [
                'nama' => 'Edukasi Harian',
                'desc' => 'Baca 1 artikel edukasi hari ini',
                'koin' => 10
            ],
            [
                'nama' => 'Setor 3 Hari Berturut',
                'desc' => 'Setor sampah 3 hari berturut-turut',
                'koin' => 150
            ],
        ];

        foreach ($misi as $item) {
            Misi::updateOrCreate(
                ['nama_misi' => $item['nama']], // Kunci pencarian unik
                [
                    'deskripsi'   => $item['desc'],
                    'reward_koin' => $item['koin'],
                    'tgl_mulai'   => Carbon::today(),
                    'tgl_selesai' => Carbon::today()->addDays(30),
                    'status_misi' => 'aktif',
                ]
            );
        }

        $this->command->info('Daftar misi berhasil disemai.');
    }
}