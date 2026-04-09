<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AktivitasAdmin extends Model
{
    protected $table = 'aktivitas_admin';

    protected $fillable = [
        'id_pengguna',
        'jenis_aktivitas',
        'modul',
        'data_id',
        'deskripsi',
        'data_lama',
        'data_baru',
    ];

    protected $casts = [
        'data_lama' => 'array',
        'data_baru' => 'array',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }

    /**
     * Static method untuk mencatat aktivitas
     */
    public static function log(
        int $idPengguna,
        string $jenis,
        string $deskripsi,
        ?string $modul = null,
        ?int $dataId = null,
        ?array $dataLama = null,
        ?array $dataBaru = null
    ): self {
        return static::create([
            'id_pengguna' => $idPengguna,
            'jenis_aktivitas' => $jenis,
            'modul' => $modul,
            'data_id' => $dataId,
            'deskripsi' => $deskripsi,
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
        ]);
    }
}