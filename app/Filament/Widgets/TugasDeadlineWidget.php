<?php

namespace App\Filament\Widgets;

use App\Models\Tugas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TugasDeadlineWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Tugas::query()
                    ->where('is_aktif', true)
                    ->where('deadline', '>=', now())
                    ->where('deadline', '<=', now()->addDays(3))
                    ->orderBy('deadline')
            )
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul Tugas')
                    ->limit(40),
                Tables\Columns\BadgeColumn::make('tipe')
                    ->label('Tipe')
                    ->colors([
                        'primary' => 'laporan',
                        'success' => 'proyek',
                        'warning' => 'evaluasi',
                        'gray'    => 'lainnya',
                    ]),
                Tables\Columns\TextColumn::make('deadline')
                    ->label('Deadline')
                    ->dateTime('d M Y, H:i')
                    ->color('danger'),
                Tables\Columns\TextColumn::make('days_left')
                    ->label('Sisa')
                    ->getStateUsing(function (Tugas $record) {
                        $hours = (int) now()->diffInHours($record->deadline, false);
                        if ($hours < 24) return $hours . ' jam lagi';
                        return ceil($hours / 24) . ' hari lagi';
                    })
                    ->badge()
                    ->color('danger'),
            ])
            ->paginated(false)
            ->heading('Tugas Mendekati Deadline (3 Hari)')
            ->emptyStateHeading('Tidak ada tugas mendekati deadline')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
