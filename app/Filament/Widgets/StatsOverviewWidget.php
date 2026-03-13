<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use App\Models\Pendaftaran;
use App\Models\Tugas;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPendaftar   = Pendaftaran::count();
        $menunggu         = Pendaftaran::where('status', 'menunggu')->count();
        $aktifMagang      = User::where('role', 'mahasiswa')->count();
        $hadirHariIni     = Absensi::whereDate('tanggal', today())->where('status', 'hadir')->count();
        $tugasAktif       = Tugas::where('is_aktif', true)->count();

        return [
            Stat::make('Total Pendaftar', $totalPendaftar)
                ->description($menunggu . ' menunggu review')
                ->descriptionIcon('heroicon-m-clock')
                ->color($menunggu > 0 ? 'warning' : 'success')
                ->chart([7, 3, 4, 5, 6, 3, $totalPendaftar]),

            Stat::make('Mahasiswa Aktif', $aktifMagang)
                ->description('Sedang magang')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('Hadir Hari Ini', $hadirHariIni)
                ->description('dari ' . $aktifMagang . ' mahasiswa')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Tugas Aktif', $tugasAktif)
                ->description('Tugas berjalan')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('info'),
        ];
    }
}
