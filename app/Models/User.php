<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // 'super_admin' | 'admin' | 'mahasiswa'
        'nim',
        'universitas',
        'jurusan',
        'telepon',
        'foto',
        'force_password_change',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'     => 'datetime',
        'password'              => 'hashed',
        'force_password_change' => 'boolean',
    ];

    // ── Filament: admin & super_admin bisa akses panel ──────────────────
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    // ── Relasi ─────────────────────────────────────────────────────────────
    public function pendaftaran()
    {
        return $this->hasOne(Pendaftaran::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function pengumpulanTugas()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }

    public function tugas()
    {
        return $this->belongsToMany(Tugas::class, 'tugas_mahasiswa');
    }

    // ── Helper ─────────────────────────────────────────────────────────────
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAdminOrSuperAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function absensiHariIni()
    {
        return $this->absensis()->whereDate('tanggal', today())->first();
    }

    public function totalHadirBulanIni(): int
    {
        return $this->absensis()
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->where('status', 'hadir')
            ->count();
    }
}
