<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TugasResource\Pages;
use App\Models\Tugas;
use Filament\Forms;
use Filament\Schemas\Schema; // <-- Update v5
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions; // <-- Untuk Action v5

class TugasResource extends Resource
{
    protected static ?string $model = Tugas::class;

    // --- Perubahan 1: Navigation Property ke Method ---
    public static function getNavigationIcon(): string | null
    {
        return 'heroicon-o-clipboard-document-list';
    }

    public static function getNavigationLabel(): string
    {
        return 'Tugas';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Monitoring';
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getModelLabel(): string
    {
        return 'Tugas';
    }

    // --- Perubahan 2: Parameter Form Menjadi Schema ---
    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('judul')
                ->label('Judul Tugas')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Forms\Components\Textarea::make('deskripsi')
                ->label('Deskripsi / Instruksi')
                ->rows(4)
                ->columnSpanFull(),
            Forms\Components\Select::make('tipe')
                ->label('Tipe Tugas')
                ->options([
                    'laporan'  => 'Laporan',
                    'proyek'   => 'Proyek',
                    'evaluasi' => 'Evaluasi',
                    'lainnya'  => 'Lainnya',
                ])
                ->required(),
            Forms\Components\DateTimePicker::make('deadline')
                ->label('Deadline')
                ->required(),
            Forms\Components\Toggle::make('is_aktif')
                ->label('Aktif')
                ->default(true),
            Forms\Components\FileUpload::make('file_tugas')
                ->label('File Lampiran untuk Mahasiswa')
                ->disk('public')
                ->directory('tugas/lampiran')
                ->acceptedFileTypes([
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/zip',
                ])
                ->maxSize(10240)
                ->downloadable()
                ->helperText('Upload file instruksi/template untuk mahasiswa (PDF, DOCX, ZIP maks 10MB)')
                ->columnSpanFull(),
            Forms\Components\Select::make('mahasiswas')
                ->label('Assign ke Mahasiswa')
                ->relationship('mahasiswas', 'name', fn ($query) => $query->where('role', 'mahasiswa'))
                ->multiple()
                ->preload()
                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - {$record->jurusan} - ({$record->universitas})")
                ->searchable()
                ->helperText('Pilih mahasiswa yang akan mendapat tugas ini')
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                // --- Perubahan 3: BadgeColumn ke TextColumn badge() ---
                Tables\Columns\TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'laporan'  => 'primary',
                        'proyek'   => 'success',
                        'evaluasi' => 'warning',
                        'lainnya'  => 'gray',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('deadline')
                    ->label('Deadline')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pengumpulans_count')
                    ->label('Dikumpulkan')
                    ->counts('pengumpulans')
                    ->suffix(' mahasiswa'),
                Tables\Columns\IconColumn::make('file_tugas')
                    ->label('Ada Lampiran')
                    ->boolean()
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-x-mark'),
                Tables\Columns\IconColumn::make('is_aktif')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('deadline', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTugas::route('/'),
            'create' => Pages\CreateTugas::route('/create'),
            'edit'   => Pages\EditTugas::route('/{record}/edit'),
        ];
    }
}