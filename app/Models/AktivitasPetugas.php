<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivitasPetugas extends Model
{
    protected $table = 'aktivitas_petugas';

    protected $fillable = [
        'id_petugas',
        'jenis_aktivitas',
        'deskripsi',
        'data_lampiran',
    ];

    protected $casts = [
        'data_lampiran' => 'array',
    ];

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(Petugas::class, 'id_petugas');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_petugas', 'id')
            ->via('petugas');
    }

    /**
     * Static method untuk mencatat aktivitas
     */
    public static function log(int $idPetugas, string $jenis, string $deskripsi, ?array $data = null): self
    {
        return static::create([
            'id_petugas' => $idPetugas,
            'jenis_aktivitas' => $jenis,
            'deskripsi' => $deskripsi,
            'data_lampiran' => $data,
        ]);
    }
}