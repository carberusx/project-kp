<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'keterangan',
        'tipe',
    ];

    protected $casts = [
        'tanggal' => 'date:Y-m-d',
    ];

    protected static function booted()
    {
        // Hapus semua absensi saat hari libur disimpan (create atau update)
        static::saved(function ($hariLibur) {
            if ($hariLibur->tipe === 'libur') {
                $tanggal = $hariLibur->tanggal instanceof \Carbon\Carbon
                    ? $hariLibur->tanggal->toDateString()
                    : (string) $hariLibur->tanggal;

                \App\Models\Absensi::whereDate('tanggal', $tanggal)->delete();
            }
        });
    }
}
