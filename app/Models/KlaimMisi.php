<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KlaimMisi extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'klaim_misi';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pengguna',
        'id_misi',
        'tgl_klaim',
        'status_klaim',
        'koin_diterima',
    ];

    // --- Relationships ---

    /**
     * Mendapatkan data pengguna (User) yang melakukan klaim misi.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }

    /**
     * Mendapatkan data misi yang diklaim oleh pengguna.
     */
    public function misi(): BelongsTo
    {
        return $this->belongsTo(Misi::class, 'id_misi');
    }
}