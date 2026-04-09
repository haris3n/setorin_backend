<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenarikanSaldo extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'penarikan_saldo';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pengguna',
        'id_saldo',
        'jumlah_tarik',
        'metode_bayar',
        'no_rekening',
        'tgl_penarikan',
        'status',
    ];

    // --- Relationships ---

    /**
     * Mendapatkan data pengguna (User) yang melakukan penarikan.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }

    /**
     * Mendapatkan referensi saldo terkait untuk transaksi penarikan ini.
     */
    public function saldo(): BelongsTo
    {
        return $this->belongsTo(Saldo::class, 'id_saldo');
    }
}