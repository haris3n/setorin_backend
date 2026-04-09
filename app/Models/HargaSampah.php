<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HargaSampah extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'harga_sampah';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_bank_sampah',
        'jenis_sampah',
        'harga_per_kg',
        'status',
    ];

    // --- Relationships ---

    /**
     * Mendapatkan data bank sampah yang memiliki referensi harga ini.
     */
    public function bankSampah(): BelongsTo
    {
        return $this->belongsTo(BankSampah::class, 'id_bank_sampah');
    }

    /**
     * Mendapatkan detail transaksi yang menggunakan referensi harga ini.
     */
    public function detail(): HasMany
    {
        return $this->hasMany(DetailTransaksiSampah::class, 'id_harga_sampah');
    }
}