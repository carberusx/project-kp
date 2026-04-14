<?php

namespace App\Filament\Widgets;

use App\Models\Pendaftaran;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendaftarTerbaruWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Pendaftaran::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('universitas')
                    ->label('Universitas'),
                Tables\Columns\TextColumn::make('jurusan')
                    ->label('Jurusan'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'menunggu',
                        'success' => 'diterima',
                        'danger'  => 'ditolak',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'menunggu' => 'Menunggu',
                        'diterima' => 'Diterima',
                        'ditolak'  => 'Ditolak',
                        default    => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->paginated(false)
            ->heading('Pendaftar Terbaru');
    }
}
