<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'notifikasi';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pengguna',
        'id_transaksi',
        'judul',
        'pesan',
        'tipe',
        'status_notifikasi',
        'memerlukan_konfirmasi',
    ];

    protected $casts = [
        'memerlukan_konfirmasi' => 'boolean',
    ];

    // --- Relationships ---

    /**
     * Mendapatkan data pengguna (User) penerima notifikasi ini.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiPenyetoran::class, 'id_transaksi');
    }
}