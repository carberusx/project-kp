<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengumpulanTugasResource\Pages;
use App\Models\PengumpulanTugas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PengumpulanTugasResource extends Resource
{
    protected static ?string $model = PengumpulanTugas::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Penilaian Tugas';
    protected static ?string $navigationGroup = 'Monitoring';
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel      = 'Pengumpulan Tugas';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'dikumpulkan')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Detail Pengumpulan')
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
                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan Mahasiswa')
                        ->disabled()
                        ->rows(3),
                ])->columns(2),

            Forms\Components\Section::make('Penilaian Admin')
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
                    ->sortable(),
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
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'info'    => 'dikumpulkan',
                        'success' => 'dinilai',
                        'warning' => 'revisi',
                    ])
                    ->formatStateUsing(fn($state) => match($state) {
                        'dikumpulkan' => 'Dikumpulkan',
                        'dinilai'     => 'Dinilai',
                        'revisi'      => 'Perlu Revisi',
                        default       => $state,
                    }),
                Tables\Columns\TextColumn::make('file_path')
   			 ->label('File')
   			 ->formatStateUsing(fn($state) => $state ? basename($state) : '—')
   			 ->url(fn($record) => $record->file_path ? asset('storage/' . $record->file_path) : null)
   			 ->openUrlInNewTab()
   			 ->color('primary'),
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
		Tables\Actions\Action::make('download')
   		 ->label('Download')
   		 ->icon('heroicon-o-arrow-down-tray')
   		 ->color('success')
   		 ->url(fn(PengumpulanTugas $record) => asset('storage/' . $record->file_path))
  		 ->openUrlInNewTab()
   		 ->visible(fn(PengumpulanTugas $record) => $record->file_path !== null),
                Tables\Actions\Action::make('nilai')
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
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.5)
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
                    })
                    ->visible(fn(PengumpulanTugas $record) => $record->status === 'dikumpulkan'),
                Tables\Actions\EditAction::make()->label('Edit'),
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
