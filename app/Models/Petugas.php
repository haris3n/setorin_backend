<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Petugas extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'petugas';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pengguna',
        'id_bank_sampah',
    ];

    // --- Relationships ---

    /**
     * Mendapatkan data profil pengguna (User) untuk petugas ini.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }

    /**
     * Mendapatkan data bank sampah tempat petugas ini bertugas.
     */
    public function bankSampah(): BelongsTo
    {
        return $this->belongsTo(BankSampah::class, 'id_bank_sampah');
    }

    /**
     * Mendapatkan riwayat transaksi yang pernah dilayani oleh petugas ini.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiPenyetoran::class, 'id_petugas');
    }
}