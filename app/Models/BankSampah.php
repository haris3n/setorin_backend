<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankSampah extends Model
{
    protected $table = 'bank_sampah';

    protected $fillable = [
        'nama_bank',
        'alamat',
        'no_telepon',
        'email',
        'logo',
        'latitude',
        'longitude',
        'status',
    ];

    // Computed location untuk Map field
    public function getLocationAttribute(): array
    {
        return [
            'lat' => (float) $this->latitude,
            'lng' => (float) $this->longitude,
        ];
    }

    public function setLocationAttribute(?array $value): void
    {
        if (is_array($value)) {
            $this->attributes['latitude'] = $value['lat'] ?? null;
            $this->attributes['longitude'] = $value['lng'] ?? null;
        }
    }

    public function hargaSampah() {
        return $this->hasMany(HargaSampah::class, 'id_bank_sampah');
    }
    public function jadwalOperasional() {
        return $this->hasMany(JadwalOperasional::class, 'id_bank_sampah');
    }
    public function petugas() {
        return $this->hasMany(Petugas::class, 'id_bank_sampah');
    }
    public function nasabah() {
        return $this->hasMany(Nasabah::class, 'id_bank_sampah');
    }
    public function transaksi() {
        return $this->hasMany(TransaksiPenyetoran::class, 'id_bank_sampah');
    }
}
