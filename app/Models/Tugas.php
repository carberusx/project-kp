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
        'tipe',
        'file_tugas',
        'is_aktif',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'is_aktif' => 'boolean',
    ];

    // Relasi ke mahasiswa yang ditugaskan
    public function mahasiswas()
    {
        return $this->belongsToMany(User::class, 'tugas_mahasiswa');
    }

    public function pengumpulans()
    {
        return $this->hasMany(PengumpulanTugas::class);
    }

    public function isOverdue(): bool
    {
        return $this->deadline->isPast();
    }

    public function getDaysLeftAttribute(): int
    {
        return max(0, now()->diffInDays($this->deadline, false));
    }

    public function getTerlambatTextAttribute(): string
    {
        if (!$this->isOverdue()) {
            return '';
        }
        
        $diff = now()->diff($this->deadline);
        $parts = [];
        if ($diff->days > 0) {
            $parts[] = $diff->days . ' hari';
        }
        if ($diff->h > 0) {
            $parts[] = $diff->h . ' jam';
        }
        if ($diff->days == 0 && $diff->h == 0 && $diff->i > 0) {
            $parts[] = $diff->i . ' menit';
        }
        
        if (empty($parts)) {
            return 'beberapa detik';
        }
        
        return implode(' ', $parts);
    }

    public function pengumpulanByUser(int $userId): ?PengumpulanTugas
    {
        return $this->pengumpulans()->where('user_id', $userId)->first();
    }
}
