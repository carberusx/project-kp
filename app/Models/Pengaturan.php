<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $fillable = ['kunci', 'nilai'];

    /**
     * Get a setting value by key.
     */
    public static function getNilai($kunci, $default = null)
    {
        $setting = self::where('kunci', $kunci)->first();
        return $setting ? $setting->nilai : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function setNilai($kunci, $nilai)
    {
        return self::updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => $nilai]
        );
    }
}
