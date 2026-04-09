<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasRoles, Notifiable;

    protected $table = 'pengguna';

    protected $fillable = [
        'nama',
        'email',
        'no_telepon',
        'password',
        'alamat',
        'role',
        'status_akun',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Logika Akses Panel Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Cek status akun harus aktif
        if ($this->status_akun !== 'aktif') {
            return false;
        }

        if ($panel->getId() === 'admin') {
            return $this->role === 'admin';
        }

        if ($panel->getId() === 'petugas') {
            return $this->role === 'petugas';
        }

        // Tambahkan jika ada panel nasabah nantinya
        if ($panel->getId() === 'nasabah') {
            return $this->role === 'nasabah';
        }

        return false;
    }

    // --- Accessors & Mutators (Solusi untuk Kolom 'nama') ---

    public function getNameAttribute()
    {
        return $this->nama;
    }

    public function setNameAttribute($value)
    {
        $this->attributes['nama'] = $value;
    }

    public function getTotalKoinAttribute(): float|int
    {
        return $this->koin()->sum('jumlah_koin');
    }

    // --- Relationships ---

    public function nasabah(): HasOne
    {
        return $this->hasOne(Nasabah::class, 'id_pengguna');
    }

    public function petugas(): HasOne
    {
        return $this->hasOne(Petugas::class, 'id_pengguna');
    }

    public function saldo(): HasOne
    {
        return $this->hasOne(Saldo::class, 'id_pengguna');
    }

    public function notifikasi(): HasMany
    {
        return $this->hasMany(Notifikasi::class, 'id_pengguna');
    }

    public function koin(): HasMany
    {
        return $this->hasMany(Koin::class, 'id_pengguna');
    }

    public function otpVerifikasi(): HasMany
    {
        return $this->hasMany(OtpVerifikasi::class, 'id_pengguna');
    }

    public function klaimMisi(): HasMany
    {
        return $this->hasMany(KlaimMisi::class, 'id_pengguna');
    }

    public function penarikan(): HasMany
    {
        return $this->hasMany(PenarikanSaldo::class, 'id_pengguna');
    }

    public function kontenEdukasi(): HasMany
    {
        return $this->hasMany(KontenEdukasi::class, 'id_pengguna');
    }
}