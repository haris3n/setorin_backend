<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Saldo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Jalankan seed database untuk akun Admin.
     */
    public function run(): void
    {
        // Membuat atau memperbarui akun admin utama
        $admin = User::updateOrCreate(
            ['email' => 'admin@setorin.com'], // Kunci pencarian
            [
                'nama'        => 'Admin Setor.in',
                'no_telepon'  => '081234567890',
                'password'    => Hash::make('admin123'),
                'role'        => 'admin',
                'status_akun' => 'aktif',
            ]
        );

        // Menugaskan role menggunakan Spatie Permission
        $admin->assignRole('admin');

        // Memastikan record saldo tersedia untuk admin jika belum ada
        Saldo::firstOrCreate(
            ['id_pengguna' => $admin->id],
            ['jumlah_saldo' => 0]
        );
    }
}