<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Role dulu
        $roles = ['admin', 'petugas', 'nasabah'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'api']);
        }

        // Baru panggil seeder lainnya
        $this->call([
            AdminSeeder::class,
            BankSampahSeeder::class,
            HargaSampahSeeder::class,
            HargaCoinSeeder::class,
            MisiSeeder::class,
        ]);
    }
}