<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'judul',
        'deskripsi',
        'deadline',
        'tipe',       // 'laporan' | 'proyek' | 'evaluasi' | 'lainnya'
        'file_tugas', // file template dari admin (opsional)
        'is_aktif',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'is_aktif' => 'boolean',
    ];

    // ── Relasi ─────────────────────────────────────────────────────────────
    public function pengumpulans()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }

    // ── Helper ─────────────────────────────────────────────────────────────
    public function isOverdue(): bool
    {
        return $this->deadline->isPast();
    }

    public function getDaysLeftAttribute(): int
    {
        return max(0, now()->diffInDays($this->deadline, false));
    }

    public function pengumpulanByUser(int $userId): ?PengumpulanTugas
    {
        return $this->pengumpulans()->where('user_id', $userId)->first();
    }
}
