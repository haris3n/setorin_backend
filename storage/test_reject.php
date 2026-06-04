<?php
use App\Models\User;
use App\Models\Saldo;
use App\Models\PenarikanSaldo;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;

DB::transaction(function() {
    $user = User::first() ?? User::create([
        'nama' => 'Test',
        'email' => 'test@test.com',
        'password' => bcrypt('password'),
        'no_telepon' => '081234567890',
        'role' => 'nasabah',
        'status_akun' => 'aktif'
    ]);
    
    $saldo = Saldo::firstOrCreate(
        ['id_pengguna' => $user->id],
        ['jumlah_saldo' => 100000]
    );
    
    $p = PenarikanSaldo::create([
        'id_pengguna' => $user->id,
        'id_saldo' => $saldo->id,
        'jumlah_tarik' => 20000,
        'metode_bayar' => 'Dana',
        'no_rekening' => '081234567890',
        'status' => 'pending'
    ]);
    
    echo "Created Penarikan ID: " . $p->id . PHP_EOL;
    
    // Now try to run the tolak logic
    $p->update(['status' => 'ditolak']);
    
    // Refund: kembalikan saldo_tertahan ke saldo aktif
    $saldo = Saldo::find($p->id_saldo);
    if ($saldo) {
        $saldo->decrement('saldo_tertahan', $p->jumlah_tarik);
        $saldo->increment('jumlah_saldo', $p->jumlah_tarik);
        $saldo->update(['tgl_update' => now()]);
    }
    
    Notifikasi::create([
        'id_pengguna' => $p->id_pengguna,
        'judul'       => 'Penarikan Saldo Ditolak',
        'pesan'       => 'Penarikan Rp ' . number_format($p->jumlah_tarik) . ' ditolak.',
        'tipe'        => 'saldo',
    ]);
    
    echo "Successfully rejected and refunded!" . PHP_EOL;
});

