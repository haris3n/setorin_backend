<?php

namespace App\Console\Commands;

use App\Models\BankSampah;
use App\Models\Nasabah;
use App\Models\Petugas;
use App\Models\User;
use Illuminate\Console\Command;

class FixMissingPetugasNasabah extends Command
{
    protected $signature = 'fix:missing-records {--bank-sampah= : ID Bank Sampah untuk assign}';
    protected $description = 'Buat record petugas/nasabah yang hilang untuk user yang sudah ada';

    public function handle(): int
    {
        $bankId = $this->option('bank-sampah');

        if (!$bankId) {
            $banks = BankSampah::where('status', 'aktif')->get();
            if ($banks->isEmpty()) {
                $this->error('Tidak ada Bank Sampah aktif.');
                return 1;
            }
            $this->table(['ID', 'Nama'], $banks->map(fn($b) => [$b->id, $b->nama_bank]));
            $bankId = $this->ask('Masukkan ID Bank Sampah untuk assign semua user yang belum punya record');
        }

        $bank = BankSampah::find($bankId);
        if (!$bank) {
            $this->error("Bank Sampah #{$bankId} tidak ditemukan.");
            return 1;
        }

        $this->info("Menggunakan: {$bank->nama_bank}");

        // Fix Petugas
        $petugasUsers = User::where('role', 'petugas')
            ->whereDoesntHave('petugas')
            ->get();

        foreach ($petugasUsers as $user) {
            Petugas::create([
                'id_pengguna' => $user->id,
                'id_bank_sampah' => $bankId,
            ]);
            $this->line("  ✅ Petugas record dibuat untuk: {$user->nama} (#{$user->id})");
        }

        // Fix Nasabah
        $nasabahUsers = User::where('role', 'nasabah')
            ->whereDoesntHave('nasabah')
            ->get();

        foreach ($nasabahUsers as $user) {
            Nasabah::create([
                'id_pengguna' => $user->id,
                'id_bank_sampah' => $bankId,
                'tgl_bergabung' => $user->created_at?->toDateString() ?? now()->toDateString(),
            ]);
            $this->line("  ✅ Nasabah record dibuat untuk: {$user->nama} (#{$user->id})");
        }

        $this->newLine();
        $this->info("Selesai! {$petugasUsers->count()} petugas & {$nasabahUsers->count()} nasabah diperbaiki.");

        return 0;
    }
}
