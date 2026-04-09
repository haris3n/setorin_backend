<?php

namespace Database\Seeders;

use App\Models\HargaCoin;
use Illuminate\Database\Seeder;

class HargaCoinSeeder extends Seeder
{
    /**
     * Jalankan seed database untuk harga coin.
     */
    public function run(): void
    {
        // 1 coin = Rp 100 (contoh)
        HargaCoin::updateOrCreate(
            ['id' => 1],
            [
                'harga_per_coin' => 100,
                'status' => 'aktif',
            ]
        );

        $this->command->info('Harga coin berhasil disemai.');
    }
}
