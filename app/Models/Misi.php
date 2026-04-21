<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property bool $sudah_klaim
 */
class Misi extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'misi';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_misi',
        'deskripsi',
        'reward_koin',
        'tgl_mulai',
        'tgl_selesai',
        'status_misi',
    ];

    // --- Relationships ---

    /**
     * Mendapatkan semua data klaim yang terkait dengan misi ini.
     */
    public function klaim(): HasMany
    {
        return $this->hasMany(KlaimMisi::class, 'id_misi');
    }
}