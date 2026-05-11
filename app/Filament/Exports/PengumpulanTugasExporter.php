<?php

namespace App\Filament\Exports;

use App\Models\PengumpulanTugas;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class PengumpulanTugasExporter extends Exporter
{
    protected static ?string $model = PengumpulanTugas::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID Pengumpulan'),
            ExportColumn::make('tugas.judul')->label('Judul Tugas'),
            ExportColumn::make('user.name')->label('Nama Mahasiswa'),
            ExportColumn::make('user.nim')->label('NIM'),
            ExportColumn::make('status')->label('Status Tugas'),
            ExportColumn::make('dikumpulkan_at')->label('Waktu Kumpul'),
            ExportColumn::make('nilai')->label('Nilai Akhir'),
            ExportColumn::make('feedback')->label('Feedback Pembimbing'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your pengumpulan tugas export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

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
