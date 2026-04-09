<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksiSampah extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'detail_transaksi_sampah';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_transaksi',
        'id_harga_sampah',
        'berat_kg',
        'subtotal',
    ];

    // --- Relationships ---

    /**
     * Mendapatkan data transaksi utama (header) untuk detail ini.
     */
    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiPenyetoran::class, 'id_transaksi');
    }

    /**
     * Mendapatkan referensi harga dan jenis sampah yang digunakan dalam detail ini.
     */
    public function hargaSampah(): BelongsTo
    {
        return $this->belongsTo(HargaSampah::class, 'id_harga_sampah');
    }
}