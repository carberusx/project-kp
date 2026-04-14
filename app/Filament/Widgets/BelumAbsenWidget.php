<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class BelumAbsenWidget extends BaseWidget
{
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        // ID mahasiswa yang sudah absen hari ini
        $sudahAbsen = Absensi::whereDate('tanggal', today())
            ->pluck('user_id')
            ->toArray();

        return $table
            ->query(
                User::query()
                    ->where('role', 'mahasiswa')
                    ->whereNotIn('id', $sudahAbsen)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama'),
                Tables\Columns\TextColumn::make('nim')
                    ->label('NIM'),
                Tables\Columns\TextColumn::make('universitas')
                    ->label('Universitas'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->toggleable(),
            ])
            ->paginated(false)
            ->heading('Mahasiswa Belum Absen Hari Ini')
            ->emptyStateHeading('Semua mahasiswa sudah absen!')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
