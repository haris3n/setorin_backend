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
        $guards = ['web', 'api'];
        foreach ($roles as $role) {
            foreach ($guards as $guard) {
                Role::firstOrCreate(['name' => $role, 'guard_name' => $guard]);
            }
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