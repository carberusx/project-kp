<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pendaftarans';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'email',
        'nim',
        'no_telpon',
        'universitas',
        'jurusan',
        'motivasi',
        'file_cv',
        'file_transkrip',
        'status', // 'menunggu' | 'diterima' | 'ditolak' | 'wawancara'
        'catatan_admin',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_mulai'  => 'date',
        'tanggal_selesai' => 'date',
    ];

    // ── Boot: auto-sync ke user & cascade delete ───────────────────────
    protected static function booted(): void
    {
        // Sinkronkan NIM, telepon, universitas, jurusan ke akun user terkait
        static::saved(function (Pendaftaran $pendaftaran) {
            if ($pendaftaran->user_id && $pendaftaran->user) {
                $pendaftaran->user->update([
                    'nim'         => $pendaftaran->nim,
                    'universitas' => $pendaftaran->universitas,
                    'jurusan'     => $pendaftaran->jurusan,
                    'telepon'     => $pendaftaran->no_telpon,
                ]);
            }
        });

        // Hapus akun mahasiswa yang terkait jika ada
        static::deleting(function (Pendaftaran $pendaftaran) {
            if ($pendaftaran->user_id && $pendaftaran->user) {
                $pendaftaran->user->delete();
            }
        });
    }

    // ── Relasi ─────────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Accessor ───────────────────────────────────────────────────────────
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'diterima'    => 'success',
            'ditolak'     => 'danger',
            'wawancara'   => 'warning',
            default       => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'diterima'    => 'Diterima',
            'ditolak'     => 'Ditolak',
            'wawancara'   => 'Wawancara',
            default       => 'Menunggu',
        };
    }
}
