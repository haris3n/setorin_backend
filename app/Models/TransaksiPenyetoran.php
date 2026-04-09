<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiPenyetoran extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'transaksi_penyetoran';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_nasabah',
        'id_bank_sampah',
        'id_petugas',
        'tgl_setor',
        'total_berat_kg',
        'total_koin',
        'status',
        'catatan',
    ];

    // --- Relationships ---

    /**
     * Mendapatkan data nasabah yang melakukan penyetoran.
     */
    public function nasabah(): BelongsTo
    {
        return $this->belongsTo(Nasabah::class, 'id_nasabah');
    }

    /**
     * Mendapatkan data bank sampah tempat transaksi terjadi.
     */
    public function bankSampah(): BelongsTo
    {
        return $this->belongsTo(BankSampah::class, 'id_bank_sampah');
    }

    /**
     * Mendapatkan data petugas yang melayani transaksi ini.
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'id_petugas');
    }

    /**
     * Mendapatkan detail rincian sampah untuk transaksi ini.
     */
    public function detail(): HasMany
    {
        return $this->hasMany(DetailTransaksiSampah::class, 'id_transaksi');
    }
}