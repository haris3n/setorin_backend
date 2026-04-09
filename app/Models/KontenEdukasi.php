<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KontenEdukasi extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'konten_edukasi';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pengguna',
        'judul',
        'isi',
        'kategori',
        'tgl_publikasi',
        'status',
    ];

    // --- Relationships ---

    /**
     * Mendapatkan data pengguna (biasanya Admin/Petugas) yang mempublikasikan konten ini.
     */
    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }
}