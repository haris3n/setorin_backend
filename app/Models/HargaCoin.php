<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HargaCoin extends Model
{
    /**
     * Nama tabel yang dikaitkan dengan model.
     *
     * @var string
     */
    protected $table = 'harga_coin';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'harga_per_coin',
        'status',
    ];
}
