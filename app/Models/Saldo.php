<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Saldo extends Model
{
    /**
     * Nonaktifkan timestamps karena tabel saldo menggunakan tgl_update manual.
     */
    public $timestamps = false;

    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'saldo';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pengguna',
        'jumlah_saldo',
        'saldo_tertahan',
        'tgl_update',
    ];

    // --- Relationships ---

    /**
     * Mendapatkan data pengguna (User) pemilik saldo ini.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }

    /**
     * Mendapatkan riwayat penarikan yang terkait dengan saldo ini.
     */
    public function penarikan(): HasMany
    {
        return $this->hasMany(PenarikanSaldo::class, 'id_saldo');
    }
}