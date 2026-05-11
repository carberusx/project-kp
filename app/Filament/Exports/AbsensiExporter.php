<?php

namespace App\Filament\Exports;

use App\Models\Absensi;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class AbsensiExporter extends Exporter
{
    protected static ?string $model = Absensi::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID Absensi'),
            ExportColumn::make('user.name')->label('Nama Mahasiswa'),
            ExportColumn::make('user.nim')->label('NIM'),
            ExportColumn::make('tanggal')->label('Tanggal Absensi'),
            ExportColumn::make('status')->label('Status Kehadiran'),
            ExportColumn::make('jam_masuk')
                ->label('Jam Masuk')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('H:i') : null),
            ExportColumn::make('jam_keluar')
                ->label('Jam Keluar')
                ->getStateUsing(function ($record) {
                    if ($record->jam_keluar) return \Carbon\Carbon::parse($record->jam_keluar)->format('H:i');
                    if (\Carbon\Carbon::parse($record->tanggal)->isToday()) return 'Belum Waktunya';
                    return 'Tidak Absen Keluar';
                }),
            ExportColumn::make('keterangan')->label('Keterangan'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your absensi export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }
}
