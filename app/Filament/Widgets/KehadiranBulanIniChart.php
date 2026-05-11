<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Absensi;
use Illuminate\Support\Carbon;

class KehadiranBulanIniChart extends ChartWidget
{
    protected ?string $heading = 'Grafik Kehadiran (Bulan Ini)';

    protected static ?int $sort = 2;
    
    // Opsional: atur lebar widget agar memenuhi seluruh layar (full width) atau setengah layar
    protected int | string | array $columnSpan = 'full';
    
    // Membatasi tinggi grafik agar tidak terlalu panjang (menyesuaikan layar)
    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $daysInMonth = Carbon::now()->daysInMonth;
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $hadirData = [];
        $tidakHadirData = [];
        $labels = [];

        // Ambil data absensi bulan ini sekaligus untuk optimasi query
        $absensiBulanIni = Absensi::whereMonth('tanggal', $currentMonth)
            ->whereYear('tanggal', $currentYear)
            ->get();

        for ($i = 1; $i <= $daysInMonth; $i++) {
            // Kita gunakan format Y-m-d untuk mencocokkan dengan data tanggal dari DB
            // Karena pada Absensi model 'tanggal' dicast sebagai 'date', instance yang dikembalikan adalah Carbon
            // Sehingga bisa difilter dengan lebih mudah
            $dateString = Carbon::createFromDate($currentYear, $currentMonth, $i)->startOfDay();
            $labels[] = $i; // Label di chart cukup menampilkan angka tanggal (1-30/31)

            // Hitung jumlah
            $hadirCount = $absensiBulanIni->filter(function($absen) use ($dateString) {
                return $absen->tanggal->isSameDay($dateString) && $absen->status === 'hadir';
            })->count();

            $tidakHadirCount = $absensiBulanIni->filter(function($absen) use ($dateString) {
                return $absen->tanggal->isSameDay($dateString) && in_array($absen->status, ['izin', 'sakit', 'alpha']);
            })->count();

            $hadirData[] = $hadirCount;
            $tidakHadirData[] = $tidakHadirCount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Hadir',
                    'data' => $hadirData,
                    'borderColor' => '#10b981', // Tailwind emerald-500
                    'backgroundColor' => '#10b981',
                    'fill' => false,
                ],
                [
                    'label' => 'Tidak Hadir (Izin/Sakit/Alpha)',
                    'data' => $tidakHadirData,
                    'borderColor' => '#ef4444', // Tailwind red-500
                    'backgroundColor' => '#ef4444',
                    'fill' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
