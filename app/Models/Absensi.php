<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensis';

    protected $fillable = [
        'user_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status',  // 'hadir' | 'izin' | 'sakit' | 'alpha'
        'keterangan',
        'lokasi_masuk',
        'lokasi_keluar',
        'foto_masuk',
        'foto_keluar',
        'latitude_masuk',
        'longitude_masuk',
        'latitude_keluar',
        'longitude_keluar',
        'alamat_masuk',
        'alamat_keluar',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'jam_masuk'  => 'datetime:H:i',
        'jam_keluar' => 'datetime:H:i',
    ];

    // ── Relasi ─────────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scope ──────────────────────────────────────────────────────────────
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal', now()->month)
                     ->whereYear('tanggal', now()->year);
    }

    // ── Helper ─────────────────────────────────────────────────────────────
    public function getDurasiAttribute(): ?string
    {
        if ($this->jam_masuk && $this->jam_keluar) {
            $masuk  = \Carbon\Carbon::parse($this->jam_masuk);
            $keluar = \Carbon\Carbon::parse($this->jam_keluar);
            $diff   = $masuk->diff($keluar);
            return $diff->format('%H jam %I menit');
        }
        return null;
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'hadir'  => 'success',
            'izin'   => 'warning',
            'sakit'  => 'info',
            default  => 'danger',
        };
    }
}
