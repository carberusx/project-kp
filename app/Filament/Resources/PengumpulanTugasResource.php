<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengumpulanTugasResource\Pages;
use App\Models\PengumpulanTugas;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Actions\Action; // <-- Gunakan ini untuk semua tombol custom
use Filament\Actions\EditAction; // <-- Gunakan ini untuk Edit

class PengumpulanTugasResource extends Resource
{
    protected static ?string $model = PengumpulanTugas::class;

    public static function getNavigationIcon(): string | null
    {
        return 'heroicon-o-clipboard-document-check';
    }

    public static function getNavigationLabel(): string
    {
        return 'Penilaian Tugas';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Monitoring';
    }

    public static function getNavigationSort(): ?int
    {
        return 4;
    }

    public static function getModelLabel(): string
    {
        return 'Pengumpulan Tugas';
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'dikumpulkan')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Detail Pengumpulan')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('Mahasiswa')
                        ->relationship('user', 'name')
                        ->disabled(),
                    Forms\Components\Select::make('tugas_id')
                        ->label('Tugas')
                        ->relationship('tugas', 'judul')
                        ->disabled(),
                    Forms\Components\DateTimePicker::make('dikumpulkan_at')
                        ->label('Waktu Pengumpulan')
                        ->disabled(),
                    Forms\Components\Placeholder::make('status_waktu')
                        ->label('Status Waktu')
                        ->content(fn ($record) => $record && $record->isLate() 
                            ? new \Illuminate\Support\HtmlString('<span style="color: red; font-weight: bold;">Terlambat ' . $record->terlambat_text . '</span>') 
                            : new \Illuminate\Support\HtmlString('<span style="color: green; font-weight: bold;">Tepat Waktu</span>')),
                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan Mahasiswa')
                        ->disabled()
                        ->rows(3),
                    Forms\Components\Placeholder::make('file_download')
                        ->label('File Tugas')
                        ->content(fn ($record) => $record && $record->file_path 
                            ? new \Illuminate\Support\HtmlString('
                                <a href="' . asset('storage/' . $record->file_path) . '" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background-color: #10b981; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; text-decoration: none; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                                    <svg style="width: 1.25rem; height: 1.25rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Download File Mahasiswa
                                </a>')
                            : 'Tidak ada file.')
                        ->columnSpanFull(),
                ])->columns(2),

            Section::make('Penilaian Admin')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'dikumpulkan' => 'Dikumpulkan',
                            'dinilai'     => 'Dinilai',
                            'revisi'      => 'Perlu Revisi',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('nilai')
                        ->label('Nilai (0-100)')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(100)
                        ->step(0.5),
                    Forms\Components\Textarea::make('feedback')
                        ->label('Feedback / Komentar')
                        ->rows(4)
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tugas.judul')
                    ->label('Tugas')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('dikumpulkan_at')
                    ->label('Dikumpulkan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->description(fn (PengumpulanTugas $record): string => $record->isLate() ? 'Terlambat ' . $record->terlambat_text : 'Tepat Waktu')
                    ->color(fn (PengumpulanTugas $record): string => $record->isLate() ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('nilai')
                    ->label('Nilai')
                    ->default('—')
                    ->badge()
                    ->color(fn($state) => match(true) {
                        $state >= 85 => 'success',
                        $state >= 70 => 'warning',
                        $state > 0   => 'danger',
                        default      => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dikumpulkan' => 'info',
                        'dinilai'     => 'success',
                        'revisi'      => 'warning',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn($state) => match($state) {
                        'dikumpulkan' => 'Dikumpulkan',
                        'dinilai'     => 'Dinilai',
                        'revisi'      => 'Perlu Revisi',
                        default       => $state,
                    }),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'dikumpulkan' => 'Dikumpulkan',
                        'dinilai'     => 'Dinilai',
                        'revisi'      => 'Perlu Revisi',
                    ]),
                Tables\Filters\SelectFilter::make('tugas_id')
                    ->label('Filter Tugas')
                    ->relationship('tugas', 'judul'),
            ])
            ->actions([
                // PENTING: Di v5, Action::make() sudah universal
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (PengumpulanTugas $record) {
                        return response()->download(
                            storage_path('app/public/' . $record->file_path),
                            basename($record->file_path)
                        );
                    })
                    ->visible(fn(PengumpulanTugas $record) => $record->file_path !== null),

                Action::make('nilai')
                    ->label('Beri Nilai')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'dinilai' => 'Dinilai',
                                'revisi'  => 'Perlu Revisi',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('nilai')
                            ->label('Nilai (0-100)')
                            ->numeric()
                            ->required(),
                        Forms\Components\Textarea::make('feedback')
                            ->label('Feedback')
                            ->rows(3),
                    ])
                    ->action(function (PengumpulanTugas $record, array $data) {
                        $record->update([
                            'status'   => $data['status'],
                            'nilai'    => $data['nilai'],
                            'feedback' => $data['feedback'],
                        ]);
                        Notification::make()->title('Tugas dinilai')->success()->send();
                    })
                    ->visible(fn(PengumpulanTugas $record) => $record->status === 'dikumpulkan'),

                EditAction::make()->label('Edit'),
            ])
            ->defaultSort('dikumpulkan_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPengumpulanTugas::route('/'),
            'edit'   => Pages\EditPengumpulanTugas::route('/{record}/edit'),
        ];
    }
}