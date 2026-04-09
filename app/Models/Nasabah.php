<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nasabah extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'nasabah';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pengguna',
        'id_bank_sampah',
        'tgl_bergabung',
    ];

    // --- Relationships ---

    /**
     * Mendapatkan data pengguna (user) terkait nasabah ini.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }

    /**
     * Mendapatkan data bank sampah tempat nasabah terdaftar.
     */
    public function bankSampah(): BelongsTo
    {
        return $this->belongsTo(BankSampah::class, 'id_bank_sampah');
    }

    /**
     * Mendapatkan riwayat transaksi penyetoran milik nasabah.
     */
    public function transaksi(): HasMany
    {
        return $this->hasMany(TransaksiPenyetoran::class, 'id_nasabah');
    }
}