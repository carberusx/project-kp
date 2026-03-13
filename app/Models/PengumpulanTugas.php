<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumpulanTugas extends Model
{
    use HasFactory;

    protected $table = 'pengumpulan_tugas';

    protected $fillable = [
        'tugas_id',
        'user_id',
        'file_path',
        'catatan',
        'status',       // 'dikumpulkan' | 'dinilai' | 'revisi'
        'nilai',
        'feedback',
        'dikumpulkan_at',
    ];

    protected $casts = [
        'dikumpulkan_at' => 'datetime',
    ];

    // ── Relasi ─────────────────────────────────────────────────────────────
    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helper ─────────────────────────────────────────────────────────────
    public function isLate(): bool
    {
        return $this->dikumpulkan_at && $this->tugas->deadline < $this->dikumpulkan_at;
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'dinilai'     => 'success',
            'revisi'      => 'warning',
            default       => 'info',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'dikumpulkan' => 'Dikumpulkan',
            'dinilai'     => 'Dinilai',
            'revisi'      => 'Perlu Revisi',
            default       => '-',
        };
    }
}
