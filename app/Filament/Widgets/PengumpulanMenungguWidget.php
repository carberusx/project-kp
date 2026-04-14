<?php

namespace App\Filament\Widgets;

use App\Models\PengumpulanTugas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PengumpulanMenungguWidget extends BaseWidget
{
    protected static ?int $sort = 7;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PengumpulanTugas::query()
                    ->where('status', 'dikumpulkan')
                    ->with(['tugas', 'user'])
                    ->latest('dikumpulkan_at')
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Mahasiswa'),
                Tables\Columns\TextColumn::make('tugas.judul')
                    ->label('Tugas')
                    ->limit(35),
                Tables\Columns\TextColumn::make('dikumpulkan_at')
                    ->label('Dikumpulkan')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors(['warning' => 'dikumpulkan'])
                    ->formatStateUsing(fn() => 'Menunggu Review'),
            ])
            ->paginated(false)
            ->heading('Pengumpulan Menunggu Review')
            ->emptyStateHeading('Tidak ada pengumpulan menunggu review')
            ->emptyStateIcon('heroicon-o-inbox');
    }
}
