<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalOperasional extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'jadwal_operasional';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_bank_sampah',
        'hari',
        'jam_buka',
        'jam_tutup',
    ];

    // --- Relationships ---

    /**
     * Mendapatkan data bank sampah yang memiliki jadwal operasional ini.
     */
    public function bankSampah(): BelongsTo
    {
        return $this->belongsTo(BankSampah::class, 'id_bank_sampah');
    }
}