<?php

namespace App\Filament\Exports;

use App\Models\Pendaftaran;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class PendaftaranExporter extends Exporter
{
    protected static ?string $model = Pendaftaran::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID Pendaftaran'),
            ExportColumn::make('nama_lengkap')->label('Nama Lengkap'),
            ExportColumn::make('nim')->label('NIM/NIS'),
            ExportColumn::make('universitas')->label('Instansi/Universitas'),
            ExportColumn::make('jurusan')->label('Jurusan'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('no_telpon')->label('No WhatsApp'),
            ExportColumn::make('status')->label('Status Pendaftaran'),
            ExportColumn::make('tanggal_mulai')->label('Mulai Magang'),
            ExportColumn::make('tanggal_selesai')->label('Selesai Magang'),
            ExportColumn::make('created_at')->label('Tanggal Daftar'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your pendaftaran export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

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
