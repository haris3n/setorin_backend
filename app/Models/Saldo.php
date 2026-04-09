<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Saldo extends Model
{
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