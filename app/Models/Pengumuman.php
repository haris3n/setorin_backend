<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    public const STATUS = [
        'draft' => 'Draft',
        'published' => 'Dipublikasikan',
        'archived' => 'Diarsipkan',
    ];

    protected $fillable = [
        'id_pengguna', 'judul', 'isi', 'status', 'tanggal_mulai', 'tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_pengguna');
    }

    public function scopeAktif(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->where('status', 'published')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->where(fn (Builder $query) => $query
                ->whereNull('tanggal_selesai')
                ->orWhereDate('tanggal_selesai', '>=', $today));
    }
}
